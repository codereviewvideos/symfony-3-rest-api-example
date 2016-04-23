<?php

namespace AppBundle\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Post\PostFile;
use PHPUnit_Framework_Assert as Assertions;
use Sanpi\Behatch\Json\JsonInspector;
use Sanpi\Behatch\Json\JsonSchema;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RestApiContext
 * @package AppBundle\Features\Context
 */
class RestApiContext implements Context
{
    /**
     * @var string
     */
    private $authorization;

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var array
     */
    private $headers = array();

    /**
     * @var \GuzzleHttp\Message\RequestInterface
     */
    private $request;

    /**
     * @var \GuzzleHttp\Message\ResponseInterface
     */
    private $response;

    /**
     * @var array
     */
    private $placeHolders = array();
    /**
     * @var string
     */
    private $dummyDataPath;

    /**
     * RestApiContext constructor.
     * @param ClientInterface   $client
     * @param string            $dummyDataPath
     */
    public function __construct(ClientInterface $client, $dummyDataPath = null)
    {
        $this->client = $client;
        $this->dummyDataPath = $dummyDataPath;
    }

    /**
     * Adds Basic Authentication header to next request.
     *
     * @param string $username
     * @param string $password
     *
     * @Given /^I am authenticating as "([^"]*)" with "([^"]*)" password$/
     */
    public function iAmAuthenticatingAs($username, $password)
    {
        $this->removeHeader('Authorization');
        $this->authorization = base64_encode($username . ':' . $password);
        $this->addHeader('Authorization', 'Basic ' . $this->authorization);
    }

    /**
     * Adds JWT Token to Authentication header for next request
     *
     * @param string $username
     * @param string $password
     *
     * @Given /^I am successfully logged in with username: "([^"]*)", and password: "([^"]*)"$/
     */
    public function iAmSuccessfullyLoggedInWithUsernameAndPassword($username, $password)
    {
        $response = $this->client->post('login', [
            'json' => [
                'username' => $username,
                'password' => $password,
            ]
        ]);

        \PHPUnit_Framework_Assert::assertEquals(200, $response->getStatusCode());

        $responseBody = json_decode($response->getBody(), true);
        $this->addHeader('Authorization', 'Bearer ' . $responseBody['token']);
    }

    /**
     * @Given when consuming the endpoint I use the :header of :value
     */
    public function whenConsumingTheEndpointIUseTheOf($header, $value)
    {
        $this->client->setDefaultOption($header, $value);
    }

    /**
     * @When I have forgotten to set the :header
     */
    public function iHaveForgottenToSetThe($header)
    {
        $this->client->setDefaultOption($header, null);
    }

    /**
     * Sets a HTTP Header.
     *
     * @param string $name  header name
     * @param string $value header value
     *
     * @Given /^I set header "([^"]*)" with value "([^"]*)"$/
     */
    public function iSetHeaderWithValue($name, $value)
    {
        $this->addHeader($name, $value);
    }

    /**
     * Sends HTTP request to specific relative URL.
     *
     * @param string $method request method
     * @param string $url    relative url
     *
     * @When /^(?:I )?send a "([A-Z]+)" request to "([^"]+)"$/
     */
    public function iSendARequest($method, $url)
    {
        $url = $this->prepareUrl($url);
        $this->request = $this->getClient()->createRequest($method, $url);
        if (!empty($this->headers)) {
            $this->request->addHeaders($this->headers);
        }

        $this->sendRequest();
    }

    /**
     * Sends HTTP request to specific URL with field values from Table.
     *
     * @param string    $method request method
     * @param string    $url    relative url
     * @param TableNode $post   table of post values
     *
     * @When /^(?:I )?send a ([A-Z]+) request to "([^"]+)" with values:$/
     */
    public function iSendARequestWithValues($method, $url, TableNode $post)
    {
        $url = $this->prepareUrl($url);
        $fields = array();

        foreach ($post->getRowsHash() as $key => $val) {
            $fields[$key] = $this->replacePlaceHolder($val);
        }

        $bodyOption = array(
            'body' => json_encode($fields),
        );
        $this->request = $this->getClient()->createRequest($method, $url, $bodyOption);
        if (!empty($this->headers)) {
            $this->request->addHeaders($this->headers);
        }

        $this->sendRequest();
    }

    /**
     * Sends HTTP request to specific URL with raw body from PyString.
     *
     * @param string       $method request method
     * @param string       $url    relative url
     * @param PyStringNode $string request body
     *
     * @When /^(?:I )?send a "([A-Z]+)" request to "([^"]+)" with body:$/
     */
    public function iSendARequestWithBody($method, $url, PyStringNode $string)
    {
        $url = $this->prepareUrl($url);
        $string = $this->replacePlaceHolder(trim($string));

        $this->request = $this->getClient()->createRequest(
            $method,
            $url,
            array(
                'headers' => $this->getHeaders(),
                'body' => $string,
            )
        );

        $this->sendRequest();
    }

    /**
     * Sends HTTP request to specific URL with form data from PyString.
     *
     * @param string       $method request method
     * @param string       $url    relative url
     * @param PyStringNode $body   request body
     *
     * @When /^(?:I )?send a "([A-Z]+)" request to "([^"]+)" with form data:$/
     */
    public function iSendARequestWithFormData($method, $url, PyStringNode $body)
    {
        $url = $this->prepareUrl($url);
        $body = $this->replacePlaceHolder(trim($body));

        $fields = array();
        parse_str(implode('&', explode("\n", $body)), $fields);
        $this->request = $this->getClient()->createRequest($method, $url);
        /** @var \GuzzleHttp\Post\PostBodyInterface $requestBody */
        $requestBody = $this->request->getBody();
        foreach ($fields as $key => $value) {
            $requestBody->setField($key, $value);
        }

        $this->sendRequest();
    }

    /**
     * @When /^(?:I )?send a multipart "([A-Z]+)" request to "([^"]+)" with form data:$/
     */
    public function iSendAMultipartRequestToWithFormData($method, $url, TableNode $post)
    {
        $url = $this->prepareUrl($url);

        $this->request = $this->getClient()->createRequest($method, $url);

        $data = $post->getColumnsHash()[0];

        $hasFile = false;

        if (array_key_exists('filePath', $data)) {
            $filePath = $this->dummyDataPath . $data['filePath'];
            unset($data['filePath']);
            $hasFile = true;
        }


        /** @var \GuzzleHttp\Post\PostBodyInterface $requestBody */
        $requestBody = $this->request->getBody();
        foreach ($data as $key => $value) {
            $requestBody->setField($key, $value);
        }


        if ($hasFile) {
            $file = fopen($filePath, 'rb');
            $postFile = new PostFile('uploadedFile', $file);
            $requestBody->addFile($postFile);
        }


        if (!empty($this->headers)) {
            $this->request->addHeaders($this->headers);
        }
        $this->request->setHeader('Content-Type', 'multipart/form-data');

        $this->sendRequest();
    }

    /**
     * Checks that response has specific status code.
     *
     * @param string $code status code
     *
     * @Then the response code should be :arg1
     */
    public function theResponseCodeShouldBe($code)
    {
        $expected = intval($code);
        $actual = intval($this->response->getStatusCode());
        Assertions::assertSame($expected, $actual);
    }

    /**
     * Checks that response body contains specific text.
     *
     * @param string $text
     *
     * @Then /^(?:the )?response should contain "([^"]*)"$/
     */
    public function theResponseShouldContain($text)
    {
        $expectedRegexp = '/' . preg_quote($text) . '/i';
        $actual = (string) $this->response->getBody();
        Assertions::assertRegExp($expectedRegexp, $actual);
    }

    /**
     * Checks that response body doesn't contains specific text.
     *
     * @param string $text
     *
     * @Then /^(?:the )?response should not contain "([^"]*)"$/
     */
    public function theResponseShouldNotContain($text)
    {
        $expectedRegexp = '/' . preg_quote($text) . '/';
        $actual = (string) $this->response->getBody();
        Assertions::assertNotRegExp($expectedRegexp, $actual);
    }

    /**
     * Checks that response body contains JSON from PyString.
     *
     * Do not check that the response body /only/ contains the JSON from PyString,
     *
     * @param PyStringNode $jsonString
     *
     * @throws \RuntimeException
     *
     * @Then /^(?:the )?response should contain json:$/
     */
    public function theResponseShouldContainJson(PyStringNode $jsonString)
    {
        $etalon = json_decode($this->replacePlaceHolder($jsonString->getRaw()), true);
        $actual = $this->response->json();

        if (null === $etalon) {
            throw new \RuntimeException(
                "Can not convert etalon to json:\n" . $this->replacePlaceHolder($jsonString->getRaw())
            );
        }

        Assertions::assertGreaterThanOrEqual(count($etalon), count($actual));
        foreach ($etalon as $key => $needle) {
            Assertions::assertArrayHasKey($key, $actual);
            Assertions::assertEquals($etalon[$key], $actual[$key]);
        }
    }

    /**
     * Prints last response body.
     *
     * @Then print response
     */
    public function printResponse()
    {
        $request = $this->request;
        $response = $this->response;

        echo sprintf(
            "%s %s => %d:\n%s",
            $request->getMethod(),
            $request->getUrl(),
            $response->getStatusCode(),
            $response->getBody()
        );
    }

    /**
     * @Then the response header :header should be equal to :value
     */
    public function theResponseHeaderShouldBeEqualTo($header, $value)
    {
        $header = $this->response->getHeaders()[$header];
        Assertions::assertContains($value, $header);
    }

    /**
     * Prepare URL by replacing placeholders and trimming slashes.
     *
     * @param string $url
     *
     * @return string
     */
    private function prepareUrl($url)
    {
        return ltrim($this->replacePlaceHolder($url), '/');
    }

    /**
     * Sets place holder for replacement.
     *
     * you can specify placeholders, which will
     * be replaced in URL, request or response body.
     *
     * @param string $key   token name
     * @param string $value replace value
     */
    public function setPlaceHolder($key, $value)
    {
        $this->placeHolders[$key] = $value;
    }

    /**
     * @Then the I follow the link in the Location response header
     */
    public function theIFollowTheLinkInTheLocationResponseHeader()
    {
        $location = $this->response->getHeader('Location');

        $this->iSendARequest(Request::METHOD_GET, $location);
    }

    /**
     * @Then the JSON should be valid according to this schema:
     */
    public function theJsonShouldBeValidAccordingToThisSchema(PyStringNode $schema)
    {
        $inspector = new JsonInspector('javascript');

        $json = new \Sanpi\Behatch\Json\Json(json_encode($this->response->json()));

        $inspector->validate(
            $json,
            new JsonSchema($schema)
        );
    }

    /**
     * Checks, that given JSON node is equal to given value
     *
     * @Then the JSON node :node should be equal to :text
     */
    public function theJsonNodeShouldBeEqualTo($node, $text)
    {
        $json = new \Sanpi\Behatch\Json\Json(json_encode($this->response->json()));

        $inspector = new JsonInspector('javascript');

        $actual = $inspector->evaluate($json, $node);

        if ($actual != $text) {
            throw new \Exception(
                sprintf("The node value is '%s'", json_encode($actual))
            );
        }
    }

    /**
     * Replaces placeholders in provided text.
     *
     * @param string $string
     *
     * @return string
     */
    protected function replacePlaceHolder($string)
    {
        foreach ($this->placeHolders as $key => $val) {
            $string = str_replace($key, $val, $string);
        }

        return $string;
    }

    /**
     * Returns headers, that will be used to send requests.
     *
     * @return array
     */
    protected function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Adds header
     *
     * @param string $name
     * @param string $value
     */
    protected function addHeader($name, $value)
    {
        if (isset($this->headers[$name])) {
            if (!is_array($this->headers[$name])) {
                $this->headers[$name] = array($this->headers[$name]);
            }

            $this->headers[$name][] = $value;
        } else {
            $this->headers[$name] = $value;
        }
    }

    /**
     * Removes a header identified by $headerName
     *
     * @param string $headerName
     */
    protected function removeHeader($headerName)
    {
        if (array_key_exists($headerName, $this->headers)) {
            unset($this->headers[$headerName]);
        }
    }

    /**
     *
     */
    private function sendRequest()
    {
        try {
            $this->response = $this->getClient()->send($this->request);
        } catch (RequestException $e) {
            $this->response = $e->getResponse();

            if (null === $this->response) {
                throw $e;
            }
        }
    }

    /**
     * @return ClientInterface
     */
    private function getClient()
    {
        if (null === $this->client) {
            throw new \RuntimeException('Client has not been set in WebApiContext');
        }

        return $this->client;
    }
}

<?php

namespace AppBundle\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;
use PHPUnit_Framework_Assert as Assertions;
use Symfony\Component\HttpFoundation\Request;

/**
 * @TODO - fix all the errors etc - quick fix for api doc branch
 *
 * Class RestApiContext
 * @package AppBundle\Features\Context
 */
class RestApiContext implements Context
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var string
     */
    private $authorization;

    /**
     * @var array
     */
    private $headers = [];

    private $request;

    private $response;

    /**
     * @var array
     */
    private $placeHolders = array();
    /**
     * @var
     */
    private $dummyDataPath;

    /**
     * RestApiContext constructor.
     *
     * @param ClientInterface        $client
     * @param null                   $dummyDataPath
     * @param EntityManagerInterface $em
     */
    public function __construct(ClientInterface $client, $dummyDataPath = null)
    {
        $this->client = $client;
        $this->dummyDataPath = $dummyDataPath;

        echo "RestApiContext base url: " . $this->client->getConfig('base_uri') . "\n\n";

        // strangeness with guzzle?
        $this->addHeader('accept', '*/*');
    }

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();
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
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function iAmSuccessfullyLoggedInWithUsernameAndPassword($username, $password)
    {
        try {

            $this->iSendARequest('POST', 'login', [
                'json' => [
                    'username' => $username,
                    'password' => $password,
                ]
            ]);

            $this->theResponseCodeShouldBe(200);

            $responseBody = json_decode($this->response->getBody(), true);
            $this->addHeader('Authorization', 'Bearer ' . $responseBody['token']);

        } catch (RequestException $e) {

            echo Psr7\str($e->getRequest());

            if ($e->hasResponse()) {
                echo Psr7\str($e->getResponse());
            }

        }
    }

    /**
     * @When I am logged out
     */
    public function iAmLoggedOut()
    {
        $this->removeHeader('Authorization');
    }


    /**
     * @Then I impersonate ":username"
     */
    public function iImpersonate($username)
    {
        $this->iSetHeaderWithValue('x-switch-user', $username);
    }

    /**
     * @Then I stop impersonation
     */
    public function iStopImpersonation()
    {
        $this->iHaveForgottenToSetThe('x-switch-user');
    }

    /**
     * @When I have forgotten to set the :header
     */
    public function iHaveForgottenToSetThe($header)
    {
        $this->addHeader($header, null);
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
     * @param array  $data
     *
     * @When /^(?:I )?send a "([A-Z]+)" request to "([^"]+)"$/
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function iSendARequest($method, $url, array $data = [])
    {
        $url = $this->prepareUrl($url);
        $data = $this->prepareData($data);

        try {
            $this->response = $this->getClient()->request($method, $url, $data);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $this->response = $e->getResponse();
            }
        }
    }

    /**
     * Sends HTTP request to specific URL with field values from Table.
     *
     * @param string    $method request method
     * @param string    $url    relative url
     * @param TableNode $post   table of post values
     *
     * @When /^(?:I )?send a ([A-Z]+) request to "([^"]+)" with values:$/
     * @throws \GuzzleHttp\Exception\GuzzleException
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
        $this->request = $this->getClient()->request($method, $url, $bodyOption);
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
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function iSendARequestWithBody($method, $url, PyStringNode $string)
    {
        $url = $this->prepareUrl($url);
        $string = $this->replacePlaceHolder(trim($string));

        $this->request = $this->iSendARequest(
            $method,
            $url,
            [ 'body' => $string, ]
        );
    }

    /**
     * Sends HTTP request to specific URL with form data from PyString.
     *
     * @param string       $method request method
     * @param string       $url    relative url
     * @param PyStringNode $body   request body
     *
     * @When /^(?:I )?send a "([A-Z]+)" request to "([^"]+)" with form data:$/
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function iSendARequestWithFormData($method, $url, PyStringNode $body)
    {
        $url = $this->prepareUrl($url);
        $body = $this->replacePlaceHolder(trim($body));

        $fields = array();
        parse_str(implode('&', explode("\n", $body)), $fields);
        $this->request = $this->getClient()->request($method, $url, []);
        /** @var \GuzzleHttp\Post\PostBodyInterface $requestBody */
        $requestBody = $this->request->getBody();
        foreach ($fields as $key => $value) {
            $requestBody->setField($key, $value);
        }

        $this->sendRequest();
    }

    /**
     * @When /^(?:I )?send a multipart "([A-Z]+)" request to "([^"]+)" with form data:$/
     * @param           $method
     * @param           $url
     * @param TableNode $post
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \DomainException
     */
    public function iSendAMultipartRequestToWithFormData($method, $url, TableNode $post)
    {
        $url = $this->prepareUrl($url);

        $fileData = $post->getColumnsHash()[0];

        if ( ! array_key_exists('filePath', $fileData)) {
            throw new \DomainException('Multipart requests require a `filePath` Behat table node');
        }

        $filePath = $this->dummyDataPath . $fileData['filePath'];
        unset($fileData['filePath']);

        $data['multipart'] = [
            [
                'name'      => 'name', // symfony form field name
                'contents'  => $fileData['name'],
            ],
            [
                'name'      => 'uploadedFile', // symfony form field name
                'contents'  => fopen($filePath, 'rb'),
            ]
        ];

        // remove the Content-Type header here as it will have been set to `application/json` during the successful
        // login, that preceeds this step in the Behat Background setup
        $this->removeHeader('Content-Type');
        $data = $this->prepareData($data);

        try {
            $this->response = $this->getClient()->request($method, $url, $data);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $this->response = $e->getResponse();
            }
        }
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
        $expected = (int)$code;
        $actual = (int)$this->response->getStatusCode();
        Assertions::assertSame($expected, $actual);
    }

    /**
     * Checks that response body contains specific text.
     *
     * @param string $text
     *
     * @Then /^(?:the )?response should contain "((?:[^"]|\\")*)"$/
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
        $actual = json_decode($this->response->getBody(), true);

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
        $response = $this->response;

        echo sprintf(
            "%d:\n%s",
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
     * @Then I follow the link in the Location response header
     */
    public function iFollowTheLinkInTheLocationResponseHeader()
    {
        $location = $this->response->getHeader('Location')[0];

        if ( ! $this->hasHeader('Authorization')) {
            $responseBody = json_decode($this->response->getBody(), true);
            $this->addHeader('Authorization', 'Bearer ' . $responseBody['token']);
        }

        $this->iSendARequest(Request::METHOD_GET, $location);
    }

    /**
     * @Then the JSON should be valid according to this schema:
     */
    public function theJsonShouldBeValidAccordingToThisSchema(PyStringNode $schema)
    {
        $inspector = new JsonInspector('javascript');

        $json = new \Sanpi\Behatch\Json\Json($this->response->getBody());

        $inspector->validate(
            $json,
            new JsonSchema($schema)
        );
    }

    /**
     * Checks, that given JSON node is equal to given value
     *
     * @Then the JSON node :node should be equal to :text
     * @throws \Exception
     */
    public function theJsonNodeShouldBeEqualTo($node, $text)
    {
        $json = new \Sanpi\Behatch\Json\Json($this->response->getBody());

        $inspector = new JsonInspector('javascript');

        $actual = $inspector->evaluate($json, $node);

        if ($actual != $text) {
            throw new \Exception(
                sprintf("The node value is '%s'", json_encode($actual))
            );
        }
    }

    /**
     * @Then the :selector date should be approximately :date
     * @throws \InvalidArgumentException
     * @throws \PHPUnit_Framework_AssertionFailedError
     */
    public function theDateShouldBeApproximately($selector, $date)
    {
        $responseBody = $this->getResponseBody();
        $dateTimeFromResponse = new \DateTime($responseBody[$selector]);
        $expectedDateTime = new \DateTime($date);

        Assertions::assertTrue(
            $expectedDateTime->diff($dateTimeFromResponse)->format('%s') < 5
        );
    }

    /**
     * Checks whether the response content is equal to given text
     *
     * @Then the response should be equal to
     */
    public function theResponseShouldBeEqualTo(PyStringNode $expected)
    {
        $expected = str_replace('\\"', '"', $expected);
        $actual   = $actual = (string) $this->response->getBody();
        $message = "The string '$expected' is not equal to the response of the current page";
        Assertions::assertEquals($expected, $actual, $message);
    }

    /**
     * @return array
     */
    protected function getResponseBody()
    {
        return json_decode($this->response->getBody(), true);
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
        if ( ! $this->hasHeader($name)) {
            $this->headers[$name] = $value;
        }

        if (!is_array($this->headers[$name])) {
            $this->headers[$name] = [$this->headers[$name]];
        }

        $this->headers[$name] = $value;
    }

    protected function hasHeader($name)
    {
        return isset($this->headers[$name]);
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
     * @return ClientInterface
     */
    private function getClient()
    {
        if (null === $this->client) {
            throw new \RuntimeException('Client has not been set in WebApiContext');
        }

        return $this->client;
    }

    private function prepareData($data)
    {
        if (!empty($this->headers)) {
            $data = array_replace(
                $data,
                ["headers" => $this->headers]
            );
        }

        return $data;
    }
}
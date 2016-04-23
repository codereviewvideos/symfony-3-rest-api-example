<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    private $doctrine;
    private $manager;
    private $schemaTool;
    private $classes;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct(\Doctrine\Common\Persistence\ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->manager = $doctrine->getManager();

        $this->schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->manager);

        $this->classes = $this->manager->getMetadataFactory()->getAllMetadata();
    }

    /**
     * @BeforeScenario
     */
    public function createSchema()
    {
        $this->schemaTool->dropSchema($this->classes);
        $this->schemaTool->createSchema($this->classes);
    }
}

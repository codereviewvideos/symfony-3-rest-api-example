<?php

namespace AppBundle\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use League\Flysystem\Filesystem;

class FileStorageContext implements Context, SnippetAcceptingContext
{
    use \Behat\Symfony2Extension\Context\KernelDictionary;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * FileStorageContext constructor.
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @Then the file with internal name: :internalName should have been deleted
     */
    public function theFileWithInternalNameShouldHaveBeenDeleted($internalName)
    {
        $this->filesystem->assertAbsent($internalName);
    }

    /**
     * @Then the file with internal name: :internalName should not have been deleted
     */
    public function theFileWithInternalNameShouldNotHaveBeenDeleted($internalName)
    {
        $this->filesystem->assertPresent($internalName);
    }
}
<?php

namespace AppBundle\Features\Context;

use AppBundle\Factory\FileFactoryInterface;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use League\Flysystem\Filesystem;

class FileSetupContext implements Context, SnippetAcceptingContext
{
    use \Behat\Symfony2Extension\Context\KernelDictionary;

    /**
     * @var EntityManagerInterface
     */
    protected $em;
    /**
     * @var UserManagerInterface
     */
    protected $userManager;
    /**
     * @var FileFactoryInterface
     */
    private $fileFactory;
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var string
     */
    private $dummyDataPath;

    /**
     * FileSetupContext constructor.
     * @param UserManagerInterface      $userManager
     * @param FileFactoryInterface      $fileFactory
     * @param EntityManagerInterface    $em
     * @param Filesystem                $filesystem
     * @param string                    $dummyDataPath
     */
    public function __construct(
        UserManagerInterface $userManager,
        FileFactoryInterface $fileFactory,
        EntityManagerInterface $em,
        Filesystem $filesystem,
        $dummyDataPath
    )
    {
        $this->userManager = $userManager;
        $this->fileFactory = $fileFactory;
        $this->em = $em;
        $this->filesystem = $filesystem;
        $this->dummyDataPath = $dummyDataPath;
    }

    /**
     * @AfterScenario
     */
    public static function removeUploadedFiles()
    {
        foreach (glob(__DIR__ . '/../../../../uploads/*') as $file) {
            echo "Removing uploaded file: \n";
            echo basename($file) . "\n";
            unlink($file);
        }
    }

    /**
     * @Given there are files with the following details:
     */
    public function thereAreFilesWithTheFollowingDetails(TableNode $files)
    {
        foreach ($files->getColumnsHash() as $key => $val) {

            $file = $this->fileFactory->create(
                $val['originalFileName'],
                $val['internalFileName'],
                $val['guessedExtension'],
                $val['size']
            );

            $this->em->persist($file);
            $this->em->flush();

            $qb = $this->em->createQueryBuilder();

            $query = $qb->update('AppBundle:File', 'f')
                ->set('f.id', $qb->expr()->literal($val['uid']))
                ->where('f.internalFileName = :internalFileName')
                ->setParameters([
                    'internalFileName' => $val['internalFileName'],
                ])
                ->getQuery()
            ;

            $query->execute();

            if ( ! empty($val['dummyFile'])) {
                $this->filesystem->put(
                    $val['internalFileName'],
                    file_get_contents($this->dummyDataPath . $val['dummyFile'])
                );
            }
        }

        $this->em->flush();
    }
}
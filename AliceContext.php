<?php


namespace Vivait\BehatAliceLoader;


use Behat\Behat\Context\BehatContext;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Nelmio\Alice\ORM\Doctrine;

/**
 * @mixin
 */
class AliceContext extends BehatContext
{
    /**
     * @var BehatAliceLoader
     */
    protected $loader;

    /**
     * @var Doctrine
     */
    protected $persister;

    use KernelDictionary;

    function __construct()
    {
        $this->loader    = new BehatAliceLoader();
    }

    protected function getPersister() {
        if ($this->persister) {
            return $this->persister;
        }

        $objectManager   = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->persister = new Doctrine($objectManager);

        $this->loader->setORM($this->persister);

        return $this->persister;
    }

    /**
     * @Given /^the database is clean$/
     * @Given /^the database is empty$/
     */
    public function theDatabaseIsClean()
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $purger   = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();
    }

    /**
     * @Given /^there are fixtures "([^"]*)"$/
     */
    public function thereAreFixtures($fixtures)
    {

        $cwd = getcwd();
        chdir($this->getKernel()->getRootDir() . '/../');
        $objects = $this->loader->load($fixtures);
        chdir($cwd);

        $this->getPersister()->persist($objects);

        return $objects;
    }

    /**
     * @Given /^there are the following "([^"]*)":$/
     */
    public function thereAreTheFollowing($entity, TableNode $table)
    {
        $objectManager   = $this->getContainer()->get('doctrine.orm.entity_manager');
        $objects = $this->loader->loadTableNode($objectManager->getClassMetadata($entity)->getName(), $table);
        $this->getPersister()->persist($objects);

        return $objects;
    }
} 

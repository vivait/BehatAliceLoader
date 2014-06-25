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
class AliceContext extends BehatContext {
	use KernelDictionary;

	/**
	 * @Given /^the database is clean$/
	 */
	public function theDatabaseIsClean()
	{
		$em = $this->getContainer()->get('doctrine.orm.entity_manager');

		$purger = new ORMPurger($em);
		$executor = new ORMExecutor($em, $purger);
		$executor->purge();
	}

	/**
	 * @Given /^there are fixtures "([^"]*)":$/
	 */
	public function thereAreFixtures( $fixtures ) {
		$objectManager = $this->getContainer()->get('doctrine.orm.entity_manager');

		$loader  = new BehatAliceLoader();
		$objects = $loader->load($fixtures);

		$persister = new Doctrine( $objectManager );
		$persister->persist( $objects );
	}

	/**
	 * @Given /^there are the following "([^"]*)":$/
	 */
	public function thereAreTheFollowing( $entity, TableNode $table ) {
		$objectManager = $this->getContainer()->get('doctrine.orm.entity_manager');

		$loader  = new BehatAliceLoader();
		$objects = $loader->loadTableNode( $objectManager->getClassMetadata($entity)->getName(), $table );

		$persister = new Doctrine( $objectManager );
		$persister->persist( $objects );
	}
} 
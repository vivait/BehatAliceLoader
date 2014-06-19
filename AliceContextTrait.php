<?php


namespace Vivait\BehatAliceLoader;


use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Nelmio\Alice\ORM\Doctrine;

/**
 * @mixin
 */
trait AliceContextTrait {
	use KernelDictionary;

	/**
	 * @Given /^there are fixtures "([^"]*)":$/
	 */
	public function thereAreFixtures( $fixtures ) {
		$container     = $this->getContainer();
		$objectManager = $container->get( 'doctrine' )->getManager();

		$loader  = new BehatAliceLoader();
		$objects = $loader->load($fixtures);

		$persister = new Doctrine( $objectManager );
		$persister->persist( $objects );

		return true;
	}

	/**
	 * @Given /^there are the following "([^"]*)":$/
	 */
	public function thereAreTheFollowing( $entity, TableNode $table ) {
		$container     = $this->getContainer();

		$objectManager = $container->get( 'doctrine' )->getManager();

		$loader  = new BehatAliceLoader();
		$objects = $loader->loadTableNode( $entity, $table );

		$persister = new Doctrine( $objectManager );
		$persister->persist( $objects );

		return true;
	}
} 
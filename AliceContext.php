<?php

namespace Vivait\BehatAliceLoader;

use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelDictionary;

class AliceContext {
	use KernelDictionary;

	/**
	 * @Given /^the "([^"]*)" fixtures have been loaded$/
	 */
	public function theFixturesHaveBeenLoaded( $fixtures ) {
		$container     = $this->getContainer();
		$objectManager = $container->get( 'doctrine' )->getManager();

		$loader  = new BehatAliceLoader();
		foreach( $loader->load( $fixtures ) as $entity) {
			$objectManager->merge($entity);
		}

		$objectManager->flush();
	}

	/**
	 * @Given /^there are the following "([^"]*)":$/
	 */
	public function thereAreTheFollowing( $entity, TableNode $table ) {
		$container     = $this->getContainer();
		$objectManager = $container->get( 'doctrine' )->getManager();

		$loader  = new BehatAliceLoader();
		foreach( $loader->loadTableNode( $entity, $table ) as $entity) {
			$objectManager->merge($entity);
		}

		$objectManager->flush();
	}
} 
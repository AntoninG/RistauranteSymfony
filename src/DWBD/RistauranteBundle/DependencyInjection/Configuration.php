<?php

namespace DWBD\RistauranteBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function getConfigTreeBuilder()
	{
		$treeBuilder = new TreeBuilder();
		$treeBuilder
			->root('dwbd_ristaurante')
			->children()
			->variableNode('dishes_directory')
			->isRequired()
			->end();

		return $treeBuilder;
	}

}
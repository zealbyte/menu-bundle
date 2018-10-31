<?php

/*
 * This file is part of the ZealByte Menu Bundle.
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Bundle\MenuBundle\DependencyInjection
{
	use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
	use Symfony\Component\Config\Definition\Builder\TreeBuilder;
	use Symfony\Component\Config\Definition\ConfigurationInterface;

	/**
	 * Menu configuration.
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class Configuration implements ConfigurationInterface
	{
		public function getConfigTreeBuilder ()
		{
			$treeBuilder = new TreeBuilder();
			$rootNode = $treeBuilder->root('platform');

			$rootNode
				->children()
					->scalarNode('default_template')->defaultValue('@Menu/list.html.twig')->end()
				->end();

			return $treeBuilder;
		}

		private function addRouteOptionsSection ($node)
		{
			$node
				->fixXmlConfig('option')
				->children()
					->arrayNode('route_builder')
						->scalarPrototype()->end()
					->end()
				->end();
		}

	}
}

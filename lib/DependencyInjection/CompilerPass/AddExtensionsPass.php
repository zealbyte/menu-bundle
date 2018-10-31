<?php

/*
 * This file is part of the ZealByte Menu Bundle.
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Bundle\MenuBundle\DependencyInjection\CompilerPass
{
	use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
	use Symfony\Component\DependencyInjection\ContainerBuilder;
	use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
	use Symfony\Component\DependencyInjection\Reference;

	/**
	 * Add extension compiler pass
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class AddExtensionsPass implements CompilerPassInterface
	{
		public function process(ContainerBuilder $container)
		{
			if (!$container->has('knp_menu.factory'))
				return;

			$taggedServiceIds = $container->findTaggedServiceIds('knp_menu.factory_extension');

			if (0 === count($taggedServiceIds))
				return;

			$definition = $container->findDefinition('knp_menu.factory');

			if (!method_exists($container->getParameterBag()->resolveValue($definition->getClass()), 'addExtension')) {
				throw new InvalidConfigurationException(sprintf(
					'You must implement the "addExtension" method in %s.',
					$definition->getClass()
				));
			}

			foreach ($taggedServiceIds as $id => $tags) {
				foreach ($tags as $tag) {
					$priority = isset($tag['priority']) ? $tag['priority'] : 0;
					$definition->addMethodCall('addExtension', [new Reference($id), $priority]);
				}
			}

		}
	}
}

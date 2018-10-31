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
	use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
	use Symfony\Component\DependencyInjection\ContainerBuilder;
	use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
	use Symfony\Component\DependencyInjection\Reference;
	use ZealByte\Bundle\MenuBundle\Builder\MenuBuilderInterface;

	/**
	 * Builder compiler pass.
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class BuilderPass implements CompilerPassInterface
	{
		const MENU_BUILDER_TAG = 'menu.builder';
		const MENU_BUILDER_PROVIDER_DEFINITION = 'ZealByte\Bundle\MenuBundle\Provider\MenuBuilderProvider';

		public function process (ContainerBuilder $container)
		{
			if (!$container->hasDefinition(self::MENU_BUILDER_PROVIDER_DEFINITION))
				return;

			$definition = $container->getDefinition(self::MENU_BUILDER_PROVIDER_DEFINITION);

			foreach ($container->findTaggedServiceIds(self::MENU_BUILDER_TAG) as $id => $tags) {
				$builderDefinition = $container->getDefinition($id);
				$builderClass = $container->getParameterBag()->resolveValue($builderDefinition->getClass());

				if(!is_subclass_of($builderClass, MenuBuilderInterface::class))
					throw new \InvalidArgumentException("Service \"$id\" must implement ".MenuBuilderInterface::class.".");

				foreach ($tags as $attributes) {
					if (empty($attributes['menu']))
						throw new InvalidArgumentException("The menu attribute is not defined in the \"menu.builder\" tag for the service \"$id\"");

					$builderDefinition->addMethodCall('setMenuName', [$attributes['menu']]);
					$definition->addMethodCall('addMenuBuilder', [new Reference($id), $attributes['menu']]);
				}
			}
		}

	}
}

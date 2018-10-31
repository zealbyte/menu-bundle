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
	use Symfony\Component\Config\FileLocator;
	use Symfony\Component\DependencyInjection\ContainerBuilder;
	use Symfony\Component\DependencyInjection\Loader;
	use Symfony\Component\HttpKernel\DependencyInjection\Extension;
	use Knp\Menu\Matcher\Voter\VoterInterface;
	use Knp\Menu\ItemInterface;

	/**
	 * The menu extension.
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class MenuExtension extends Extension
	{
		public function load (array $configs, ContainerBuilder $container)
		{
			$loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

			// @TODO We will need to keep up to date with KnpMenuBundle
			if (!isset(($container->getParameter('kernel.bundles'))['KnpMenuBundle']))
				$loader->load('knp_services.xml');

			$loader->load('menu.xml');

			$configuration = new Configuration();
			$config = $this->processConfiguration($configuration, $configs);

			$container->setParameter('menu.default_template', $config['default_template']);
		}
	}
}

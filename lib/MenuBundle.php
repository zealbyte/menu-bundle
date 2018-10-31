<?php

/*
 * This file is part of the ZealByte Menu Bundle.
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Bundle\MenuBundle
{
	use Symfony\Component\HttpKernel\Bundle\Bundle;
	use Symfony\Component\DependencyInjection\ContainerBuilder;
	use Symfony\Component\DependencyInjection\Compiler\PassConfig;
	use ZealByte\Bundle\MenuBundle\DependencyInjection\CompilerPass\AddExtensionsPass;
	use ZealByte\Bundle\MenuBundle\DependencyInjection\CompilerPass\AddVotersPass;
	use ZealByte\Bundle\MenuBundle\DependencyInjection\CompilerPass\BuilderPass;

	/**
	 * Menu bundle.
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class MenuBundle extends Bundle
	{
		/**
		 * Boots the Bundle.
		 */
		public function boot ()
		{
		}

		/**
		 * Shutdowns the Bundle.
		 */
		public function shutdown ()
		{
		}

		/**
		 * Builds the bundle.
		 *
		 * It is only ever called once when the cache is empty.
		 *
		 * This method can be overridden to register compilation passes,
		 * other extensions, ...
		 */
		public function build (ContainerBuilder $container)
		{
			parent::build($container);
			$container->addCompilerPass(new AddExtensionsPass());
			$container->addCompilerPass(new AddVotersPass());
			$container->addCompilerPass(new BuilderPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION);
		}

	}
}

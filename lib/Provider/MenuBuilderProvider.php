<?php

/*
 * This file is part of the ZealByte Menu Bundle.
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Bundle\MenuBundle\Provider
{
	use InvalidArgumentException;
	use Symfony\Component\Translation\TranslatorInterface;
	use Knp\Menu\MenuItem;
	use Knp\Menu\FactoryInterface;
	use Knp\Menu\Provider\MenuProviderInterface;
	use ZealByte\Bundle\MenuBundle\Builder\MenuBuilderInterface;

	/**
	 * The menu provider.
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class MenuBuilderProvider implements MenuProviderInterface
	{
		private $menuFactory;

		private $translator;

		private $menuBuilders = [];

		public function __construct (FactoryInterface $menuFactory, TranslatorInterface $translator = null)
		{
			$this->setMenuFactory($menuFactory);

			if ($translator)
				$this->setTranslator($translator);
		}

		public function addMenuBuilder (MenuBuilderInterface $builder, string $menu) : self
		{
			$builder->setMenuName($menu);
			$this->menuBuilders[] = $builder;

			return $this;
		}

		public function get ($name, array $options = []) : MenuItem
		{
			//@todo This will be called multiple times, we need to maximize effeciency
			return $this->generateMenu($name, $options);
		}

		public function has ($name, array $options = []) : bool
		{
			foreach ($this->menuBuilders as $menuBuilder)
				if ($menuBuilder->has($name))
					return true;

			return false;
		}

		public function setMenuFactory (FactoryInterface $menuFactory) : self
		{
			$this->menuFactory = $menuFactory;

			return $this;
		}

		public function setTranslator (TranslatorInterface $translator) : self
		{
			$this->translator = $translator;

			return $this;
		}

		protected function generateMenu (string $name, array $options) : MenuItem
		{
			foreach ($this->menuBuilders as $menuBuilder)
				if ($menuBuilder->has($name))
					return $menuBuilder->build($this->menuFactory, $name, $options);

			throw new InvalidArgumentException("No menu \"$name\" exists in any registered menu builder.");
		}

	}
}

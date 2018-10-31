<?php

/*
 * This file is part of the ZealByte Menu Bundle.
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Bundle\MenuBundle\Builder
{
	use Knp\Menu\FactoryInterface;
	use Knp\Menu\MenuItem;

	/**
	 * Menu builder interface
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	interface MenuBuilderInterface
	{
		public function build (FactoryInterface $factory, string $name, array $options = []) : MenuItem;

		public function has (string $name) : bool;

		public function setMenuName (string $name) : MenuBuilderInterface;
	}
}

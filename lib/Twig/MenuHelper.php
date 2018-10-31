<?php

/*
 * This file is part of the ZealByte Menu Bundle.
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Bundle\MenuBundle\Twig
{
	use BadMethodCallException;
	use InvalidArgumentException;
	use LogicException;
	use Knp\Menu\ItemInterface;
	use Knp\Menu\Matcher\MatcherInterface;
	use Knp\Menu\Util\MenuManipulator;
	use Knp\Menu\Renderer\RendererProviderInterface;
	use Knp\Menu\Provider\MenuProviderInterface;

	/**
	 * Helper class containing logic to retrieve and render menus from templating engines
	 *
	 */
	class MenuHelper
	{
		private $rendererProvider;
		private $menuProvider;
		private $menuManipulator;
		private $matcher;

		/**
		 * @param RendererProviderInterface  $rendererProvider
		 * @param MenuProviderInterface|null $menuProvider
		 * @param MenuManipulator|null       $menuManipulator
		 * @param MatcherInterface|null      $matcher
		 */
		public function __construct (?RendererProviderInterface $rendererProvider = null, ?MenuProviderInterface $menuProvider = null, ?MenuManipulator $menuManipulator = null, ?MatcherInterface $matcher = null)
		{
			$this->rendererProvider = $rendererProvider;
			$this->menuProvider = $menuProvider;
			$this->menuManipulator = $menuManipulator;
			$this->matcher = $matcher;
		}

		public function setMenuProvider (MenuProviderInterface $menu_provider) : self
		{
			$this->menuProvider = $menu_provider;

			return $this;
		}

		public function setMenuManipulator(MenuManipulator $menu_manipulator) : self
		{
			$this->menuManipulator = $menu_manipulator;

			return $this;
		}

		public function setMenuMatcher(MatcherInterface $menu_matcher) : self
		{
			$this->matcher = $menu_matcher;

			return $this;
		}

		public function setMenuRendererProvider(RendererProviderInterface $renderer_provider) : self
		{
			$this->rendererProvider = $renderer_provider;

			return $this;
		}

		/**
		 * Retrieves item in the menu, eventually using the menu provider.
		 *
		 * @param ItemInterface|string $menu
		 * @param array                $path
		 * @param array                $options
		 *
		 * @return ItemInterface
		 *
		 * @throws \BadMethodCallException   when there is no menu provider and the menu is given by name
		 * @throws \LogicException
		 * @throws \InvalidArgumentException when the path is invalid
		 */
		public function get ($menu, array $path = [], array $options = [])
		{
			if (!$menu instanceof ItemInterface) {
				if (null === $this->menuProvider)
					throw new BadMethodCallException('A menu provider must be set to retrieve a menu');

				$menuName = $menu;
				$menu = $this->menuProvider->get($menuName, $options);

				if (!$menu instanceof ItemInterface)
					throw new LogicException(sprintf('The menu "%s" exists, but is not a valid menu item object. Check where you created the menu to be sure it returns an ItemInterface object.', $menuName));
			}

			foreach ($path as $child) {
				$menu = $menu->getChild($child);

				if (null === $menu)
					throw new InvalidArgumentException(sprintf('The menu has no child named "%s"', $child));
			}

			return $menu;
		}

		/**
		 * Renders a menu with the specified renderer.
		 *
		 * If the argument is an array, it will follow the path in the tree to
		 * get the needed item. The first element of the array is the whole menu.
		 * If the menu is a string instead of an ItemInterface, the provider
		 * will be used.
		 *
		 * @param ItemInterface|string|array $menu
		 * @param array                      $options
		 *
		 * @return string
		 *
		 * @throws \InvalidArgumentException
		 */
		public function render ($menu, array $options = [])
		{
			$menu = $this->castMenu($menu);

			return $this->rendererProvider->get('twig')->render($menu, $options);
		}

		/**
		 * Renders an array ready to be used for breadcrumbs.
		 *
		 * Each element in the array will be an array with 3 keys:
		 * - `label` containing the label of the item
		 * - `url` containing the url of the item (may be `null`)
		 * - `item` containing the original item (may be `null` for the extra items)
		 *
		 * The subItem can be one of the following forms
		 *   * 'subItem'
		 *   * ItemInterface object
		 *   * array('subItem' => '@homepage')
		 *   * array('subItem1', 'subItem2')
		 *   * array(array('label' => 'subItem1', 'url' => '@homepage'), array('label' => 'subItem2'))
		 *
		 * @param mixed $item
		 * @param mixed $subItem A string or array to append onto the end of the array
		 *
		 * @return array
		 */
		public function getBreadcrumbsArray ($menu, $subItem = null)
		{
			if (null === $this->menuManipulator)
				throw new BadMethodCallException('The menu manipulator must be set to get the breadcrumbs array');

			$menu = $this->castMenu($menu);
			$breadcrumbs = $this->menuManipulator->getBreadcrumbsArray($menu, $subItem);

			for ($i = 0; count($breadcrumbs) > $i; $i++)
				unset($breadcrumbs[$i]['item']);

			return $breadcrumbs;
		}

		/**
		 * Returns the current item of a menu.
		 *
		 * @param ItemInterface|array|string $menu
		 *
		 * @return ItemInterface|null
		 */
		public function getCurrentItem ($menu)
		{
			if (null === $this->matcher)
				throw new BadMethodCallException('The matcher must be set to get the current item of a menu');

			$menu = $this->castMenu($menu);

			return $this->retrieveCurrentItem($menu);
		}

		/**
		 * @param ItemInterface|array|string $menu
		 *
		 * @return ItemInterface
		 */
		private function castMenu ($menu)
		{
			if (!$menu instanceof ItemInterface) {
				$path = [];

				if (is_array($menu)) {
					if (empty($menu))
						throw new InvalidArgumentException('The array cannot be empty');

					$path = $menu;
					$menu = array_shift($path);
				}

				return $this->get($menu, $path);
			}

			return $menu;
		}

		/**
		 * @param ItemInterface $item
		 *
		 * @return ItemInterface|null
		 */
		private function retrieveCurrentItem (ItemInterface $item)
		{
			if ($this->matcher->isCurrent($item))
				return $item;

			if ($this->matcher->isAncestor($item)) {
				foreach ($item->getChildren() as $child) {
					$currentItem = $this->retrieveCurrentItem($child);

					if (null !== $currentItem) {
						return $currentItem;
					}
				}
			}

			return null;
		}

	}
}

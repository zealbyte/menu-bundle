<?php

/*
 * This file is part of the ZealByte Menu Bundle.
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Bundle\MenuBundle\Twig\Extension
{
	use Twig\Extension\AbstractExtension;
	use Knp\Menu\ItemInterface;
	use Knp\Menu\Matcher\MatcherInterface;
	use ZealByte\Bundle\MenuBundle\Twig\MenuHelper;

	/**
	 * Menu extension for twig.
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class MenuExtension extends AbstractExtension
	{
		private $helper;
		private $matcher;

		/**
		 * @param Helper $helper
		 */
		public function __construct (MenuHelper $helper, MatcherInterface $matcher = null)
		{
			$this->helper = $helper;
			$this->matcher = $matcher;
		}

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
		public function getFunctions ()
		{
			return [
				new \Twig_SimpleFunction('menu', [$this, 'render'], ['is_safe' => ['html']]),
				new \Twig_SimpleFunction('breadcrumbs', [$this, 'getBreadcrumbs']),
			];
		}

		public function getTests()
		{
			return [
				new \Twig_SimpleTest('current', [$this, 'isCurrent']),
				new \Twig_SimpleTest('ancestor', [$this, 'isAncestor']),
				new \Twig_SimpleTest('first', [$this, 'isFirst']),
				new \Twig_SimpleTest('last', [$this, 'isLast']),
				new \Twig_SimpleTest('level', [$this, 'isLevel']),
			];
		}

		/**
		 * Renders a menu with the specified renderer.
		 *
		 * @param ItemInterface|string|array $menu
		 * @param string                     $template
		 *
		 * @return string
		 */
		public function render ($menu, string $template = null, array $options = [])
		{
			if ($template)
				$options['template'] = $template;

			return $this->helper->render($menu, $options);
		}

		/**
		 * Returns an array ready to be used for breadcrumbs.
		 *
		 * @param ItemInterface|string $menu
		 *
		 * @return array
		 */
		public function getBreadcrumbs ($menu)
		{
			$rootItem = $this->helper->get($menu);
			$currentItem = $this->helper->getCurrentItem($rootItem);

			if (null === $currentItem)
				$currentItem = $rootItem;

			return $this->helper->getBreadcrumbsArray($currentItem);
		}

		/**
		 * Checks whether an item is current.
		 *
		 * @param ItemInterface $item
		 *
		 * @return boolean
		 */
		public function isCurrent (ItemInterface $item)
		{
			if (null === $this->matcher)
				throw new \BadMethodCallException('The matcher must be set to get the breadcrumbs array');

			return $this->matcher->isCurrent($item);
		}

		/**
		 * Checks whether an item is the ancestor of a current item.
		 *
		 * @param ItemInterface $item
		 * @param integer       $depth The max depth to look for the item
		 *
		 * @return boolean
		 */
		public function isAncestor (ItemInterface $item, $depth = null)
		{
			if (null === $this->matcher)
				throw new \BadMethodCallException('The matcher must be set to get the breadcrumbs array');

			return $this->matcher->isAncestor($item);
		}

		/**
		 * Checks whether an item is the acting first item
		 *
		 * @param ItemInterface $item
		 *
		 * @return boolean
		 */
		public function isFirst (ItemInterface $item)
		{
			return $item->actsLikeFirst();
		}

		/**
		 * Checks whether an item is the acting last item
		 *
		 * @param ItemInterface $item
		 *
		 * @return boolean
		 */
		public function isLast (ItemInterface $item)
		{
			return $item->actsLikeLast();
		}

		/**
		 * Checks the menu depth level of an item
		 *
		 * @param ItemInterface $item
		 *
		 * @return boolean
		 */
		public function isLevel (ItemInterface $item, int $level)
		{
			return (bool) ($item->getLevel() === $level);
		}

		/**
		 * @return string
		 */
		public function getName ()
		{
			return 'zealbyte_menu';
		}

	}
}

<?php

/*
 * This file is part of the ZealByte Menu Bundle.
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Bundle\MenuBundle\EventListener
{
	use Symfony\Component\HttpKernel\KernelEvents;
	use Symfony\Component\EventDispatcher\EventSubscriberInterface;
	use Symfony\Component\HttpKernel\Event\GetResponseEvent;
	use Knp\Menu\Provider\MenuProviderInterface;
	use Knp\Menu\Matcher\MatcherInterface;
	use Knp\Menu\Util\MenuManipulator;
	use Knp\Menu\Renderer\RendererProviderInterface;
	use ZealByte\Bundle\MenuBundle\Twig\MenuHelper;

	/**
	 * Menu helper for templating.
	 *
	 * @todo This is a hack.. We should actually use knpmenubundle.
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	final class MenuHelperSubscriber implements EventSubscriberInterface
	{
		private $menuHelper;

		private $menuProvider;

		private $menuManipulator;

		private $menuMatcher;

		private $menuRendererProvider;

		public static function getSubscribedEvents ()
		{
			return [
				KernelEvents::REQUEST => [
					['onKernelRequest', 10]
				],
			];
		}

		public function __construct (MenuProviderInterface $menu_provider,  MenuManipulator $menu_manipulator = null, MatcherInterface $menu_matcher = null, RendererProviderInterface $renderer_provider, MenuHelper $menu_helper = null)
		{
			$this->menuProvider = $menu_provider;
			$this->menuManipulator = $menu_manipulator;
			$this->menuMatcher = $menu_matcher;
			$this->menuRendererProvider = $renderer_provider;
			$this->menuHelper = $menu_helper;
		}

		public function onKernelRequest (GetResponseEvent $event) : void
		{
			$this->menuHelper->setMenuProvider($this->menuProvider);
			$this->menuHelper->setMenuManipulator($this->menuManipulator);
			$this->menuHelper->setMenuMatcher($this->menuMatcher);
			$this->menuHelper->setMenuRendererProvider($this->menuRendererProvider);
		}

	}
}


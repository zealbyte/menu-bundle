<?php

/*
 * This file is part of the ZealByte Menu Bundle.
 *
 * (c) ZealByte <info@zealbyte.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZealByte\Bundle\MenuBundle\Renderer
{
	use Knp\Menu\Renderer\RendererInterface;
	use Knp\Menu\ItemInterface;
	use Knp\Menu\Matcher\MatcherInterface;

	/**
	 * Twig renderer.
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class TwigRenderer implements RendererInterface
	{
		/**
		 * @var \Twig_Environment
		 */
		private $environment;

		private $matcher;

		private $defaultOptions;

		/**
		 * @param \Twig_Environment $environment
		 * @param string            $template
		 * @param MatcherInterface  $matcher
		 */
		public function __construct (\Twig_Environment $environment, MatcherInterface $matcher, array $options = [])
		{
			$this->environment = $environment;

			$this->matcher = $matcher;

			$this->defaultOptions = array_merge([
				'template' => '@Menu/list.html.twig',
				'clear_matcher' => true,
			], $options);
		}

		public function render (ItemInterface $item, array $options = [])
		{
			$options = array_merge($this->defaultOptions, $options);

			$html = $this->environment->render($options['template'], ['item' => $item]);

			if ($options['clear_matcher'])
				$this->matcher->clear();

			return $html;
		}
	}
}

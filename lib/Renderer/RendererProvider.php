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
	use Knp\Menu\Renderer\RendererProviderInterface;
	use Knp\Menu\Renderer\RendererInterface;

	/**
	 * Renderer porvider
	 *
	 * @author Dustin Martella <dustin.martella@zealbyte.com>
	 */
	class RendererProvider implements RendererProviderInterface
	{
		private $renderers = [];
		private $defaultRenderer;

		public function add (RendererInterface $renderer, string $name, bool $is_default = false)
		{
			if (array_key_exists($name, $this->renderers))
				throw new \InvalidArgumentException("Renderer name \"$name\" is already defined.");

			if (empty($this->defaultRenderer) || $is_default)
				$this->defaultRenderer = $name;

			$this->renderers[$name] = $renderer;

			return $this;
		}

		public function get ($name = null)
		{
			if (null === $name)
				$name = $this->defaultRenderer;

			if (!isset($this->renderers[$name]))
				throw new \InvalidArgumentException(sprintf('The renderer "%s" is not defined.', $name));

			return $this->renderers[$name];
		}

		public function has ($name)
		{
			return array_key_exists($name, $this->renderers);
		}

	}
}

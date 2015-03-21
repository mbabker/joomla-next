<?php
/**
 * Joomla! Next Application Platform
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Joomla\CMS\Document;

/**
 * Factory for handling document classes
 *
 * @since  1.0
 */
class DocumentFactory
{
	/**
	 * Loads a document object.
	 *
	 * @param   string  $type     The type of document to load
	 * @param   array   $options  Options for instantiating the renderer
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function getDocument($type, array $options = [])
	{
		/*
		 * This supports the ability to override a document based on the format type.  Document class names must end in 'Document'.
		 * The lookup priority is as follows:
		 *
		 * 1) Custom namespace (My\Namespace\TypeDocument)
		 * 2) Default namespace (Joomla\CMS\Document\HtmlDocument)
		 */
		$namespace     = isset($options['namespace']) ? $options['namespace'] : __NAMESPACE__;
		$baseNamespace = __NAMESPACE__;

		$classes = [
			1 => $namespace . '\\' . ucfirst($type) . 'Document',
			2 => $baseNamespace . '\\' . ucfirst($type) . 'Document',
		];

		$found = false;

		foreach ($classes as $class)
		{
			if (class_exists($class))
			{
				// Found our object, break the loop
				$found = true;

				break;
			}
		}

		if (!$found)
		{
			throw new \InvalidArgumentException(
				sprintf(
					'Unable to load document of type %s', $type
				)
			);
		}

		$document = new $class;

		// Inject any attributes we've been passed
		foreach ($options as $attribute => $value)
		{
			$setter = 'set' . ucfirst($attribute);

			if (method_exists($document, $setter))
			{
				$document->$setter($value);
			}
		}

		return $document;
	}

	/**
	 * Loads a renderer.
	 *
	 * @param   string             $type     The renderer type
	 * @param   DocumentInterface  $doc      The DocumentInterface object to inject into the renderer
	 * @param   array              $options  Options for instantiating the renderer
	 *
	 * @return  DocumentRendererInterface
	 *
	 * @since   1.0
	 * @throws  \InvalidArgumentException
	 */
	public function getRenderer($type, DocumentInterface $doc, array $options = [])
	{
		/*
		 * This supports the ability to override a renderer based on the format type or by injecting a custom namespace.
		 * The lookup priority is as follows:
		 *
		 * 1) Custom namespace & type (My\Namespace\Html\Renderer\Class)
		 * 2) Custom namespace (My\Namespace\Renderer\Class)
		 * 3) Default namespace & type (Joomla\CMS\Document\Html\Renderer\Class)
		 * 4) Default namespace (Joomla\CMS\Document\Renderer\Class)
		 */
		$namespace     = isset($options['namespace']) ? $options['namespace'] : __NAMESPACE__;
		$baseNamespace = __NAMESPACE__;
		$documentType  = $doc->getType();

		$classes = [
			1 => $namespace . '\\' . ucfirst(strtolower($doc->getType())) . '\\Renderer\\' . ucfirst($type),
			2 => $namespace . '\\Renderer\\' . ucfirst($type),
			3 => $baseNamespace . '\\' . ucfirst(strtolower($doc->getType())) . '\\Renderer\\' . ucfirst($type),
			4 => $baseNamespace . '\\Renderer\\' . ucfirst($type),
		];

		$found = false;

		foreach ($classes as $class)
		{
			if (class_exists($class))
			{
				// Found our object, break the loop
				$found = true;

				break;
			}
		}

		if (!$found)
		{
			throw new \InvalidArgumentException(
				sprintf(
					'Unable to load renderer of type %s', $type
				)
			);
		}

		return new $class($doc);
	}
}

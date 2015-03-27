<?php
/**
 * Joomla! Next Application Platform
 *
 * @copyright  Copyright (C) Open Source Matters, Inc. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

namespace Joomla\CMS\Renderer\Helper;
use Joomla\CMS\Uri\Uri;
use Joomla\Filesystem\File;

/**
 * Trait for media renderer helpers
 *
 * @since  1.0
 */
trait MediaTrait
{
	/**
	 * Compute the files to be included
	 *
	 * @param   string   $folder         Folder name to search into (images, css, js, ...).
	 * @param   string   $file           Path to file.
	 * @param   boolean  $relative       Path to file is relative to /media folder  (and searches in template).
	 * @param   boolean  $detect_browser Detect browser to include specific browser files.
	 * @param   boolean  $detect_debug   Detect debug to include compressed files if debug is on.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	protected function includeRelativeFiles($folder, $file, $relative, $detect_browser, $detect_debug)
	{
		// If http is present in filename
		if (strpos($file, 'http') === 0)
		{
			return [$file];
		}

		// Extract extension and strip the file
		$strip = File::stripExt($file);
		$ext   = pathinfo($file, PATHINFO_EXTENSION);

		// Prepare array of files
		$includes = [];

		$potential = [$strip];

		/** @var \Joomla\CMS\Application\CMSApplicationInterface $app */
		$app     = $this->container->get('app');
		$baseUri = (new Uri)->base(true);
		$rootUri = (new Uri)->root(true);

		// Detect browser and compute potential files
		if ($detect_browser)
		{
			/** @var \Joomla\Application\Web\WebClient $navigator */
			$navigator = $app->client;
			$browser   = $navigator->browser;
			$version   = $navigator->browserVersion;

			// Split the browser version to major/minor
			$version = explode('.', $version);
			$major   = $version[0];
			$minor   = isset($version[1]) ? $version[1] : 'x';

			// Try to include files named filename.ext, filename_browser.ext, filename_browser_major.ext, filename_browser_major_minor.ext
			// where major and minor are the browser version names
			$potential = array_merge($potential, [
				$strip . '_' . $browser,
				$strip . '_' . $browser . '_' . $major,
				$strip . '_' . $browser . '_' . $major . '_' . $minor
			]);
		}

		// If relative search in template directory or media directory
		if ($relative)
		{
			// Get the template
			$template = $app->getTemplate();

			// For each potential files
			foreach ($potential as $strip)
			{
				$files = [];

				// Detect debug mode
				if ($detect_debug && JDEBUG)
				{
					/*
					 * Detect if we received a file in the format name.min.ext
					 * If so, strip the .min part out, otherwise append -uncompressed
					 */
					if (strrpos($strip, '.min', '-4'))
					{
						$position = strrpos($strip, '.min', '-4');
						$filename = str_replace('.min', '.', $strip, $position);
						$files[]  = $filename . $ext;
					}
					else
					{
						$files[] = $strip . '-uncompressed.' . $ext;
					}
				}

				$files[] = $strip . '.' . $ext;

				/*
				 * Loop on 1 or 2 files and break on first found.
				 * Add the content of the MD5SUM file located in the same folder to url to ensure cache browser refresh
				 * This MD5SUM file must represent the signature of the folder content
				 */
				foreach ($files as $file)
				{
					// If the file is in the template folder
					$path = JPATH_THEMES . "/$template/$folder/$file";

					if (file_exists($path))
					{
						$md5        = dirname($path) . '/MD5SUM';
						$includes[] = $baseUri . "/templates/$template/$folder/$file" . (file_exists($md5) ? ('?' . file_get_contents($md5)) : '');

						break;
					}
					else
					{
						// If the file contains any /: it can be in an media extension subfolder
						if (strpos($file, '/'))
						{
							// Divide the file extracting the extension as the first part before /
							list($extension, $file) = explode('/', $file, 2);

							// If the file yet contains any /: it can be a plugin
							if (strpos($file, '/'))
							{
								// Divide the file extracting the element as the first part before /
								list($element, $file) = explode('/', $file, 2);

								// Try to deal with plugins group in the media folder
								$path = JPATH_ROOT . "/media/$extension/$element/$folder/$file";

								if (file_exists($path))
								{
									$md5        = dirname($path) . '/MD5SUM';
									$includes[] = $rootUri . "/media/$extension/$element/$folder/$file" .
										(file_exists($md5) ? ('?' . file_get_contents($md5)) : '');

									break;
								}

								// Try to deal with classical file in a a media subfolder called element
								$path = JPATH_ROOT . "/media/$extension/$folder/$element/$file";

								if (file_exists($path))
								{
									$md5        = dirname($path) . '/MD5SUM';
									$includes[] = $rootUri . "/media/$extension/$folder/$element/$file" .
										(file_exists($md5) ? ('?' . file_get_contents($md5)) : '');

									break;
								}

								// Try to deal with system files in the template folder
								$path = JPATH_THEMES . "/$template/$folder/system/$element/$file";

								if (file_exists($path))
								{
									$md5        = dirname($path) . '/MD5SUM';
									$includes[] = $rootUri . "/templates/$template/$folder/system/$element/$file" .
										(file_exists($md5) ? ('?' . file_get_contents($md5)) : '');

									break;
								}

								// Try to deal with system files in the media folder
								$path = JPATH_ROOT . "/media/system/$folder/$element/$file";

								if (file_exists($path))
								{
									$md5        = dirname($path) . '/MD5SUM';
									$includes[] = $rootUri . "/media/system/$folder/$element/$file" .
										(file_exists($md5) ? ('?' . file_get_contents($md5)) : '');

									break;
								}
							}
							else
							{
								// Try to deals in the extension media folder
								$path = JPATH_ROOT . "/media/$extension/$folder/$file";

								if (file_exists($path))
								{
									$md5        = dirname($path) . '/MD5SUM';
									$includes[] = $rootUri . "/media/$extension/$folder/$file" .
										(file_exists($md5) ? ('?' . file_get_contents($md5)) : '');

									break;
								}

								// Try to deal with system files in the template folder
								$path = JPATH_THEMES . "/$template/$folder/system/$file";

								if (file_exists($path))
								{
									$md5        = dirname($path) . '/MD5SUM';
									$includes[] = $rootUri . "/templates/$template/$folder/system/$file" .
										(file_exists($md5) ? ('?' . file_get_contents($md5)) : '');

									break;
								}

								// Try to deal with system files in the media folder
								$path = JPATH_ROOT . "/media/system/$folder/$file";

								if (file_exists($path))
								{
									$md5        = dirname($path) . '/MD5SUM';
									$includes[] = $rootUri . "/media/system/$folder/$file" .
										(file_exists($md5) ? ('?' . file_get_contents($md5)) : '');

									break;
								}
							}
						}
						// Try to deal with system files in the media folder
						else
						{
							$path = JPATH_ROOT . "/media/system/$folder/$file";

							if (file_exists($path))
							{
								$md5        = dirname($path) . '/MD5SUM';
								$includes[] = $rootUri . "/media/system/$folder/$file" .
									(file_exists($md5) ? ('?' . file_get_contents($md5)) : '');

								break;
							}
						}
					}
				}
			}

			return $includes;
		}

		// If not relative and http is not present in filename
		foreach ($potential as $strip)
		{
			$files = [];

			// Detect debug mode
			if ($detect_debug && JDEBUG)
			{
				/*
				 * Detect if we received a file in the format name.min.ext
				 * If so, strip the .min part out, otherwise append -uncompressed
				 */
				if (strrpos($strip, '.min', '-4'))
				{
					$position = strrpos($strip, '.min', '-4');
					$filename = str_replace('.min', '.', $strip, $position);
					$files[]  = $filename . $ext;
				}
				else
				{
					$files[] = $strip . '-uncompressed.' . $ext;
				}
			}

			$files[] = $strip . '.' . $ext;

			/*
			 * Loop on 1 or 2 files and break on first found.
			 * Add the content of the MD5SUM file located in the same folder to url to ensure cache browser refresh
			 * This MD5SUM file must represent the signature of the folder content
			 */
			foreach ($files as $file)
			{
				$path = JPATH_ROOT . "/$file";

				if (file_exists($path))
				{
					$md5        = dirname($path) . '/MD5SUM';
					$includes[] = $rootUri . "/$file" . (file_exists($md5) ? ('?' . file_get_contents($md5)) : '');

					break;
				}
			}
		}

		return $includes;
	}
}

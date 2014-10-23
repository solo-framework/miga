<?php
/**
 *
 *
 * PHP version 5
 *
 * @package
 * @author  Andrey Filippov <afi@i-loto.ru>
 */

namespace Miga;

class Autoload
{
	/**
	 * Autoload a Class by it's Class Name
	 *
	 * @param string $className
	 *
	 * @return boolean
	 */
	public function autoLoad($className)
	{
		$className = ltrim($className, '/');
		$postfix = '/' . str_replace(array('_', '\\'), '/', $className . '.php');

		// Change BaseDir according to Namespace
		if (strpos($className, 'Miga\\Migrations\\') === 0)
		{
			$baseDir = getcwd() . '/.miga/migrations';
			$postfix = "/{$className}.php";
			$postfix = substr($postfix, 16);
		}
		else
		{
//			if (strpos($className, 'Command\\') === 0)
//			{
//				$baseDir = getcwd() . '/.meme/commands';
//				$postfix = substr($postfix, 8);
//
//			} else
//			{
				$baseDir = dirname(dirname(__FILE__));
//			}
		}

		//Try to load a normal Miga class. Think that component is compiled to .phar
		$classFileWithinPhar = $baseDir . $postfix;
		if ($this->isReadable($classFileWithinPhar))
		{
			/** @noinspection PhpIncludeInspection */
			require_once $classFileWithinPhar;
			return true;
		}

		//Try to load a custom Task or Class. Notice that the path is absolute to CWD
		$classFileOutsidePhar = getcwd() . '/.miga/migrations' . $postfix;
		if ($this->isReadable($classFileOutsidePhar))
		{
			/** @noinspection PhpIncludeInspection */
			require_once $classFileOutsidePhar;
			return true;
		}

		return false;
	}

	/**
	 * Checks if a file can be read.
	 *
	 * @param string $filePath
	 *
	 * @return boolean
	 */
	public function isReadable($filePath)
	{
		return is_readable($filePath);
	}

}


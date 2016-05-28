<?php

class Autoloader
{
	private $namepaces = [];
	private $includePath;

	/**
	 * Set the path as source of classes.
	 * @param void 
	 */
	public function setIncludePath($path)
	{
		$this->includePath = $path . DIRECTORY_SEPARATOR;
	}

	/**
	 * Load specific class.
	 * @param  string $className
	 * @return void
	 */
	public function load($className)
	{
		$file = $this->includePath . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';

		if ( ! is_file($file))
		{
			exit(sprintf('Nu se poate incarca clasa %s. Asigurati-va ca aceasta exista in biblioteca.', $className));
		}

		require $file;
	}

	/**
	 * Register the Autoloader::load method to the spl_register
	 * @return void
	 */
	public function register()
	{
		spl_autoload_register([$this, 'load']);
	}


	/**
	 * Unregister the Autoloader::load method.
	 * @return void
	 */
	public function unregister()
	{
		spl_autoload_unregister([$this, 'load']);
	}
}
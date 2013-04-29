<?php namespace Sidney\Latchet\Generators;

use Illuminate\Filesystem\Filesystem;

class Generator {

	/**
	 * The filesystem instance.
	 *
	 * @var \Illuminate\Filesystem
	 */
	protected $files;

	/**
	 * Construct
	 *
	 * @param  \Illuminate\Filesystem  $files
	 * @return void
	 */
	public function __construct(Filesystem $files)
	{
		$this->files = $files;
	}

	/**
	 * Copy all the stubs to /socket
	 *
	 * @return void
	 */
	public function make($path)
	{
		if($this->copyFiles($path))
		{
			//all stubs were copied successfuly
			//so we can now edit the routes.php file
			return $this->editRoutesFile();
		}

		return false;
	}

	/**
	 * Copy all the files to /socket
	 *
	 * @param string $path
	 * @return bool
	 */
	protected function copyFiles($path)
	{
		if (!$this->files->isDirectory($path))
		{
			return $this->files->copyDirectory(__DIR__.'/stubs', $path);
		}

		return false;
	}

	/**
	 * Edit the app/routes.php file to register the previously
	 * copied handlers
	 *
	 * @return bool
	 */
	protected function editRoutesFile()
	{
		return $this->files->append(
					app_path().'/routes.php',
					$this->getContent(__DIR__.'/content/routes.php')
				);
	}

	/**
	 * get content of a file
	 *
	 * @param string $path
	 * @return srting
	 */
	protected function getContent($path)
	{
		return PHP_EOL.$this->files->get($path);
	}

}
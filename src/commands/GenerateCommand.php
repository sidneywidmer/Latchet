<?php namespace Sidney\Latchet;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Sidney\Latchet\Generators\Generator;

class GenerateCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'latchet:generate';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create all the necessary files for a successful websocket connection';

	/**
	 * The controller generator instance.
	 *
	 * @var \Sidney\Latchet\Generators\Generator
	 */
	protected $generator;

	/**
	 * The path to the socket directory.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Create a new generator command instance
	 *
	 * @param \Sidney\Latchet\Generators\Generator  $generator
	 * @param string $path
	 * @return void
	 */
	public function __construct(Generator $generator, $path)
	{
		parent::__construct();

		$this->path = $path;
		$this->generator = $generator;
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$this->generate();
	}

	/**
	 * Generate all the necessary files for a websocket connection.
	 * This means a ConnectonHandler and at least a TopicHandler
	 *
	 * @return void
	 */
	protected function generate()
	{
		if (!$this->generator->make($this->path))
		{
			$this->error('Couldn\'t generate all the necessary files because the \'app/socket\' folder already exists.');
		}
		else
		{
			$this->info('All files created successfully!');
		}
	}

}
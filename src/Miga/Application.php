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

class Application extends \Symfony\Component\Console\Application
{
	public function __construct()
	{
		parent::__construct("\nWelcome to Miga database bigration tool");
		$this->setCatchExceptions(true);
		$this->setAutoExit(false);
		$this->setCatchExceptions(false);

		// todo: load commands from dir
		$this->addCommands(array(
//				new Command\Check(),
				new Command\Init(),
//				new Command\Commit(),
//				new Command\Create(),
//				new Command\Diff(),
//				new Command\Log(),
//				new Command\Migrate(),
				new Command\Status()
		));
	}

	public function throwErrorException($errno, $errstr, $errfile, $errline)
	{
		if (!($errno & error_reporting()))
			return false;

		throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
	}
}


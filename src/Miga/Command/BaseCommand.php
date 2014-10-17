<?php
/**
 *
 *
 * PHP version 5
 *
 * @package
 * @author  Andrey Filippov <afi@i-loto.ru>
 */

namespace Miga\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class BaseCommand extends Command
{
	protected $configDir;

	protected function initialize(InputInterface $input, OutputInterface $output)
	{
		$this->configDir = getcwd() . '/.miga';
	}

	public function getConfig()
	{
		return Yaml::parse($this->configDir . "/config.yml");
	}
}


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

use Doctrine\DBAL\DriverManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class BaseCommand extends Command
{
	protected $configDir = null;

	protected $config = null;

	//protected $dbConfig = null;

	protected function initialize(InputInterface $input, OutputInterface $output)
	{
		$this->configDir = getcwd() . '/.miga';
		$this->config =Yaml::parse($this->configDir . "/config.yml");

	}

//	public function getConfig()
//	{
//		return Yaml::parse($this->configDir . "/config.yml");
//	}

	/**
	 * Соединение к БД
	 *
	 * @param $envName
	 *
	 * @return \Doctrine\DBAL\Connection
	 */
	public function getConnection($envName)
	{
		// TODO: оптимизировать создание соединиения
		//$dbConfig = $this->getConfig();
		$dbParams =  $this->config["environments"][$envName];
		return DriverManager::getConnection($dbParams);
	}
}


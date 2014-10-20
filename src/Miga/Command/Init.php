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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Init extends BaseCommand
{


	protected function configure()
	{
		$this->setName("init")
				->setDescription("Initializes migration repository, creates config and migration table")
				->setHelp(sprintf("%Initializes migration repository and creates migration table.%s", PHP_EOL, PHP_EOL));
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln("Creating new Miga project");
		//$env = $i->getArgument('env');


		//print_r($this->getConfig());

		if (is_dir($this->configDir))
		{
			$output->writeln("<info>Project already exists</info>");
			return 1;
		}
		else
		{

			$output->writeln("creatin'");
			mkdir($this->configDir);

			$yml = $this->configDir . "/config.yml";
			touch($yml);

			$cnfDummy = <<<EOT
paths:
    migrations: %%MIGA_CONFIG_DIR%%/migrations
    binlog: "/var/log/mysql"

environments:

    default_migration_table: _miga_migrations
    default_database: dev

    production:
        adapter: mysql
        host: production-host
        name: dbname
        user: root
        password: password
        port: 3306
        charset: utf8

    dev:
        adapter: mysql
        host: localhost
        name: box
        user: root
        pass: 'password'
        port: 3306
        charset: utf8

    test:
        adapter: mysql
        host: localhost
        name: testing_db
        user: root
        pass: ''
        port: 3306
        charset: utf8

EOT;

			file_put_contents($yml, $cnfDummy);

		}

		$output->writeln("<info>Project has been successfully created</info>");
		return 0;
	}
}


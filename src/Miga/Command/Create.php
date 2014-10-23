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

use Miga\Application;
use Miga\MigrationManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Create extends BaseCommand
{
	protected function configure()
	{
		parent::configure();
		$this
				->setName('create')
				->setDescription('Creates a new migration')
				->addOption(
						'env',
						"e",
						InputOption::VALUE_REQUIRED,
						'Name of environment'
				)
			//->addOption("debug", "d", InputOption::VALUE_OPTIONAL, "Run in debug mode")
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$envName = $input->getOption("env");
		if (!$envName)
			throw new \Exception("You should set an environment name");

		$conn = $this->getConnection($envName);
		$mm = new MigrationManager($this->config, $conn);

		$created = $mm->createMigrationTable();
		if ($created)
			$output->writeln("<info>The table for migrations was created</info>");

		$created = $mm->createFileForNewMigration(Application::MIGRATIONS_DIR);
		if (!$created)
			$output->writeln("<info>File for a new migration already exists. You should commit or delete it.</info>");
		else
			$output->writeln("<info>File for a new mirgation successfully created.</info>");
	}
}


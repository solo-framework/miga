<?php
/**
 * Created by PhpStorm.
 * User: afi
 * Date: 23.10.14
 * Time: 22:05
 */

namespace Miga\Command;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Rollback extends BaseCommand
{
	protected function configure()
	{
		$this->setName("rollback")
			->setDescription("Commit a migration")
			->addOption('env', "e",	InputOption::VALUE_REQUIRED, 'Name of environment')
			->addOption("step", "s", InputOption::VALUE_OPTIONAL, "Steps of migrations to rollback")
			->addOption("target", "t", InputOption::VALUE_OPTIONAL, "UID migration to rollback")
			//->addArgument("comment", InputArgument::REQUIRED, "Comment for migration")
			->setHelp(sprintf('%sRollback to migration.%s', PHP_EOL, PHP_EOL));
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$step = $input->getOption("step");
		$target = $input->getOption("target");

		if (!$step && !$target)
			throw new \Exception("You should set migration id or number of steps to rollback");
		print_r($input->getOptions());
	}
}
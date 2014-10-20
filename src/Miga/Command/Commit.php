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

use Miga\MigrationManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Commit extends BaseCommand
{
	protected function configure()
	{
		$this->setName("commit")
				->setDescription("Commit a migration")
				->addOption(
						'env',
						"e",
						InputOption::VALUE_REQUIRED,
						'Name of environment'
				)
				->addArgument("comment", InputArgument::REQUIRED, "Comment for migration")
				->setHelp(sprintf('%sCommit migration.%s', PHP_EOL, PHP_EOL));
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$comment = $input->getArgument("comment");
		$envName = $input->getOption("env");

		$uid = MigrationManager::getCurrentTime();
//
		$output->write("\n<comment>Creating new migraton {$uid}...</comment>");
		$conn = $this->getConnection($envName);

		$conn->beginTransaction();
		try
		{
			// сохранение миграции в БД
			$conn->
		}
		catch (\Exception $e)
		{
			$conn->rollBack();
			throw $e;
		}

//		$this->migrator->entityManager->beginTransaction();
//		try
//		{
//			$this->migrator->exportDump($uid);
//			$this->migrator->putDelta($uid, $comment);
//			$this->migrator->insertMigration($uid, $comment);
//
//			$this->migrator->entityManager->commit();
//		}
//		catch (\Exception $e)
//		{
//			$this->migrator->entityManager->rollback();
//			throw DBMigratorException::create($e);
//		}
		$output->writeln("<info>Done</info>\n");
	}
}


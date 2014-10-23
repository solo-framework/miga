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

use Herrera\Json\Exception\Exception;
use Miga\Application;
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

		$conn = $this->getConnection($envName);
		$uid = MigrationManager::getCurrentTime();
		$mm = new MigrationManager($this->config, $conn);
//
		$output->write("\n<comment>Creating new migraton {$uid}...</comment>");

		if (is_file(Application::NEW_MIGRATION_FILE))
		{
			$php = file_get_contents(Application::NEW_MIGRATION_FILE);

			$clsUid = str_replace(".", "_", $uid);
			$php = str_replace("NewMigration", "Migration_{$clsUid}", $php);

			// вставим служебную информацию
			$serviceData = $mm->generateServiceData($uid, $comment);
			$php = str_replace("//{SERVICE_DATA}", $serviceData, $php);

			file_put_contents(Application::MIGRATIONS_DIR . DIRECTORY_SEPARATOR . "Migration_" . $clsUid . ".php", $php);
			@unlink(Application::NEW_MIGRATION_FILE);

		}
		else
		{
			throw new Exception("Can't find a file with new migration. Run command 'create'");
		}

		$conn->beginTransaction();
		try
		{
			// сохранение миграции в БД
			$mm->insertMigration($uid, $comment);

			$conn->commit();
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


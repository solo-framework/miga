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
use Miga\Migration;
use Miga\MigrationManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use zpt\anno\Annotations;

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

				//->addArgument("comment", InputArgument::REQUIRED, "Comment for migration")
				->setHelp(sprintf('%sCommit migration.%s', PHP_EOL, PHP_EOL));
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		//$comment = $input->getArgument("comment");
		$envName = $input->getOption("env");

		$conn = $this->getConnection($envName);
		$uid = MigrationManager::getCurrentTime();
		$mm = new MigrationManager($this->config, $conn);
//
		$output->write("\n<comment>Creating new migraton {$uid}...</comment>");

		$className = "";
		if (is_file(Application::NEW_MIGRATION_FILE))
		{
			$php = file_get_contents(Application::NEW_MIGRATION_FILE);

			// переименовать класс
			$clsUid = str_replace(".", "_", $uid);
			$php = str_replace("NewMigration", "Migration_{$clsUid}", $php);

			// получить parent из аннотаций
			$rf = new  \ReflectionClass("Miga\\Migrations\\NewMigration");
			$annotations = new Annotations($rf);
//			$rf = new \ReflectionClass("Miga\\Migrations\\NewMigration");
//			$ar = new AnnotationReader();
//			$annotations = $ar->getClassAnnotations($rf);

			// вставим служебную информацию
			$serviceData = $mm->generateServiceData($uid, $annotations["comment"], $annotations["parent"]);
			$php = str_replace("//{SERVICE_DATA}", $serviceData, $php);

			// перезаписать содержимое файла
			$className = "Miga\\Migrations\\Migration_" . $clsUid;
			file_put_contents(Application::MIGRATIONS_DIR . DIRECTORY_SEPARATOR . "Migration_" . $clsUid . ".php", $php);
			@unlink(Application::NEW_MIGRATION_FILE);

		}
		else
		{
			throw new \Exception("Can't find a file with new migration. Run command 'create'");
		}

		$conn->beginTransaction();
		try
		{
			// сохранение миграции в БД
			//$mm->insertMigration($uid, $comment);

//			$className = "Miga\\Migrations\\Migration_1413831508_0655";
//
			/** @var $cls Migration */
			$cls = $mm->getMigrationInstance($className);
			$cls->insertServiceData();
			$mm->setCurrentMigration($uid);

//
//			$rf = new \ReflectionClass($className);
//			$ar = new AnnotationReader();
//			$annotations = $ar->getClassAnnotations($rf);
//			$mm->insertMigration($uid, $comment, $annotations["parent"]);

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


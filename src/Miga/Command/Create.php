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

		$migTable = $this->config["environments"]["default_migration_table"];
		$res = $conn->getSchemaManager()->tablesExist(array($migTable));

		if (!$res)
		{
			$output->writeln("<info>Creating a table for migrations</info>");

			$conn->executeQuery("
				CREATE TABLE `{$migTable}` (
					`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`createTime` DECIMAL(14,4) NOT NULL,
					`comment` VARCHAR(255) NOT NULL,
					PRIMARY KEY (`id`)
				)
				COLLATE='utf8_general_ci'
				ENGINE=InnoDB
				AUTO_INCREMENT=1;
			");
		}

		// создать файл для миграции
		$migrationsDir = Application::MIGRATIONS_DIR;// . $this->config["paths"]["migrations"];
		if (!is_dir($migrationsDir))
		{
			mkdir($migrationsDir);
		}

		$dummyFile = <<<EOT
<?php
namespace Miga\Migrations;


class NewMigration extends Migration
{
    public function up()
    {
		//{UP}
    }

    public function down()
    {
		//{DOWN}
    }

	// DO NOT DELETE THIS!
    //{SERVICE_DATA}
    // DO NOT DELETE THIS!
}
EOT;

		$dummyFilePath = $migrationsDir . DIRECTORY_SEPARATOR . "new_migration.php";

		if (!is_file($dummyFilePath))
		{
			file_put_contents($dummyFilePath, $dummyFile);
		}
		else
		{
			$output->writeln("<info>File with new mirgation already exists. You should commit or delete it.</info>");
		}
	}
}


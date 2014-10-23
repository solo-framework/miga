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

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;

class MigrationManager
{

	/**
	 * @var EntityManager
	 */
	public $entityManager;

	public $dbTool = null;

	protected $config = null;

	private $host = null;
	private $user = null;
	private $password = null;
	private $dbname = null;
	private $migrationTable = null;
	private $migrationPath = null;

	/**
	 * @var \Doctrine\DBAL\Connection
	 */
	protected $connection = null;

	/**
	 * Устанавливает соединение с базой
	 *
	 * @param $config
	 */
	public function __construct($config, Connection $connection)
	{
		$this->config = $config;
		$this->connection = $connection;
		$this->migrationTable = $config["environments"]["default_migration_table"];
		$this->dbname = $connection->getDatabase();


//		$this->host = $config["db"]["host"];
//		$this->user = $config["db"]["user"];
//		$this->password = $config["db"]["password"];
//		$this->dbname = $config["db"]["dbname"];
//
//		$this->migrationTable = $config["migration"]["table"];
//		$this->migrationPath = $config["migration"]["path"];
//
//		$configDB = Setup::createAnnotationMetadataConfiguration(array("Migration.php"), true);
//		$this->entityManager = EntityManager::create($config["db"], $configDB);
//
//		//$this->entityManager->getConfiguration()->setSQLLogger(new EchoSQLLogger());
//
//		$this->dbTool = DBToolFacrory::create($config["db"]);
	}

	public function generateServiceData($uid, $comment, $parent)
	{
		$uid = floatval($uid);
		$comment = $this->connection->quote($comment);
//		if ($parent == "null")
//			$parent = null;

		$code = "\n
			public function insertServiceData()
			{
				\$this->connection->executeQuery(
					\"INSERT INTO `{$this->migrationTable}` (uid, comment, parent) VALUES ({$uid}, {$comment}, {$parent})
				\");
			}
			\n
		";

		return $code;
		//return "";
	}

	/**
	 * Создает таблицу migration
	 *
	 * @return bool
	 */
	public function createMigrationTable()
	{
		$migTable = $this->config["environments"]["default_migration_table"];
		$res = $this->connection->getSchemaManager()->tablesExist(array($migTable));

		if (!$res)
		{

			$this->connection->executeQuery("
				CREATE TABLE `{$migTable}` (
					`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`uid` DECIMAL(14,4) NOT NULL,
					`comment` VARCHAR(255) NOT NULL,
					`isCurrent` TINYINT(1) NOT NULL DEFAULT '0',
					`parent` DECIMAL(14,0) NULL DEFAULT NULL,
					PRIMARY KEY (`id`),
					UNIQUE INDEX `uid_idx` (`uid`)
				)
				COLLATE='utf8_general_ci'
				ENGINE=InnoDB
				AUTO_INCREMENT=1;
			");

			return true;
		}
		else
		{
			return false;
		}
		//throw new \Exception("not implemented");
//		$conn = $this->entityManager->getConnection();
//		if (!$conn->getSchemaManager()->tablesExist(array($this->migrationTable)))
//		{
//			$st = new SchemaTool($this->entityManager);
//
//			$sqls = $st->getCreateSchemaSql(
//					array($this->entityManager->getClassMetadata(__NAMESPACE__ . "\\Migration"))
//			);
//
//			$this->entityManager->getConnection()->executeQuery($sqls[0]);
//		}
	}

	/**
	 * Очищает таблицу migration
	 *
	 * @return void
	 */
	public function emptyMigrationTable()
	{
		$this->connection->getSchemaManager()->dropAndCreateTable($this->migrationTable);
	}

	/**
	 * Удаляет все из базы
	 *
	 * @return void
	 */
	public function emptyDatabase()
	{
		$this->connection->getSchemaManager()->dropAndCreateDatabase($this->dbname);
	}

	/**
	 * Возвращает все миграции
	 *
	 * @param string $order Порядок сортировки
	 *
	 * @return Migration[]
	 */
	public function getAllMigrations($order = "ASC")
	{
		$res = $this->connection->fetchAll("SELECT * FROM `{$this->migrationTable}` ORDER BY `uid` ASC");
		return $res;
		//return $this->getRepository()->findBy(array(), array("createTime" => $order));
	}

	/**
	 * Возвращает последнюю миграцию
	 *
	 * @return Migration
	 */
	public function getLastMigration()
	{
		$res = $this->connection->fetchAll("SELECT * FROM `{$this->migrationTable}` ORDER BY `uid` ASC");
		return array_shift($res);
		//return $this->getRepository()->findOneBy(array(), array("createTime" => "DESC"));
	}

	/**
	 * Возвращает миграцию по времени создания
	 *
	 * @param $time
	 *
	 * @return Migration
	 */
	public function getMigrationByTime($time)
	{
		$res = $this->connection->fetchAll("SELECT * FROM `{$this->migrationTable}` WHERE `createTime` = '{$time}'");
		return array_shift($res);
		//return $this->getRepository()->findOneBy(array("createTime" => $time), array("createTime" => "DESC"));
	}

	/**
	 * Возвращает миграцию id
	 *
	 * @param $id
	 *
	 * @return Migration
	 */
	public function getMigrationById($id)
	{
		return $this->getRepository()->find($id);
	}

	public function getCurrentMigration()
	{
		$res = $this->connection->executeQuery("SELECT * FROM `{$this->migrationTable}` WHERE `isCurrent` = true");
		return $res->fetch();
	}

	public function getMigrationInstance($className)
	{
		$cls = new $className();
		$cls->setConnection($this->connection);
		return $cls;
	}

	public function setCurrentMigration($uid)
	{
		// снять флаг current у других миграций
		$sql = "UPDATE `{$this->migrationTable}` SET isCurrent = false";
		$this->connection->executeQuery($sql);

		// установить как текущую
		$sql = "UPDATE `{$this->migrationTable}` SET isCurrent = true WHERE `uid` = ?";
		$this->connection->executeQuery($sql, array($uid));
	}

	public function insertMigration($uid, $comment, $parent)
	{
		if ($parent == "null")
			$parent = null;
		return $this->connection->insert(
			$this->migrationTable,
			array(
				"uid" => floatval($uid),
				"comment" => $comment,
				"parent" => $parent,
				"isCurrent" => 1
				)
		);

//		$this->connection->executeQuery("INSERT INTO `{$this->migrationTable}` ()", array(floatval($createTime), $comment));
//		$m = new Migration();
//		$m->createTime = floatval($createTime);
//		$m->comment = $comment;
//
//		$this->entityManager->persist($m);
//		$this->entityManager->flush();
	}

	public static function getCurrentTime()
	{
		return number_format(microtime(true), 4, '.', '');
	}

	public function createFileForNewMigration(/*$migrationsDir*/)
	{
		$curr = $this->getCurrentMigration();
		$parent = "null";
		if ($curr)
			$parent = $curr;

//		if (!is_dir($migrationsDir))
//		{
//			mkdir($migrationsDir);
//		}

		$dummyFile = <<<EOT
<?php
namespace Miga\Migrations;

use Miga\Migration;

/**
 * Class NewMigration
 *
 * @parent {PARENT}
 *
 * @comment ...Add here a comment for this migration...
 *
 * @package Miga\Migrations
 */

class NewMigration extends Migration
{
    public function up()
    {
		// Add here code for upgrade DB
    }

    public function down()
    {
		// Add here code for downgrade DB
    }

	// DO NOT DELETE THIS!
    //{SERVICE_DATA}
    // DO NOT DELETE THIS!
}
EOT;

		$dummyFile = str_replace("{PARENT}", $parent, $dummyFile);
		$dummyFilePath = Application::NEW_MIGRATION_FILE;//$migrationsDir . DIRECTORY_SEPARATOR . "new_migration.php";

		if (!is_file($dummyFilePath))
		{
			file_put_contents($dummyFilePath, $dummyFile);
			return true;
		}
		else
		{
			return false;
		}
	}
}


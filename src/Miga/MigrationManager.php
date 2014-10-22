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

	public function generateServiceData($uid, $comment)
	{
		$uid = floatval($uid);
		$comment = $this->connection->quote($comment);

		$code = "\n
			public function insertServiceData()
			{
				\$this->connection->executeQuery(\"INSERT INTO `{$this->migrationTable}` (createTime, comment) VALUES ({$uid}, {$comment})\");
			}
			\n
		";

		return $code;
		//return "";
	}

	/**
	 * Создает таблицу migration
	 *
	 * @return void
	 */
	public function createMigrationTable()
	{
		throw new \Exception("not implemented");
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
		$res = $this->connection->fetchAll("SELECT * FROM `{$this->migrationTable}` ORDER BY `createTime` ASC");
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
		$res = $this->connection->fetchAll("SELECT * FROM `{$this->migrationTable}` ORDER BY `createTime` ASC");
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

	public function getCurrentVersion()
	{
		$res = $this->connection->executeQuery("SELECT * FROM `{$this->migrationTable}` WHERE `isCurrent` = true");
		return $res->fetch();
		//return $this->getRepository()->findOneBy(array("isCurrent" => true))->createTime;
	}

	public function setCurrentVersion($uid)
	{
		$m = $this->getRepository()->findOneBy(array("createTime" => floatval($uid)));
		$m->isCurrent = true;

		$this->entityManager->persist($m);
		$this->entityManager->flush();
	}

//	/**
//	 * @return \Doctrine\ORM\EntityRepository
//	 */
//	private function getRepository()
//	{
//		return $this->entityManager->getRepository(__NAMESPACE__ . "\\Migration");
//	}
//
//	public function executeQuery($sql)
//	{
//		$this->entityManager->getConnection()->executeQuery($sql);
//	}

	public function insertMigration($createTime, $comment)
	{
		return $this->connection->insert($this->migrationTable, array("createTime" => floatval($createTime), "comment" => $comment));

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
}


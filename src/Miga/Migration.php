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

abstract class Migration
{
	/**
	 * @var Connection
	 */
	protected $connection = null;

	/**
	 * Установка соединения к БД
	 *
	 * @param Connection $connection
	 */
	public function setConnection($connection)
	{
		$this->connection = $connection;
	}

	public abstract function up();

	public abstract function down();

	public abstract function insertServiceData();

}


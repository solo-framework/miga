<?php
namespace Miga\Migrations;

use Miga\Migration;

/**
 * Class Migration_1414087242_0925
 *
 * @parent null
 *
 * @comment ...Add here a comment for this migration...
 *
 * @package Miga\Migrations
 */

class Migration_1414087242_0925 extends Migration
{
    public function up()
    {
		// Add here code for upgrade DB
		$this->connection->executeQuery("INSERT INTO `rule` (`rule`, `mask`) VALUES ('blabla.ru', 1)");
    }

    public function down()
    {
		$this->connection->executeQuery("DELETE FROM `rule` WHERE  `rule`='blabla.ru'");
		// Add here code for downgrade DB
    }

	// DO NOT DELETE THIS!
    

			public function insertServiceData()
			{
				$this->connection->executeQuery(
					"INSERT INTO `_miga_migrations` (uid, comment, parent) VALUES (1414087242.0925, '...Add here a comment for this migration...', null)
				");
			}
			

		
    // DO NOT DELETE THIS!
}
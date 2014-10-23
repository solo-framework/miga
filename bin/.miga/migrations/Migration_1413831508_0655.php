<?php
namespace Miga\Migrations;

use Miga\Migration;

/**
 * Class Migration_1413831508_0655
 *
 * @parent 1413831508.0625
 *
 * @comment быва ыва ываыаыыва ssdfd
 *          ыва ываыаруцацу уц
 * @package Miga\Migrations
 */
class Migration_1413831508_0655 extends Migration
{
    public function up()
    {
		//{UP} лялял лялялял
    }

    public function down()
    {
		//{DOWN}
    }

	// DO NOT DELETE THIS!


			public function insertServiceData()
			{
				$this->connection->executeQuery("INSERT INTO `_miga_migrations` (createTime, comment) VALUES (1413831508.0655, 'bla bla bla\' dwdw/ wdw')");
			}



    // DO NOT DELETE THIS!
}
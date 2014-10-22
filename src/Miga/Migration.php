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

abstract class Migration
{
	public abstract function up();

	public abstract function down();

	public abstract function insertServiceData();

}


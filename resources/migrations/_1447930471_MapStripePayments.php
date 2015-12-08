<?php

use Message\Cog\Migration\Adapter\MySQL\Migration;

class _1447930471_MapStripePayments extends Migration
{
	public function up()
	{
		$this->run("INSERT INTO `payment_gateway` (
				SELECT `payment_id`, 'stripe' as `gateway`
				FROM `payment`
				WHERE `method` = 'stripe' 
			);");
	}

	public function down()
	{
		$this->run("DELETE FROM `payment_gateway` WHERE `gateway` = 'stripe';");
	}
}
<?php

namespace Message\Mothership\Sripe\Bootstrap;

use Message\Cog\Bootstrap\ServicesInterface;
use Message\Mothership\Stripe;
use Message\Mothership\Ecommerce\Gateway\Validation;

class Services implements ServicesInterface
{
	public function registerServices($services)
	{
		$services['gateway.adapter.stripe'] = function($c) {

		};

		$services->extends('gateway.collection', function($collection, $c) {
			$collection->add($c['gateway.adapter.stripe']);

			return $collection;
		});

		$services->extend('order.payment.methods', function($methods, $c) {
			$methods->add(new Stripe\Payment\Method\Stripe);

			return $methods;
		});
	}
}

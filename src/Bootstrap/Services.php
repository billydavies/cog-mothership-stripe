<?php

namespace Message\Mothership\Stripe\Bootstrap;

use Message\Cog\Bootstrap\ServicesInterface;
use Message\Mothership\Stripe;
use Message\Mothership\Ecommerce\Gateway\Validation;

class Services implements ServicesInterface
{
	public function registerServices($services)
	{
		$services['gateway.adapter.stripe'] = function($c) {
			return new Stripe\Gateway(
				$c['cfg']->stripe->secretKey,
				$c['cfg']->stripe->publishableKey
			);
		};

		$services->extend('gateway.collection', function($collection, $c) {
			$collection->add($c['gateway.adapter.stripe']);

			return $collection;
		});

		$services->extend('order.payment.methods', function($methods, $c) {
			$methods->add(new Stripe\Payment\Method\Stripe);

			return $methods;
		});

		$services['stripe.checkout.form'] = $services->factory(function ($c) {
			return new \Message\Mothership\Stripe\Form\Checkout;
		});

		$services['stripe.charger'] = function ($c) {
			return new \Message\Mothership\Stripe\Charge\Charge;
		};
	}
}

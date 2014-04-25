<?php

namespace Message\Mothership\Stripe\Bootstrap;

use Message\Cog\Bootstrap\ServicesInterface;
use Message\Mothership\Stripe;
use Message\Mothership\Ecommerce\Gateway\Validation;

/**
 * Class Services
 * @package Message\Mothership\Stripe\Bootstrap
 * @author Thomas Marchant <thomas@message.co.uk>
 */
class Services implements ServicesInterface
{
	public function registerServices($services)
	{
		$services['gateway.adapter.stripe'] = function($c) {
			return new Stripe\Gateway(
				$c['request'],
				$c['stripe.charge.wrapper'],
				$c['log.payments'],
				$c['stripe.secret_key'],
				$c['stripe.publishable_key']
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

		$services['stripe.secret_key'] = $services->factory(function($c) {
			return ($c['cfg']->checkout->payment->useTestPayments) ?
				$c['cfg']->stripe->testSecretKey : $c['cfg']->stripe->liveSecretKey;
		});

		$services['stripe.publishable_key'] = $services->factory(function($c) {
			return ($c['cfg']->checkout->payment->useTestPayments) ?
				$c['cfg']->stripe->testPublishableKey : $c['cfg']->stripe->livePublishableKey;
		});

		$services['stripe.checkout.form'] = $services->factory(function($c) {
			return new \Message\Mothership\Stripe\Form\Checkout;
		});

		$services['stripe.charge.wrapper'] = function($c) {
			return new \Message\Mothership\Stripe\Charge\Wrapper($c['user.current']);
		};
	}
}

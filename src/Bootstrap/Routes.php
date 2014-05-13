<?php

namespace Message\Mothership\Stripe\Bootstrap;

use Message\Cog\Bootstrap\RoutesInterface;

/**
 * Class Routes
 * @package Message\Mothership\Stripe\Bootstrap
 * @author Thomas Marchant <thomas@message.co.uk>
 */
class Routes implements RoutesInterface
{
	public function registerRoutes($router)
	{
		// checkout
		$router['ms.ecom.checkout']->add('ms.ecom.checkout.stripe.card.action', 'payment', 'Message:Mothership:Stripe::Controller:Purchase#purchaseAction')
		->setMethod('POST');
		$router['ms.ecom.checkout']->add('ms.ecom.checkout.stripe.card', 'payment', 'Message:Mothership:Stripe::Controller:Purchase#cardDetails');

		// all other payments
		$router['ms.ecom.stripe']->setPrefix('/stripe');
		$router['ms.ecom.stripe']->add('ms.ecom.stripe.card.action', 'payment', 'Message:Mothership:Stripe::Controller:Purchase#purchaseAction')
			->setMethod('POST');
		$router['ms.ecom.stripe']->add('ms.ecom.stripe.card', 'payment', 'Message:Mothership:Stripe::Controller:Purchase#cardDetails');
	}
}
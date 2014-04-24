<?php

namespace Message\Mothership\Stripe\Bootstrap;

use Message\Cog\Bootstrap\RoutesInterface;

class Routes implements RoutesInterface
{
	public function registerRoutes($router)
	{
		$router['ms.ecom.checkout']->add('ms.ecom.checkout.stripe.card.action', 'payment', 'Message:Mothership:Stripe::Controller:Purchase#purchaseAction')
			->setMethod('POST');
		$router['ms.ecom.checkout']->add('ms.ecom.checkout.stripe.card', 'payment', 'Message:Mothership:Stripe::Controller:Purchase#cardDetails');
	}
}
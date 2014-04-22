<?php

namespace Message\Mothership\Stripe\Bootstrap;

use Message\Cog\Bootstrap\RoutesInterface;

class Routes implements RoutesInterface
{
	public function registerRoutes($router)
	{
		$router['ms.ecom.checkout']->add('ms.ecom.checkout.stripe.card.action', 'card-details', 'Message:Mothership:Stripe::Controller:Purchase#purchaseAction')
			->setMethod('POST');
		$router['ms.ecom.checkout']->add('ms.ecom.checkout.stripe.card', 'card-details', 'Message:Mothership:Stripe::Controller:Purchase#cardDetails');
	}
}
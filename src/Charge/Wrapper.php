<?php

namespace Message\Mothership\Stripe\Charge;

class Wrapper
{
	public function create($amount, $currency, $card)
	{
		return \Stripe_Charge::create([
			'amount'   => $amount,
			'currency' => $currency,
			'card'     => $card,
		]);
	}

	public function refund($reference)
	{
		return \Stripe_Charge::retrieve($reference)->refund();
	}
}
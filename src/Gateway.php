<?php

namespace Message\Mothership\Stripe;

use Message\Mothership\Ecommerce\Gateway\GatewayInterface;

/**
 * Stripe payment gateway class
 *
 * Class Gateway
 * @package Message\Mothership\Stripe
 * @author Thomas Marchant <thomas@message.co.uk>
 */
class Gateway implements GatewayInterface
{
	public function __construct()
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function getName()
	{
		return 'stripe';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPurchaseControllerReference()
	{
		return 'Message:Mothership:Stripe::Controller:Purchase#purchase';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getRefundControllerReference()
	{
		return 'Message:Mothership:Stripe::ControllerRefund#refund';
	}
}
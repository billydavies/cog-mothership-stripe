<?php

namespace Message\Mothership\Stripe\Payment\Method;

use Message\Mothership\Commerce\Order\Entity\Payment\MethodInterface;

/**
 * Stripe payment method
 *
 * Class Stripe
 * @package Message\Mothership\Stripe\Payment\Method
 * @author Thomas Marchant <thomas@message.co.uk>
 */
class Stripe implements MethodInterface
{
	public function getName()
	{
		return 'stripe';
	}

	public function getDisplayName()
	{
		return 'Stripe';
	}
}
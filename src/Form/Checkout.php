<?php

namespace Message\Mothership\Stripe\Form;

use Symfony\Component\Form;
use Symfony\Component\Validator\Constraints;

/**
 * Class Checkout
 * @package Message\Mothership\Stripe\Form
 * @author Thomas Marchant <thomas@message.co.uk>
 */
class Checkout extends Form\AbstractType
{
	public function getName()
	{
		return 'stripe_checkout';
	}
}
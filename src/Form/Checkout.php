<?php

namespace Message\Mothership\Stripe\Form;

use Symfony\Component\Form;
use Symfony\Component\Validator\Constraints;

class Checkout extends Form\AbstractType
{
	public function getName()
	{
		return 'stripe_checkout';
	}
}
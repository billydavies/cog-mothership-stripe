<?php

namespace Message\Mothership\Stripe\Test\Stripe\Form;

use Message\Mothership\Stripe\Form\Checkout;

class CheckoutTest extends \PHPUnit_Framework_TestCase
{
	protected $_form;

	const NAME = 'stripe_checkout';

	public function setUp()
	{
		$this->_form = new Checkout;
	}

	public function testGetName()
	{
		$this->assertSame(
			self::NAME,
			$this->_form->getName()
		);
	}
}
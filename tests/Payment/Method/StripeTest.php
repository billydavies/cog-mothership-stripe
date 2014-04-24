<?php

namespace Message\Mothership\Stripe\Test\Payment\Method;

use Message\Mothership\Stripe\Payment\Method\Stripe;

class StripeTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var Stripe
	 */
	protected $_method;

	const NAME = 'stripe';
	const DISPLAY_NAME = 'Stripe';

	public function setUp()
	{
		$this->_method = new Stripe;
	}

	public function testGetName()
	{
		$this->assertSame(
			self::NAME,
			$this->_method->getName()
		);
	}

	public function testGetDisplayName()
	{
		$this->assertSame(
			self::DISPLAY_NAME,
			$this->_method->getDisplayName()
		);
	}
}
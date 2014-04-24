<?php

namespace Message\Mothership\Stripe\Test;

use Message\Mothership\Stripe\Gateway;

class GatewayTest extends \PHPUnit_Framework_TestCase
{
	protected $_request;

	/**
	 * @var Gateway
	 */
	protected $_gateway;

	protected $_payable;
	protected $_wrapper;
	protected $_logger;
	protected $_charge;

	const NAME = 'stripe';

	const SKEY = 'secret_key';
	const PKEY = 'publishable_key';
	const TOKEN = 'token';

	const P_CONTROLLER = 'Message:Mothership:Stripe::Controller:Purchase#purchase';
	const R_CONTROLLER = 'Message:Mothership:Stripe::Controller:Refund#refund';

	public function setUp()
	{
		$this->_payable = \Mockery::mock('\\Message\\Mothership\\Commerce\\Payable\\PayableInterface');
		$this->_request = \Mockery::mock('\\Message\\Cog\\HTTP\\Request');
		$this->_wrapper = \Mockery::mock('\\Message\\Mothership\\Stripe\\Charge\\Wrapper');
		$this->_logger  = \Mockery::mock('\\Monolog\\Logger');
		$this->_charge  = \Mockery::mock('\\Stripe_Charge');

		$this->_gateway = new \Message\Mothership\Stripe\Gateway(
				$this->_request,
				$this->_wrapper,
				$this->_logger,
				self::SKEY,
				self::PKEY
		);
	}

	public function tearDown()
	{
		\Mockery::close();
	}

	public function testName()
	{
		$this->assertSame(
			self::NAME,
			$this->_gateway->getName()
		);
	}

	public function testGetPurchaseControllerReference()
	{
		$this->assertSame(
			self::P_CONTROLLER,
			$this->_gateway->getPurchaseControllerReference()
		);
	}

	public function testGetRefundControllerReference()
	{
		$this->assertSame(
			self::R_CONTROLLER,
			$this->_gateway->getRefundControllerReference()
		);
	}

	public function testGetPublishableKey()
	{
		$this->assertSame(
			self::PKEY,
			$this->_gateway->getPublishableKey()
		);
	}

	public function testPurchase()
	{
		$this->_payable
			->shouldReceive('getPayableCurrency')
			->twice()
			->andReturn('GBP');

		$this->_payable
			->shouldReceive('getPayableAmount')
			->once()
			->andReturn(100);

		$this->_request
			->shouldReceive('get')
			->once()
			->andReturn(self::TOKEN);

		$this->_wrapper
			->shouldReceive('create')
			->once()
			->andReturn($this->_charge);

		$this->_logger
			->shouldReceive('alert')
			->never();

		$charge = $this->_gateway->purchase($this->_payable);

		$this->assertSame($this->_charge, $charge);
	}

	/**
	 * @expectedException \Stripe_Error
	 */
	public function testPurchaseError()
	{
		$this->_payable
			->shouldReceive('getPayableCurrency')
			->twice()
			->andReturn('GBP');

		$this->_payable
			->shouldReceive('getPayableAmount')
			->once()
			->andReturn(100);

		$this->_request
			->shouldReceive('get')
			->once()
			->andReturn(self::TOKEN);

		$this->_wrapper
			->shouldReceive('create')
			->once()
			->andThrow(new \Stripe_Error(''));

		$this->_logger
			->shouldReceive('alert')
			->once();

		$this->_gateway->purchase($this->_payable);
	}

	public function testRefund()
	{
		$this->_wrapper
			->shouldReceive('refund')
			->once()
			->andReturn($this->_charge);

		$charge = $this->_gateway->refund('test');

		$this->assertSame($this->_charge, $charge);
	}

	/**
	 * @expectedException \Stripe_Error
	 */
	public function testRefundError()
	{
		$this->_wrapper
			->shouldReceive('refund')
			->once()
			->andThrow(new \Stripe_Error(''));

		$this->_logger
			->shouldReceive('alert')
			->once();

		$this->_gateway->refund('test');
	}
}
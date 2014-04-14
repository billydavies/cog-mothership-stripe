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
	protected $_publishableKey;
	protected $_secretKey;

	public function __construct($secretKey, $publishableKey)
	{
		$this->_setSecretKey($secretKey);
		$this->_setPublishableKey($publishableKey);
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

	public function getPublishableKey()
	{
		return $this->_publishableKey;
	}

	protected function _setPublishableKey($publishableKey)
	{
		$this->_publishableKey = $publishableKey;
	}

	protected function _setSecretKey($secretKey)
	{
		\Stripe::setApiKey($secretKey);
		$this->_secretKey = $secretKey;
	}
}
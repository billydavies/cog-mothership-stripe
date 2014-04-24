<?php

namespace Message\Mothership\Stripe;

use Message\Mothership\Stripe\Charge\Wrapper as Charge;
use Message\Cog\HTTP\Request;
use Message\Mothership\Ecommerce\Gateway\GatewayInterface;
use Message\Mothership\Commerce\Payable\PayableInterface;

/**
 * Stripe payment gateway class
 *
 * Class Gateway
 * @package Message\Mothership\Stripe
 * @author Thomas Marchant <thomas@message.co.uk>
 */
class Gateway implements GatewayInterface
{
	// Post data keys returned by Stripe
	const STRIPE_CHECKOUT = 'stripe_checkout';
	const STRIPE_TOKEN    = 'stripeToken';

	/**
	 * @var \Message\Cog\HTTP\Request
	 */
	protected $_request;

	/**
	 * @var \Message\Mothership\Stripe\Charge\Wrapper
	 */
	protected $_charge;

	protected $_publishableKey;
	protected $_secretKey;

	/**
	 * List of ISO codes for currencies that do not use decimals, as Stripe accepts the smallest currency unit
	 * for its amounts, i.e. £1 is 100, and ¥1 is 1
	 *
	 * @var array
	 */
	protected $_zeroDecimalCurrencies = [
		'BIF',
		'CJP',
		'DJF',
		'GNF',
		'JPY',
		'KMF',
		'KRW',
		'MGA',
		'PYG',
		'RWF',
		'VUV',
		'XAF',
		'XOF',
		'XPF',
	];

	public function __construct(Request $request, Charge $chargeWrapper, $secretKey, $publishableKey)
	{
		$this->_request = $request;
		$this->_charge  = $chargeWrapper;

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
		return 'Message:Mothership:Stripe::Controller:Refund#refund';
	}

	public function getPublishableKey()
	{
		return $this->_publishableKey;
	}

	public function purchase(PayableInterface $payable)
	{
		$total = (in_array($payable->getPayableCurrency(), $this->_zeroDecimalCurrencies)) ?
			(int) $payable->getPayableAmount() :
			(int) $payable->getPayableAmount() * 100;

		return $this->_getCharge()->create(
			$total,
			$payable->getPayableCurrency(),
			$this->_getToken()
		);
	}

	public function refund($reference)
	{
		return $this->_getCharge()->refund($reference);
	}

	protected function _getCharge()
	{
		if (!$this->_charge instanceof Charge) {
			throw new \LogicException(
				'$_charge property must be instance of \\Message\\Mothership\\Stripe\\Charge\\Wrapper, ' .
				gettype($this->_charge) . ' given'
			);
		}

		return $this->_charge;
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

	protected function _getToken()
	{
		return $this->_request->get(self::STRIPE_TOKEN);
	}
}
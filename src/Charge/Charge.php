<?php

namespace Message\Mothership\Stripe\Charge;

use Message\Mothership\Commerce\Payable\PayableInterface;

/**
 * Class for handling card charges using Stripe
 *
 * Class Charge
 * @package Message\Mothership\Stripe\Charge
 *
 * @author Thomas Marchant <thomas@message.co.uk>
 */
class Charge
{
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

	public function makePayment(PayableInterface $payable, $token)
	{
		$total = (in_array($payable->getPayableCurrency(), $this->_zeroDecimalCurrencies)) ?
			(int) $payable->getPayableTotal() :
			(int) $payable->getPayableTotal() * 100;

		$charge = \Stripe_Charge::create([
			'amount'   => $total,
			'currency' => $payable->getPayableCurrency(),
			'card'     => $token,
		]);

		return $charge;
	}
}
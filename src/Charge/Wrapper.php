<?php

namespace Message\Mothership\Stripe\Charge;

use Message\User;

/**
 * Class Wrapper
 * @package Message\Mothership\Stripe\Charge
 */
class Wrapper
{
	/**
	 * @var \Message\User\User
	 */
	protected $_user;

	public function __construct(User\UserInterface $user)
	{
		$this->_user = $user;
	}

	public function create($amount, $currency, $card)
	{
		return \Stripe_Charge::create([
			'amount'      => $amount,
			'currency'    => $currency,
			'card'        => $card,
			'description' => $this->_getDescription()
		]);
	}

	public function refund($reference, $amount)
	{
		$amount = (int) $amount;

		return \Stripe_Charge::retrieve($reference)->refund([
			'amount' => $amount
		]);
	}

	protected function _getDescription()
	{
		$description = 'Payment made by ' . $this->_user->getName();
		$description .= ($this->_user instanceof User\User) ?
			' (User ID: ' . $this->_user->id . ', Email: ' . $this->_user->email . ')' :
			' (User unknown)';

		return $description;
	}
}
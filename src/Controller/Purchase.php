<?php

namespace Message\Mothership\Stripe\Controller;

use Message\Cog\Controller\Controller;
use Message\Mothership\Ecommerce\Controller\Gateway\PurchaseControllerInterface;
use Message\Mothership\Commerce\Payable\PayableInterface;

/**
 * Controller for purchases using the Stripe server gateway integration.
 *
 * Class Purchase
 * @package Message\Mothership\Stripe\Controller
 * @author Thomas Marchant <thomas@message.co.uk>
 */
class Purchase extends Controller implements PurchaseControllerInterface
{
	// Session keys for purchase() vars
	const PAYABLE_KEY = 'stripe.checkout.payable';
	const STAGES_KEY  = 'stripe.checkout.stages';
	const OPTIONS_KEY = 'stripe.checkout.options';

	// Post data keys returned by Stripe
	const STRIPE_CHECKOUT = 'stripe_checkout';
	const STRIPE_TOKEN    = 'stripeToken';

	public function purchase(PayableInterface $payable, array $stages, array $options = null)
	{
		$this->get('http.session')->set(self::PAYABLE_KEY, $payable);
		$this->get('http.session')->set(self::STAGES_KEY, $stages);
		$this->get('http.session')->set(self::OPTIONS_KEY, $options);

		return $this->redirect($this->generateUrl('ms.ecom.checkout.stripe.card'));
	}

	public function cardDetails()
	{
		$payable = $this->get('http.session')->get(self::PAYABLE_KEY);

		if (!$payable) {
			throw new \UnexpectedValueException('No payable object found in session');
		}

		return $this->render('Message:Mothership:Stripe::card_details', [
			'form'           => $this->createForm($this->get('stripe.checkout.form')),
			'address'        => $payable->getPayableAddress('billing'),
			'publishableKey' => $this->get('gateway.adapter.stripe')->getPublishableKey(),
		]);
	}

	public function purchaseAction()
	{
		list($payable, $stages, $options) = $this->_getSessionVars();
		$this->get('gateway.adapter.stripe');
		try {
			$charge = $this->get('stripe.charger')->makePayment($payable, $this->_getToken());
			de($charge);
		}
		catch (\Stripe_CardError $e) {
			$this->addFlash('error', $e->getMessage());

			return $this->forward($stages['failure'], ['payable' => $payable]);
		}
	}

	protected function _getSessionVars()
	{
		return [
			$this->get('http.session')->get(self::PAYABLE_KEY),
			$this->get('http.session')->get(self::STAGES_KEY),
			$this->get('http.session')->get(self::OPTIONS_KEY),
		];
	}

	protected function _getToken()
	{
		return $this->get('request')->get(self::STRIPE_TOKEN);
	}
}
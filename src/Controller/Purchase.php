<?php

namespace Message\Mothership\Stripe\Controller;

use Message\Cog\Controller\Controller;
use Message\Mothership\Commerce\Order\Order;
use Message\Mothership\Ecommerce\Controller\Gateway\PurchaseControllerInterface;
use Message\Mothership\Commerce\Payable\PayableInterface;
use Message\Cog\HTTP\Response;

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
	const PAYABLE_KEY = 'stripe.payable';
	const STAGES_KEY  = 'stripe.checkout.stages';
	const OPTIONS_KEY = 'stripe.checkout.options';
	const ADDRESS_KEY = 'stripe.checkout.address';

	public function purchase(PayableInterface $payable, array $stages, array $options = null)
	{
		$this->get('http.session')->set(self::PAYABLE_KEY, $payable);
		$this->get('http.session')->set(self::STAGES_KEY, $stages);
		$this->get('http.session')->set(self::OPTIONS_KEY, $options);

		// Store billing address in session as $payable loses its address loader during the serialization process
		$this->get('http.session')->set(self::ADDRESS_KEY, $payable->getPayableAddress('billing'));

		$route = ($payable instanceof Order) ?
			'ms.ecom.checkout.stripe.card' :
			'ms.ecom.stripe.card';

		return $this->redirectToRoute($route);
	}

	public function cardDetailsCheckout()
	{
		return $this->_cardDetails('Message:Mothership:Stripe::card_details_checkout');
	}

	public function cardDetails()
	{
		return $this->_cardDetails('Message:Mothership:Stripe::card_details');
	}

	public function purchaseAction()
	{
		list($payable, $stages, $options) = $this->_getSessionVars();

		try {
			$charge = $this->get('gateway.adapter.stripe')->purchase($payable);
		}
		catch (\Stripe_CardError $e) {
			$this->addFlash('error', $e->getMessage());

			return $this->_redirectOnFailure($payable, $stages);
		}
		catch (\Stripe_InvalidRequestError $e) {
			$this->addFlash('error', $this->trans('ms.stripe.error.js'));

			return $this->_redirectOnFailure($payable, $stages);
		}
		catch (\Exception $e) {
			$this->addFlash('error', $this->trans('ms.stripe.error.generic'));

			return $this->_redirectOnFailure($payable, $stages);
		}

		$response = $this->forward($stages['success'], [
			'payable'   => $payable,
			'reference' => $charge->id,
			'method'    => $this->get('order.payment.methods')->get('stripe'),
		]);

		$content = $response->getContent();
		$data    = json_decode($content);

		return $this->redirect($data->url);
	}

	protected function _cardDetails($viewPath)
	{
		$payable = $this->get('http.session')->get(self::PAYABLE_KEY);

		if (!$payable instanceof PayableInterface) {
			throw new \UnexpectedValueException(
				'`' . self::PAYABLE_KEY . '` in session must be instance of PayableInterface, ' . gettype($payable) . ' given'
			);
		}

		return $this->render($viewPath, [
			'form'           => $this->createForm($this->get('stripe.checkout.form')),
			'payable'        => $payable,
			'address'        => $this->get('http.session')->get(self::ADDRESS_KEY),
			'publishableKey' => $this->get('gateway.adapter.stripe')->getPublishableKey(),
		]);
	}

	protected function _redirectOnFailure(PayableInterface $payable, $stages)
	{
		$response = $this->forward($stages['failure'], ['payable' => $payable]);

		return $this->redirect($this->generateUrl('ms.ecom.stripe.card'));
	}

	protected function _getSessionVars()
	{
		return [
			$this->get('http.session')->get(self::PAYABLE_KEY),
			$this->get('http.session')->get(self::STAGES_KEY),
			$this->get('http.session')->get(self::OPTIONS_KEY),
		];
	}
}
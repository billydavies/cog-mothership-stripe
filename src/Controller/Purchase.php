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
	public function purchase(PayableInterface $payable, array $stages, array $options = null)
	{
		$form = $this->createForm($this->get('stripe.checkout.form'));

		return $this->render('Message:Mothership:Stripe::checkout', [
			'form'           => $form,
			'publishableKey' => $this->get('gateway.adapter.stripe')->getPublishableKey(),
		]);
	}
}
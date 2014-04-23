<?php

namespace Message\Mothership\Stripe\Controller;

use Message\Cog\Controller\Controller;
use Message\Mothership\Commerce\Payable\PayableInterface;
use Message\Mothership\Ecommerce\Controller\Gateway\RefundControllerInterface;

class Refund extends Controller implements RefundControllerInterface
{
	public function refund(PayableInterface $payable, $reference, array $stages, array $options = null)
	{
		try {
			$charge = $this->get('gateway.adapter.stripe')->refund($reference);
		}
		catch (\Stripe_CardError $e) {
			$this->addFlash('error', $e->getMessage());

			$response = $this->forward($stages['failure'], ['payable' => $payable]);
			
			return $this->redirect($response->getTargetUrl());
		}

		$response = $this->forward($stages['success'], [
			'payable'   => $payable,
			'reference' => $charge->id,
			'stages'    => $stages,
			'method'    => $this->get('order.payment.methods')->get('stripe'),
		]);

		$content = $response->getContent();
		$data    = json_decode($content);

		return $this->redirect($data->url);
	}
}
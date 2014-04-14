<?php

namespace Message\Mothership\Stripe\Controller;

use Message\Cog\Controller\Controller;
use Message\Mothership\Commerce\Payable\PayableInterface;
use Message\Mothership\Ecommerce\Controller\Gateway\RefundControllerInterface;

class Refund extends Controller implements RefundControllerInterface
{
	public function refund(PayableInterface $refund, array $stages, array $options = null)
	{}
}
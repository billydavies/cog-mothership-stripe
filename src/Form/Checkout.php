<?php

namespace Message\Mothership\Stripe\Form;

use Symfony\Component\Form;
use Symfony\Component\Validator\Constraints;

class Checkout extends Form\AbstractType
{
	public function buildForm(Form\FormBuilderInterface $builder, array $options)
	{
//		$builder->add('number', 'text', [
//			'attr' => [
//				'data-stripe' => 'number',
//			],
//			'constraints' => [
//				new Constraints\NotBlank,
//			],
//		]);
//		$builder->add('cvc', 'text', [
//			'attr' => [
//				'data-stripe' => 'cvc',
//			],
//			'constraints' => [
//				new Constraints\Length([
//					'min' => 3,
//					'max' => 3,
//				]),
//			],
//		]);
//		$builder->add('exp-month', 'date', [
//			'attr' => [
//				'data-stripe' => 'exp-month',
//			],
//			'widget' => 'choice',
//		]);
//		$builder->add('exp-year', 'date', [
//			'attr' => [
//				'data-stripe' => 'exp-year',
//			],
//			'widget' => 'choice',
//			'years'  => $this->_getYearRange(),
//		]);
	}

	public function getName()
	{
		return 'stripe_checkout';
	}

	protected function _getYearRange()
	{
		$thisYear     = date("Y");
		$fifteenYears = $thisYear + 15;

		return range($thisYear, $fifteenYears);
	}
}
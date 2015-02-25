# Mothership Stripe

This module provides an integration with the Stripe payment gateway for Mothership, and provides methods for payments and refunds.

## Configuration
Installation creates a `stripe.yml` file in the config file. By default, it will look like this:

    secret-key: sk_test_BQokikJOvBiI2HlWgH4olfQ2
    publishable-key: pk_test_6pRNASCoBOKtIshFeQd4XMUh

The default values are those provided by the Stripe documentation to ensure usage in test mode. As a result of this, the `use-test-payments` config setting makes no difference to Stripe's functionality, and it should be automatically set to test mode as soon as it is installed.
Any attempts to use real cards while in test mode will result in a validation error, so no orders can be created if it goes live without setting the keys for the live environment.

The `secret-key` is a server-side id key for Stripe, and will never be seen by the customer.
The `publishable-key` is a client-side id key for Stripe, which appears in the JavaScript used to connect to Stripe.

To set Stripe as your active gateway, update the service container with:

```php
$services['gateway'] = function($c) {
    return $c['gateway.collection']->get('stripe');
};
```

## API Process
The Stripe gateway never leaves Mothership, and instead uses JavaScript and PHP classes to connect to the API without a redirect being necessary. The process looks like this:

1. When the customer continues to payment, the `Purchase#purchase` controller stores the necessary data in the session, and redirects to `Purchase#cardDetails`.
1. This controller renders a form in the `resources/view/card_details.html.twig` file. While this form uses Symfony to ensure it has the CSRF protection, it is not used for creating the form elements. The reason for this is that Symfony does not support having nameless form elements, but this is required by Stripe's API to stop sensitive data being posted.
1. When the form is submitted, the form data is validated by the `https://js.stripe.com/v2/` file. Any issues with regards to formatting (for instance if the card number is invalid) will display an error message in the `.payment-errors` span.
1. If the form is valid, it creates a `card` object using this data and the billing address data on the order and sends it to Stripe.
1. Stripe returns a single use token for the card and appends a hidden field to the form with the token as the value, and then submits the form.
1. Once the form is submitted, it redirects to the `Purchase#purchaseAction` controller. This called the `purchase` method on the `Gateway` class, and if it catches a `Stripe_CardError`, it redirects to the failure url.
1. The `Gateway::purchase()` method returns an instance of `Stripe_Charge`, which represents the charge made to the card, and the Stripe-generated ID is set as the payment reference in the database. This can then be used to refund the payment using the `Stripe_Charge::refund()` method.

## Notes

* As noted in the API process above, the checkout form does not use Symfony Form to generate the fields. This is because the card data cannot be posted for security reasons. The form fields also have a `data-stripe` attribute. These are not actually used in our implementation of the gateway, as we want to add address details too, but if we wanted to, they allow for a simpler way to grab the form data.
* Stripe accepts payment amounts in the lowest possible currency unit, so instead of submitting £1, we need to submit 100p. This is a little awkward as some currencies, such as Yen, are zero decimal currency and can't be broken down in the same way. The `Gateway` class has a protected property defining all zero decimal currency codes (according to Stripe's documentation). If a currency is not a zero decimal currency, the payment amount is multiplied by 100 and cast to an integer. As far as I'm aware there aren't any currencies that break down differently.


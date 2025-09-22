<?php

namespace Botble\PayPal\Providers;

use Botble\Base\Facades\Html;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Facades\PaymentMethods;
use Botble\PayPal\Forms\PaypalPaymentMethodForm;
use Botble\PayPal\Services\Gateways\PayPalPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // PayPal is completely disabled - all filters are commented out
        
        // add_filter(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, [$this, 'registerPayPalMethod'], 2, 2);
        // $this->app->booted(function (): void {
        //     add_filter(PAYMENT_FILTER_AFTER_POST_CHECKOUT, [$this, 'checkoutWithPayPal'], 2, 2);
        // });
        // add_filter(PAYMENT_METHODS_SETTINGS_PAGE, [$this, 'addPaymentSettings'], 2);
        // add_filter(BASE_FILTER_ENUM_ARRAY, function ($values, $class) {
        //     if ($class == PaymentMethodEnum::class) {
        //         $values['PAYPAL'] = PAYPAL_PAYMENT_METHOD_NAME;
        //     }
        //     return $values;
        // }, 2, 2);
        // add_filter(BASE_FILTER_ENUM_LABEL, function ($value, $class) {
        //     if ($class == PaymentMethodEnum::class && $value == PAYPAL_PAYMENT_METHOD_NAME) {
        //         $value = 'PayPal';
        //     }
        //     return $value;
        // }, 2, 2);
        // add_filter(BASE_FILTER_ENUM_HTML, function ($value, $class) {
        //     if ($class == PaymentMethodEnum::class && $value == PAYPAL_PAYMENT_METHOD_NAME) {
        //         $value = Html::tag(
        //             'span',
        //             PaymentMethodEnum::getLabel($value),
        //             ['class' => 'label-success status-label']
        //         )->toHtml();
        //     }
        //     return $value;
        // }, 2, 2);
        // add_filter(PAYMENT_FILTER_GET_SERVICE_CLASS, function ($data, $value) {
        //     if ($value == PAYPAL_PAYMENT_METHOD_NAME) {
        //         $data = PayPalPaymentService::class;
        //     }
        //     return $data;
        // }, 2, 2);
        // add_filter(PAYMENT_FILTER_PAYMENT_INFO_DETAIL, function ($data, $payment) {
        //     if ($payment->payment_channel == PAYPAL_PAYMENT_METHOD_NAME) {
        //         $paymentDetail = (new PayPalPaymentService())->getPaymentDetails($payment->charge_id);
        //         $data = view('plugins/paypal::detail', ['payment' => $paymentDetail])->render();
        //     }
        //     return $data;
        // }, 2, 2);
    }

    // PayPal is disabled - all methods are commented out
    // public function addPaymentSettings(?string $settings): string
    // public function registerPayPalMethod(?string $html, array $data): string
    // public function checkoutWithPayPal(array $data, Request $request): array
}
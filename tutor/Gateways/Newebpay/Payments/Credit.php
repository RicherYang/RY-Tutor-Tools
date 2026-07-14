<?php

namespace RY\Tutor\Gateways\Newebpay\Payments;

defined('ABSPATH') or exit;

use Ollyo\PaymentHub\Core\Payment\BasePayment;
use RY\Tutor\Gateways\Newebpay\PaymentTrait;

final class Credit extends BasePayment
{
    public const PAYMENT_TYPE = 'CREDIT';

    use PaymentTrait;

    public function check(): bool
    {
        return true;
    }

    public function setup(): void {}
}

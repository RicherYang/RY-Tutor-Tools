<?php

namespace RY\Tutor\Tutor\Gateways\Payuni\Payment;

defined('ABSPATH') or exit;

use Ollyo\PaymentHub\Core\Payment\BasePayment;
use RY\Tutor\Tutor\Gateways\Payuni\PaymentTrait;

final class Credit extends BasePayment
{
    public const PAYMENT_TYPE = 'Credit';

    use PaymentTrait;

    public function setup(): void {}

    public function check(): bool
    {
        return true;
    }
}

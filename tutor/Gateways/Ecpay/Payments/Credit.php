<?php

namespace RY\Tutor\Gateways\Ecpay\Payments;

defined('ABSPATH') or exit;

use Ollyo\PaymentHub\Core\Payment\BasePayment;
use RY\Tutor\Gateways\Ecpay\PaymentTrait;

final class Credit extends BasePayment
{
    public const PAYMENT_TYPE = 'Credit';

    use PaymentTrait;

    public function check(): bool
    {
        return true;
    }

    public function setup(): void
    {
        $this->add_testmode_filter();
    }
}

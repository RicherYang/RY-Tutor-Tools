<?php

namespace RY\Tutor\Tutor\Gateways\Smilepay\Payments;

defined('ABSPATH') or exit;

use Ollyo\PaymentHub\Core\Payment\BasePayment;
use RY\Tutor\Tutor\Gateways\Smilepay\PaymentTrait;

final class Credit extends BasePayment
{
    public const PAYMENT_TYPE = '1';

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

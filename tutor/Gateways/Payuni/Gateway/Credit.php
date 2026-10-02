<?php

namespace RY\Tutor\Tutor\Gateways\Payuni\Gateway;

defined('ABSPATH') or exit;

use RY\Tutor\Tutor\Gateways\Payuni\Payment\Credit as Payment;
use Tutor\PaymentGateways\GatewayBase;

final class Credit extends GatewayBase
{
    private $dir_name = 'Payments';

    private $config_class = CreditConfig::class;

    private $payment_class = Payment::class;

    public function get_root_dir_name(): string
    {
        return $this->dir_name;
    }

    public function get_payment_class(): string
    {
        return $this->payment_class;
    }

    public function get_config_class(): string
    {
        return $this->config_class;
    }

    public static function get_autoload_file()
    {
        return '';
    }
}

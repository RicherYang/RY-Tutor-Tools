<?php

namespace RY\Tutor\Tutor\Gateways\Ecpay;

defined('ABSPATH') or exit;

use RY\Tutor\Tutor\Gateways\Ecpay\Payments\Credit;
use Tutor\PaymentGateways\GatewayBase;

final class GatewayCredit extends GatewayBase
{
    private $dir_name = 'Payments';

    private $config_class = GatewayCreditConfig::class;

    private $payment_class = Credit::class;

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

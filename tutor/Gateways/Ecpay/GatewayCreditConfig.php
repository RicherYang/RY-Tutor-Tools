<?php

namespace RY\Tutor\Tutor\Gateways\Ecpay;

defined('ABSPATH') or exit;

use RY\Tutor\Tutor\Gateways\Config;
use Tutor\PaymentGateways\Configs\PaymentUrlsTrait;

final class GatewayCreditConfig extends Config
{
    protected $name = 'ry_ecpay_credit';

    private $title = '';

    public function __construct()
    {
        parent::__construct();

        foreach (['title'] as $key) {
            $this->$key = $this->get_field_value($this->settings, $key);
        }
    }

    use PaymentUrlsTrait;

    public function is_configured()
    {
        if (!tutor_utils()->get_option('RY_ecpay_testmode', false)) {
            return !empty(tutor_utils()->get_option('RY_ecpay_MerchantID', ''))
                && !empty(tutor_utils()->get_option('RY_ecpay_HashKey', ''))
                && !empty(tutor_utils()->get_option('RY_ecpay_HashIV', ''));
        }

        return true;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}

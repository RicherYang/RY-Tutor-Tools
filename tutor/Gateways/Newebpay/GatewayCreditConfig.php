<?php

namespace RY\Tutor\Gateways\Newebpay;

defined('ABSPATH') or exit;

use RY\Tutor\Gateways\Config;
use Tutor\PaymentGateways\Configs\PaymentUrlsTrait;

final class GatewayCreditConfig extends Config
{
    protected $name = 'ry_newebpay_credit';

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
        return !empty(tutor_utils()->get_option('RY_newebpay_MerchantID', ''))
            && !empty(tutor_utils()->get_option('RY_newebpay_HashKey', ''))
            && !empty(tutor_utils()->get_option('RY_newebpay_HashIV', ''));
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}

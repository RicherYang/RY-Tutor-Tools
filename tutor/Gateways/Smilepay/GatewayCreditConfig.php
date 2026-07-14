<?php

namespace RY\Tutor\Gateways\Smilepay;

defined('ABSPATH') or exit;

use RY\Tutor\Gateways\Config;
use Tutor\PaymentGateways\Configs\PaymentUrlsTrait;

final class GatewayCreditConfig extends Config
{
    protected $name = 'ry_smilepay_credit';

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
        if (!tutor_utils()->get_option('RY_smilepay_testmode', false)) {
            return !empty(tutor_utils()->get_option('RY_smilepay_Dcvc', ''))
                && !empty(tutor_utils()->get_option('RY_smilepay_Rvg2c', ''))
                && !empty(tutor_utils()->get_option('RY_smilepay_Verifykey', ''))
                && !empty(tutor_utils()->get_option('RY_smilepay_Rotcheck', ''));
        }

        return true;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}

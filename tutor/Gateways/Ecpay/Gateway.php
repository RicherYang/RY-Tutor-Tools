<?php

namespace RY\Tutor\Tutor\Gateways\Ecpay;

defined('ABSPATH') or exit;

use RY\General\V20260729\Logs;

final class Gateway
{
    public const LOG_HANDLE = 'ecpay-tutor-api';

    private static ?self $_instance = null;

    public static function instance(): Gateway
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
            self::$_instance->do_init();
        }

        return self::$_instance;
    }

    protected function do_init(): void
    {
        add_filter('tutor_payment_gateways_with_class', [$this, 'add_method']);
        add_filter('tutor_payment_gateways', [$this, 'add_method_setting'], 100);
        add_filter('tutor_payment_method_labels', [$this, 'add_method_label']);

        Logs::set_log(tutor_utils()->get_option('RY_ecpay_log', false), self::LOG_HANDLE);
    }

    public function add_method($methods)
    {
        $methods['ry_ecpay_credit'] = [
            'gateway_class' => GatewayCredit::class,
            'config_class' => GatewayCreditConfig::class,
        ];

        return $methods;
    }

    public function add_method_setting($settings)
    {
        $settings[] = [
            'name' => 'ry_ecpay_credit',
            'label' => _x('ECPay Credit', 'gateway label', 'ry-tutor-tools'),
            'is_installed' => true,
            'is_active' => false,
            'icon' => RY_TFTUTOR_PLUGIN_URL . 'assets/icons/ecpay.png',
            'support_subscription' => false,
            'fields' => [
                [
                    'name' => 'title',
                    'type' => 'text',
                    'label' => __('Title', 'ry-tutor-tools'),
                    'value' => _x('ECPay Credit', 'gateway label', 'ry-tutor-tools'),
                ],
            ],
        ];

        return $settings;
    }

    public function add_method_label($labels)
    {
        $labels['ry_ecpay_credit'] = _x('ECPay Credit', 'gateway label', 'ry-tutor-tools');

        return $labels;
    }
}

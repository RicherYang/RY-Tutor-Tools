<?php

namespace RY\Tutor\Tutor\Gateways\Newebpay;

defined('ABSPATH') or exit;

use RY\General\V20260801\Logs;

final class Gateway
{
    public const LOG_HANDLE = 'newebpay-tutor-api';

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
        add_filter('ry-plugin/log_enabled', [$this, 'set_log_enabled'], 10, 2);

        add_filter('tutor_payment_gateways_with_class', [$this, 'add_method']);
        add_filter('tutor_payment_gateways', [$this, 'add_method_setting'], 100);
        add_filter('tutor_payment_method_labels', [$this, 'add_method_label']);
    }

    public function set_log_enabled(bool $enabled, string $handle): bool
    {
        if ($handle === self::LOG_HANDLE) {
            return tutor_utils()->get_option('RY_ecpay_log', false);
        }

        return $enabled;
    }

    public function add_method($methods)
    {
        $methods['ry_newebpay_credit'] = [
            'gateway_class' => GatewayCredit::class,
            'config_class' => GatewayCreditConfig::class,
        ];

        return $methods;
    }

    public function add_method_setting($settings)
    {
        $settings[] = [
            'name' => 'ry_newebpay_credit',
            'label' => _x('NewebPay Credit', 'gateway label', 'ry-tutor-tools'),
            'is_installed' => true,
            'is_active' => false,
            'icon' => RY_TFTUTOR_PLUGIN_URL . 'assets/icons/newebpay.png',
            'support_subscription' => false,
            'fields' => [
                [
                    'name' => 'title',
                    'type' => 'text',
                    'label' => __('Title', 'ry-tutor-tools'),
                    'value' => _x('NewebPay Credit', 'gateway label', 'ry-tutor-tools'),
                ],
            ],
        ];

        return $settings;
    }

    public function add_method_label($labels)
    {
        $labels['ry_newebpay_credit'] = _x('NewebPay Credit', 'gateway label', 'ry-tutor-tools');

        return $labels;
    }
}

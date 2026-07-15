<?php

namespace RY\Tutor\Gateways\Smilepay;

defined('ABSPATH') or exit;

use RY\General\Logs;
use RY\Tutor\Gateways\Smilepay\Gateway;

final class Api extends AbstractsApi
{
    private static ?self $_instance = null;

    private array $api_test_url = [
        'checkout' => 'https://ssl.smse.com.tw/ezpos/mtmk_utf.asp',
    ];

    private array $api_url = [
        'checkout' => 'https://ssl.smse.com.tw/ezpos/mtmk_utf.asp',
    ];

    public static function instance(): self
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
            self::$_instance->do_init();
        }

        return self::$_instance;
    }

    protected function do_init(): void {}

    public function checkout_form($payment_data, $config, $payment)
    {
        $item_name = $this->get_item_name(tutor_utils()->get_option('RY_general_itemname', ''), (array) $payment_data->items);
        $item_name = mb_substr($item_name, 0, 40);

        $data = [
            'Dcvc' => tutor_utils()->get_option('RY_smilepay_Dcvc', ''),
            'Rvg2c' => tutor_utils()->get_option('RY_smilepay_Rvg2c', ''),
            'Od_sob' => $item_name,
            'Data_id' => $this->generate_trade_no($payment_data->order_id, tutor_utils()->get_option('RY_general_prefix', '')),
            'Amount' => (int) ceil($payment_data->total_price),
            'Email' => $payment_data->billing_address->email,
            'Roturl' => $config->get('webhook_url'),
            'Roturl_status' => 'RY_SmilePay',
        ];

        switch (get_locale()) {
            case 'zh_HK':
            case 'zh_TW':
                break;
            case 'en_US':
            case 'en_AU':
            case 'en_CA':
            case 'en_GB':
            default:
                $args['Language'] = 'EN';
                break;
        }

        $data = $this->add_type_info($data, $payment);

        if (tutor_utils()->get_option('RY_smilepay_testmode', false)) {
            $url = $this->api_test_url['checkout'];
        } else {
            $url = $this->api_url['checkout'];
        }

        Logs::log(Gateway::LOG_HANDLE, 'info', 'Checkout #' . $payment_data->order_id, $data);
        do_action('ry_smilepay_gateway_checkout', $data, $payment_data);

        $method = 'post';
        $redirect_url = $url;
        $redirect_data = $data;
        include RY_TFTUTOR_PLUGIN_DIR . 'includes/auto-redirect.php';
    }

    protected function add_type_info(array $data, $payment): array
    {
        if (defined(get_class($payment) . '::PAYMENT_TYPE')) {
            $data['Pay_zg'] = $payment::PAYMENT_TYPE;
        }

        return $data;
    }
}

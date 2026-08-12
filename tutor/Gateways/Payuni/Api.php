<?php

namespace RY\Tutor\Tutor\Gateways\Payuni;

defined('ABSPATH') or exit;

use RY\General\V20260810\Logs;

final class Api extends AbstractsApi
{
    private static ?self $_instance = null;

    private array $api_test_url = [
        'checkout' => 'https://sandbox-api.payuni.com.tw/api/upp',
    ];

    private array $api_url = [
        'checkout' => 'https://api.payuni.com.tw/api/upp',
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
            'MerID' => tutor_utils()->get_option('RY_payuni_MerID', ''),
            'MerTradeNo' => $this->generate_trade_no($payment_data->order_id, tutor_utils()->get_option('RY_general_prefix', '')),
            'TradeAmt' => (int) ceil($payment_data->total_price),
            'Timestamp' => new \DateTime('now', new \DateTimeZone('Asia/Taipei')),
            'ReturnURL' => $config->get('success_url'),
            'NotifyURL' => $config->get('webhook_url'),
            'BackURL' => $config->get('cancel_url'),
            'UsrMail' => $payment_data->billing_address->email,
            'UsrMailFix' => 1,
            'ProdDesc' => $item_name,
        ];
        $data['Timestamp'] = $data['Timestamp']->getTimestamp();
        switch (get_locale()) {
            case 'zh_HK':
            case 'zh_TW':
                break;
            case 'en_US':
            case 'en_AU':
            case 'en_CA':
            case 'en_GB':
            default:
                $data['Lang'] = 'en';
                break;
        }

        $data = $this->add_type_info($data, $payment);
        $args = $this->build_args($data, '2.0');

        if (tutor_utils()->get_option('RY_payuni_testmode', false)) {
            $url = $this->api_test_url['checkout'];
        } else {
            $url = $this->api_url['checkout'];
        }

        Logs::log(Gateway::LOG_HANDLE, 'info', 'Checkout #' . $payment_data->order_id, $data);
        do_action('ry_payuni_gateway_checkout', $data, $payment_data);

        $method = 'post';
        $redirect_url = $url;
        $redirect_data = $args;
        include RY_TFTUTOR_PLUGIN_DIR . 'includes/auto-redirect.php';
    }

    protected function add_type_info(array $data, $payment): array
    {
        if (defined(get_class($payment) . '::PAYMENT_TYPE')) {
            $data[$payment::PAYMENT_TYPE] = 1;
        }

        return $data;
    }

    protected function build_args(array $data, string $version): array
    {
        $args = [
            'MerID' => tutor_utils()->get_option('RY_payuni_MerID', ''),
            'EncryptInfo' => $this->data_encrypt($data),
            'Version' => $version,
        ];
        $args['HashInfo'] = $this->generate_hash_value($args['EncryptInfo']);

        return $args;
    }
}

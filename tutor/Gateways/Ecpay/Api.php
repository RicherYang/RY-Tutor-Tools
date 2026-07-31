<?php

namespace RY\Tutor\Tutor\Gateways\Ecpay;

defined('ABSPATH') or exit;

use RY\General\V20260729\Logs;

final class Api extends AbstractsApi
{
    private static ?self $_instance = null;

    private array $api_test_url = [
        'checkout' => 'https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V5',
    ];

    private array $api_url = [
        'checkout' => 'https://payment.ecpay.com.tw/Cashier/AioCheckOut/V5',
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
            'MerchantID' => tutor_utils()->get_option('RY_ecpay_MerchantID', ''),
            'MerchantTradeNo' => $this->generate_trade_no($payment_data->order_id, tutor_utils()->get_option('RY_general_prefix', '')),
            'MerchantTradeDate' => new \DateTime('now', new \DateTimeZone('Asia/Taipei')),
            'PaymentType' => 'aio',
            'TotalAmount' => (int) ceil($payment_data->total_price),
            'TradeDesc' => get_bloginfo('name'),
            'ItemName' => $item_name,
            'ReturnURL' => $config->get('webhook_url'),
            'OrderResultURL' => $config->get('success_url'),
            'NeedExtraPaidInfo' => 'Y',
            'EncryptType' => 1,
            'PaymentInfoURL' => $config->get('webhook_url'),
        ];
        $data['TradeDesc'] = preg_replace('/[\x{21}-\x{2f}\x{3a}-\x{40}\x{5b}-\x{60}\x{7b}-\x{7e}]/', ' ', $data['TradeDesc']);
        $data['TradeDesc'] = mb_substr($data['TradeDesc'], 0, 100);
        $data['MerchantTradeDate'] = $data['MerchantTradeDate']->format('Y/m/d H:i:s');

        switch (get_locale()) {
            case 'zh_HK':
            case 'zh_TW':
                break;
            case 'ko_KR':
                $data['Language'] = 'KOR';
                break;
            case 'ja':
                $data['Language'] = 'JPN';
                break;
            case 'en_US':
            case 'en_AU':
            case 'en_CA':
            case 'en_GB':
            default:
                $data['Language'] = 'ENG';
                break;
        }

        $data = $this->add_type_info($data, $payment);
        $data['CheckMacValue'] = $this->generate_hash_value($data);

        if (tutor_utils()->get_option('RY_ecpay_testmode', false)) {
            $url = $this->api_test_url['checkout'];
        } else {
            $url = $this->api_url['checkout'];
        }

        Logs::log(Gateway::LOG_HANDLE, 'info', 'Checkout #' . $payment_data->order_id, $data);
        do_action('ry_ecpay_gateway_checkout', $data, $payment_data);

        $method = 'post';
        $redirect_url = $url;
        $redirect_data = $data;
        include RY_TFTUTOR_PLUGIN_DIR . 'includes/auto-redirect.php';
    }

    protected function add_type_info(array $data, $payment): array
    {
        if (defined(get_class($payment) . '::PAYMENT_TYPE')) {
            $data['ChoosePayment'] = $payment::PAYMENT_TYPE;

            switch ($payment::PAYMENT_TYPE) {
                case 'All':
                    $data['ChoosePayment'] = 'ALL';
                    $data['IgnorePayment'] = ['ATM', 'CVS', 'BARCODE', 'BNPL', 'WeiXin'];
                    $data['IgnorePayment'] = implode('#', $data['IgnorePayment']);
                    break;
                case 'Credit':
                    $data['IgnorePayment'] = 'DigitalPayment';
                    break;
            }
        }

        return $data;
    }
}

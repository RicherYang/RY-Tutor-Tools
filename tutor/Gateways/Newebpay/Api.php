<?php

namespace RY\Tutor\Tutor\Gateways\Newebpay;

defined('ABSPATH') or exit;

use RY\General\V20260729\Logs;

final class Api extends AbstractsApi
{
    private static ?self $_instance = null;

    private array $api_test_url = [
        'checkout' => 'https://ccore.newebpay.com/MPG/mpg_gateway',
    ];

    private array $api_url = [
        'checkout' => 'https://core.newebpay.com/MPG/mpg_gateway',
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
            'MerchantID' => tutor_utils()->get_option('RY_newebpay_MerchantID', ''),
            'RespondType' => 'JSON',
            'TimeStamp' => new \DateTime('now', new \DateTimeZone('Asia/Taipei')),
            'Version' => '2.3',
            'MerchantOrderNo' => $this->generate_trade_no($payment_data->order_id, tutor_utils()->get_option('RY_general_prefix', '')),
            'Amt' => (int) ceil($payment_data->total_price),
            'ItemDesc' => $item_name,
            'ReturnURL' => $config->get('success_url'),
            'NotifyURL' => $config->get('webhook_url'),
            'CustomerURL' => $config->get('success_url'),
            'Email' => $payment_data->billing_address->email,
            'EmailModify' => 0,
            'CREDIT' => 0,
            'APPLEPAY' => 0,
            'ANDROIDPAY' => 0,
            'SAMSUNGPAY' => 0,
            'LINEPAY' => 0,
            'AFTEE' => 0,
            'InstFlag' => 0,
            'CreditRed' => 0,
            'UNIONPAY' => 0,
            'CREDITAE' => 0,
            'WEBATM' => 0,
            'VACC' => 0,
            'CVS' => 0,
            'BARCODE' => 0,
            'ESUNWALLET' => 0,
            'TAIWANPAY' => 0,
            'BITOPAY' => 0,
            'TWQR' => 0,
            'EZPWECHAT' => 0,
            'EZPALIPAY' => 0,
            'CVSCOM' => 0,
        ];
        $data['TimeStamp'] = $data['TimeStamp']->getTimestamp();

        switch (get_locale()) {
            case 'zh_HK':
            case 'zh_TW':
                break;
            case 'ja':
                $data['LangType'] = 'jp';
                break;
            case 'en_US':
            case 'en_AU':
            case 'en_CA':
            case 'en_GB':
            default:
                $data['LangType'] = 'en';
                break;
        }

        $data = $this->add_type_info($data, $payment);
        $args = $this->build_args($data, '2.3');

        if (tutor_utils()->get_option('RY_newebpay_testmode', false)) {
            $url = $this->api_test_url['checkout'];
        } else {
            $url = $this->api_url['checkout'];
        }

        Logs::log(Gateway::LOG_HANDLE, 'info', 'Checkout #' . $payment_data->order_id, $data);
        do_action('ry_newebpay_gateway_checkout', $data, $payment_data);

        $method = 'post';
        $redirect_url = $url;
        $redirect_data = $args;
        include RY_TFTUTOR_PLUGIN_DIR . 'includes/auto-redirect.php';
    }

    protected function add_type_info(array $data, $payment): array
    {
        if (defined(get_class($payment) . '::PAYMENT_TYPE')) {
            if (isset($data[$payment::PAYMENT_TYPE])) {
                $data[$payment::PAYMENT_TYPE] = 1;
            }

            switch ($payment::PAYMENT_TYPE) {
                case 'ALL':
                    $data['CREDIT'] = 1;
                    $data['APPLEPAY'] = 1;
                    $data['ANDROIDPAY'] = 1;
                    $data['SAMSUNGPAY'] = 1;
                    $data['LINEPAY'] = 1;
                    $data['CREDITAE'] = 1;
                    $data['ESUNWALLET'] = 1;
                    $data['TAIWANPAY'] = 1;
                    $data['TWQR'] = 1;
                    break;
            }
        }

        return $data;
    }

    protected function build_args(array $data, string $version): array
    {
        $args = [
            'MerchantID' => tutor_utils()->get_option('RY_newebpay_MerchantID', ''),
            'TradeInfo' => $this->data_encrypt($data),
            'Version' => $version,
            'EncryptType' => 0,
        ];
        $args['TradeSha'] = $this->generate_hash_value($args['TradeInfo']);

        return $args;
    }
}

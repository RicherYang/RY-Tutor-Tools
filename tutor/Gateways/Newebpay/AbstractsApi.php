<?php

namespace RY\Tutor\Gateways\Newebpay;

defined('ABSPATH') or exit;

use RY\Tutor\AbstractsApi as Base_AbstractsApi;

abstract class AbstractsApi extends Base_AbstractsApi
{
    protected function generate_trade_no($order_ID, $prefix = ''): string
    {
        $trade_no = $this->order_no_to_trade_no($order_ID, $prefix);
        $trade_no = apply_filters('ry_newebpay_trade_no', $trade_no, $order_ID);
        return substr($trade_no, 0, 18);
    }

    protected function get_order_id($ipn_info, $prefix = '')
    {
        if (isset($ipn_info['Result']['MerchantOrderNo'])) {
            $order_ID = $this->trade_no_to_order_no($ipn_info['Result']['MerchantOrderNo'], $prefix);
            $order_ID = (int) apply_filters('ry_newebpay_trade_no_to_order_id', $order_ID, $ipn_info['Result']['MerchantOrderNo']);
            if ($order_ID > 0) {
                return $order_ID;
            }
        }
        return false;
    }

    protected function generate_hash_value(string $string)
    {
        $HashKey = tutor_utils()->get_option('RY_newebpay_HashKey', '');
        $HashIV = tutor_utils()->get_option('RY_newebpay_HashIV', '');

        $string = 'HashKey=' . $HashKey . '&' . $string . '&HashIV=' . $HashIV;
        return strtoupper(hash('sha256', $string));
    }

    protected function data_encrypt(array $args): string
    {
        $HashKey = tutor_utils()->get_option('RY_newebpay_HashKey', '');
        $HashIV = tutor_utils()->get_option('RY_newebpay_HashIV', '');

        $args_string = http_build_query($args);
        $encrypt_string = @openssl_encrypt($args_string, 'aes-256-cbc', $HashKey, OPENSSL_RAW_DATA, $HashIV);
        return bin2hex($encrypt_string);
    }

    protected function data_decrypt(string $string): string|false
    {
        $HashKey = tutor_utils()->get_option('RY_newebpay_HashKey', '');
        $HashIV = tutor_utils()->get_option('RY_newebpay_HashIV', '');

        $string = hex2bin($string);
        $decrypt_string = openssl_decrypt($string, 'aes-256-cbc', $HashKey, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $HashIV);

        $slast = ord(substr($decrypt_string, -1));
        $slastc = chr($slast);
        if (preg_match("/$slastc{" . $slast . '}/', $decrypt_string)) {
            return substr($decrypt_string, 0, strlen($decrypt_string) - $slast);
        }
        return false;
    }
}

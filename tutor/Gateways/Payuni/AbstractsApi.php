<?php

namespace RY\Tutor\Tutor\Gateways\Payuni;

defined('ABSPATH') or exit;

use RY\Tutor\Tutor\AbstractsApi as BaseAbstractsApi;

abstract class AbstractsApi extends BaseAbstractsApi
{
    protected function generate_trade_no($order_ID, $prefix = ''): string
    {
        $trade_no = $this->order_no_to_trade_no($order_ID, $prefix);
        $trade_no = apply_filters('ry_payuni_trade_no', $trade_no, $order_ID);
        return substr($trade_no, 0, 18);
    }

    protected function get_order_id($ipn_info, $prefix = '')
    {
        if (isset($ipn_info['MerTradeNo'])) {
            $order_ID = $this->trade_no_to_order_no($ipn_info['MerTradeNo'], $prefix);
            $order_ID = (int) apply_filters('ry_payuni_trade_no_to_order_id', $order_ID, $ipn_info['MerTradeNo']);
            if ($order_ID > 0) {
                return $order_ID;
            }
        }
        return false;
    }

    protected function generate_hash_value(string $string): string
    {
        $HashKey = tutor_utils()->get_option('RY_payuni_HashKey', '');
        $HashIV = tutor_utils()->get_option('RY_payuni_HashIV', '');

        $string = $HashKey . $string . $HashIV;
        return strtoupper(hash('sha256', $string));
    }

    protected function data_encrypt(array $args): string
    {
        $HashKey = tutor_utils()->get_option('RY_payuni_HashKey', '');
        $HashIV = tutor_utils()->get_option('RY_payuni_HashIV', '');

        $tag = '';
        $encrypted = @openssl_encrypt(http_build_query($args), 'aes-256-gcm', $HashKey, 0, $HashIV, $tag);
        return trim(bin2hex($encrypted . ':::' . base64_encode($tag)));
    }

    protected function data_decrypt(string $string): string|false
    {
        $HashKey = tutor_utils()->get_option('RY_payuni_HashKey', '');
        $HashIV = tutor_utils()->get_option('RY_payuni_HashIV', '');

        $string = hex2bin($string);
        if (str_contains($string, ':::')) {
            list($encryptData, $tag) = explode(':::', $string, 2);
            return openssl_decrypt($encryptData, 'aes-256-gcm', $HashKey, 0, $HashIV, base64_decode($tag));
        }
        return false;
    }
}

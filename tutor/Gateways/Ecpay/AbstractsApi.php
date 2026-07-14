<?php

namespace RY\Tutor\Gateways\Ecpay;

defined('ABSPATH') or exit;

use RY\Tutor\AbstractsApi as Base_AbstractsApi;

abstract class AbstractsApi extends Base_AbstractsApi
{
    protected function generate_trade_no($order_ID, $prefix = ''): string
    {
        $trade_no = $this->order_no_to_trade_no($order_ID, $prefix);
        $trade_no = apply_filters('ry_ecpay_trade_no', $trade_no, $order_ID);
        return substr($trade_no, 0, 18);
    }

    protected function get_order_id($ipn_info, $prefix = '')
    {
        if (isset($ipn_info['MerchantTradeNo'])) {
            $order_ID = $this->trade_no_to_order_no($ipn_info['MerchantTradeNo'], $prefix);
            $order_ID = (int) apply_filters('ry_ecpay_trade_no_to_order_id', $order_ID, $ipn_info['MerchantTradeNo']);
            if ($order_ID > 0) {
                return $order_ID;
            }
        }
        return false;
    }

    protected function generate_hash_value(array $args): string
    {
        $HashKey = tutor_utils()->get_option('RY_ecpay_HashKey', '');
        $HashIV = tutor_utils()->get_option('RY_ecpay_HashIV', '');

        unset($args['CheckMacValue']);
        ksort($args, SORT_STRING | SORT_FLAG_CASE);

        $args_string = [];
        $args_string[] = 'HashKey=' . $HashKey;
        foreach ($args as $key => $value) {
            $args_string[] = $key . '=' . $value;
        }
        $args_string[] = 'HashIV=' . $HashIV;

        $args_string = $this->urlencode(implode('&', $args_string));
        $check_value = hash('sha256', strtolower($args_string));
        return strtoupper($check_value);
    }

    protected function urlencode($string): string
    {
        return str_replace(
            ['%2D', '%2d', '%5F', '%5f', '%2E', '%2e', '%2A', '%2a', '%21', '%28', '%29'],
            ['-', '-', '_', '_', '.', '.', '*', '*', '!', '(', ')'],
            urlencode($string),
        );
    }
}

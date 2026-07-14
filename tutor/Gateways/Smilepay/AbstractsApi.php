<?php

namespace RY\Tutor\Gateways\Smilepay;

defined('ABSPATH') or exit;

use RY\Tutor\AbstractsApi as Base_AbstractsApi;

abstract class AbstractsApi extends Base_AbstractsApi
{
    public function die_success($res)
    {
        if (property_exists($res, 'redirectUrl')) {
            wp_safe_redirect($res->redirectUrl);
        }
        exit('<Roturlstatus>RY_SmilePay</Roturlstatus>');
    }

    protected function generate_trade_no($order_ID, $prefix = ''): string
    {
        $trade_no = $this->order_no_to_trade_no($order_ID, $prefix);
        $trade_no = apply_filters('ry_smilepay_trade_no', $trade_no, $order_ID);
        return substr($trade_no, 0, 18);
    }

    protected function get_order_id($ipn_info, $prefix = '')
    {
        if (isset($ipn_info['Data_id'])) {
            $order_ID = $this->trade_no_to_order_no($ipn_info['Data_id'], $prefix);
            $order_ID = (int) apply_filters('ry_smilepay_trade_no_to_order_id', $order_ID, $ipn_info['Data_id']);
            if ($order_ID > 0) {
                return $order_ID;
            }
        }
        return false;
    }

    protected function generate_hash_value(array $args): string
    {
        $check_array = [
            tutor_utils()->get_option('RY_smilepay_Rotcheck', ''),
            $args['Purchamt'] ?? '',
            $args['Smseid'] ?? '',
        ];
        $check_array[0] = str_pad($check_array[0], 4, '0', STR_PAD_LEFT);
        $check_array[1] = str_pad($check_array[1], 8, '0', STR_PAD_LEFT);
        $check_array[2] = str_pad(substr($check_array[2], -4), 4, '9', STR_PAD_LEFT);
        $check_array[2] = preg_replace('/[^\d]/s', '9', $check_array[2]);

        $check_string = implode('', $check_array);
        $strlen = strlen($check_string);
        $odd = $even = 0;
        for ($i = 0; $i < $strlen; ++$i) {
            if (0 === $i % 2) {
                $even = $even + (int) $check_string[$i];
            }
            if (1 === $i % 2) {
                $odd = $odd + (int) $check_string[$i];
            }
        }

        return $even * 9 + $odd * 3;
    }
}

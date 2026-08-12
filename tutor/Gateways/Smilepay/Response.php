<?php

namespace RY\Tutor\Tutor\Gateways\Smilepay;

defined('ABSPATH') or exit;

use RY\General\V20260810\Logs;

final class Response extends AbstractsApi
{
    private static ?self $_instance = null;

    public static function instance(): Response
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
            self::$_instance->do_init();
        }

        return self::$_instance;
    }

    protected function do_init(): void {}

    public function check_payload_data($ipn_info)
    {
        if (is_array($ipn_info) && !empty($ipn_info)) {
            $check_value = $ipn_info['Mid_smilepay'] ?? false;
            if ($check_value) {
                $ipn_info_check_value = $this->generate_hash_value($ipn_info);
                if (hash_equals($check_value, $ipn_info_check_value)) {
                    return true;
                }

                Logs::log(Gateway::LOG_HANDLE, 'error', 'IPN request check failed', ['response' => $check_value, 'self' => $ipn_info_check_value]);
            }
        }
        return false;
    }

    public function get_data(array $ipn_info): ?array
    {
        $info_value = $ipn_info;
        if (function_exists('mb_convert_encoding')) {
            foreach ($info_value as $key => $value) {
                $info_value[$key] = mb_convert_encoding($value, 'UTF-8', 'BIG-5');
            }
        }
        $info_value['order_id'] = $this->get_order_id($info_value, tutor_utils()->get_option('RY_general_prefix', ''));
        Logs::log(Gateway::LOG_HANDLE, 'info', 'IPN request', ['data' => $info_value]);

        return $info_value;
    }
}

<?php

namespace RY\Tutor\Gateways\Payuni;

defined('ABSPATH') or exit;

use RY\Tutor\Gateways\Payuni\Gateway;

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
            $check_value = $ipn_info['HashInfo'] ?? false;
            if ($check_value) {
                $info_value = $ipn_info['EncryptInfo'] ?? '';
                $ipn_info_check_value = $this->generate_hash_value($info_value);
                if (hash_equals($check_value, $ipn_info_check_value)) {
                    return true;
                }

                \RY_Logs::log(Gateway::LOG_HANDLE, 'error', 'IPN request check failed', ['response' => $check_value, 'self' => $ipn_info_check_value]);
            }
        }
        return false;
    }

    public function get_data(array $ipn_info): ?array
    {
        $info_value = $ipn_info['EncryptInfo'] ?? '';
        $info_value = $this->data_decrypt($info_value);
        if ($info_value) {
            parse_str($info_value, $info_value);
            if (is_array($info_value) && !empty($info_value)) {
                \RY_Logs::log(Gateway::LOG_HANDLE, 'info', 'IPN request', ['data' => $info_value]);
                $info_value['order_id'] = $this->get_order_id($info_value, tutor_utils()->get_option('RY_general_prefix', ''));
                return $info_value;
            }
        }
        \RY_Logs::log(Gateway::LOG_HANDLE, 'error', 'IPN request decrypt failed', ['data' => $ipn_info]);

        return null;
    }
}

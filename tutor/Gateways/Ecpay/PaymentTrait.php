<?php

namespace RY\Tutor\Gateways\Ecpay;

defined('ABSPATH') or exit;

use GuzzleHttp\Exception\RequestException;
use Ollyo\PaymentHub\Core\Support\System;
use RY\Tutor\Gateways\Ecpay\Api;
use RY\Tutor\Gateways\Ecpay\Response;

trait PaymentTrait
{
    protected function add_testmode_filter(): void
    {
        add_filter('RY_ecpay_MerchantID', [$this, 'change_test_MerchantID']);
        add_filter('RY_ecpay_HashKey', [$this, 'change_test_HashKey']);
        add_filter('RY_ecpay_HashIV', [$this, 'change_test_HashIV']);
    }

    public function change_test_MerchantID($value)
    {
        if (tutor_utils()->get_option('RY_ecpay_testmode', false)) {
            return '3002607';
        }

        return $value;
    }

    public function change_test_HashKey($value)
    {
        if (tutor_utils()->get_option('RY_ecpay_testmode', false)) {
            return 'pwFHCqoQZGmho4w6';
        }

        return $value;
    }

    public function change_test_HashIV($value)
    {
        if (tutor_utils()->get_option('RY_ecpay_testmode', false)) {
            return 'EkRm7iFT261dpevs';
        }

        return $value;
    }

    public function createPayment()
    {
        try {
            $payment_data = $this->getData();

            Api::instance()->checkout_form($payment_data, $this->config, $this);
            exit;
        } catch (RequestException $error) {
            throw new \ErrorException($error->getResponse()->getBody()); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
        }
    }

    public function verifyAndCreateOrderData(object $payload): object
    {
        $post_data = $payload->post;

        try {
            $response = Response::instance();
            $response->set_do_die();
            if (!$response->check_payload_data($post_data)) {
                return new \stdClass();
            }

            $response_data = $response->get_data($post_data);
            if ($response_data && $response_data['order_id']) {
                $returnData = System::defaultOrderData();
                $returnData->id = $response_data['order_id'];
                $returnData->transaction_id = $response_data['TradeNo'];
                $returnData->payment_status = 'failed';
                $returnData->payment_method = $this->config->get('name');

                if ($response_data['RtnCode'] == '1') {
                    $returnData->payment_status = 'paid';
                } else {
                    $returnData->payment_error_reason = $response_data['RtnMsg'];
                }

                return $returnData;
            }

            return new \stdClass();
        } catch (\Throwable $error) {
            throw $error;
        }
    }
}

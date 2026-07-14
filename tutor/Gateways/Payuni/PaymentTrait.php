<?php

namespace RY\Tutor\Gateways\Payuni;

defined('ABSPATH') or exit;

use GuzzleHttp\Exception\RequestException;
use Ollyo\PaymentHub\Core\Support\System;
use RY\Tutor\Gateways\Payuni\Api;
use RY\Tutor\Gateways\Payuni\Response;

trait PaymentTrait
{
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

                if ($response_data['Status'] === 'SUCCESS') {
                    if ($response_data['TradeStatus'] == 1) {
                        $returnData->payment_status = 'paid';
                    }
                } else {
                    $returnData->payment_error_reason = $response_data['Message'];
                }

                return $returnData;
            }

            return new \stdClass();
        } catch (\Throwable $error) {
            throw $error;
        }
    }
}

<?php

namespace RY\Tutor\Tutor\Gateways\Smilepay;

defined('ABSPATH') or exit;

use GuzzleHttp\Exception\RequestException;
use Ollyo\PaymentHub\Core\Support\System;
use Ollyo\PaymentHub\Core\Support\Uri;

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
                $returnData->transaction_id = $response_data['Smseid'];
                $returnData->payment_status = 'failed';
                $returnData->payment_method = $this->config->get('name');
                $successUrl = Uri::getInstance($this->config->get('success_url'));
                $successUrl->setVar('order_id', $returnData->id);
                $returnData->redirectUrl = $successUrl->toString();

                if ($response_data['Response_id'] == '1') {
                    $returnData->payment_status = 'paid';
                } else {
                    $returnData->payment_error_reason = $response_data['Errdesc'];
                }

                return $returnData;
            }

            return new \stdClass();
        } catch (\Throwable $error) {
            throw $error;
        }
    }
}

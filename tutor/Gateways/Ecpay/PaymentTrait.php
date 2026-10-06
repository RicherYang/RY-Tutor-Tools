<?php

namespace RY\Tutor\Tutor\Gateways\Ecpay;

defined('ABSPATH') or exit;

use GuzzleHttp\Exception\RequestException;
use Ollyo\PaymentHub\Core\Support\System;
use Tutor\Models\OrderActivitiesModel;
use Tutor\Models\OrderModel;

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
                $order = OrderModel::get_order($response_data['order_id']);
                $activity_model = new OrderActivitiesModel();

                $returnData = System::defaultOrderData();
                $returnData->id = $order->id;
                $returnData->transaction_id = $response_data['TradeNo'];
                $returnData->payment_method = $this->config->get('name');

                if ($response_data['RtnCode'] == '1') {
                    $returnData->payment_status = OrderModel::PAYMENT_PAID;

                    $payload = new \stdClass();
                    $payload->order_id = $order->id;
                    $payload->meta_key = OrderActivitiesModel::META_KEY_COMMENT;
                    $payload->meta_value = __('ECPay payment completed', 'ry-tutor-tools');
                    $activity_model->add_order_meta($payload);
                } else {
                    $returnData->payment_status = OrderModel::PAYMENT_FAILED;
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

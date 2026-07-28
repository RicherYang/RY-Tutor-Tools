<?php

namespace RY\Tutor\Tutor\Gateways;

defined('ABSPATH') or exit;

use Ollyo\PaymentHub\Core\Payment\BaseConfig;
use Tutor\Ecommerce\Settings;

class Config extends BaseConfig
{
    protected $settings = [];

    public function __construct()
    {
        parent::__construct();

        $this->settings = Settings::get_payment_gateway_settings($this->name);
        add_filter('tutor_ecommerce/checkout', [$this, 'checkout_html']);
    }

    public function checkout_html($html = '')
    {
        $title = $this->getTitle();
        if (!empty($title)) {
            $html = str_replace($this->settings['label'], $title, $html);
        }

        return $html;
    }
}

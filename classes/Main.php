<?php

namespace RY\Tutor;

defined('ABSPATH') or exit;

use RY\General\V20260729\AbstractBasic;
use RY\Tutor\Admin\Admin;
use RY\Tutor\Tutor\Admin\Settings;
use RY\Tutor\Tutor\Country;
use RY\Tutor\Tutor\Gateways\Ecpay\Gateway as EcpayGateway;
use RY\Tutor\Tutor\Gateways\Newebpay\Gateway as NewebpayGateway;
use RY\Tutor\Tutor\Gateways\Payuni\Gateway as PayuniGateway;
use RY\Tutor\Tutor\Gateways\Smilepay\Gateway as SmilepayGateway;

final class Main extends AbstractBasic
{
    public const PREFIX = 'RY_TFTUTOR_';

    public const PLUGIN_NAME = 'RY Tools for Tutor LMS';

    public const MIN_TUTOR_VERSION = '4.0.0';

    private static ?self $_instance = null;

    public static function instance(): Main
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
            self::$_instance->do_init();
        }

        return self::$_instance;
    }

    protected function do_init(): void
    {
        load_plugin_textdomain('ry-tutor-tools', false, plugin_basename(dirname(__DIR__)) . '/languages');

        if (is_admin()) {
            Update::update();
        }

        add_action('init', [$this, 'do_wp_init'], 9);
    }

    public function do_wp_init(): void
    {
        Updater::instance();

        if (is_admin()) {
            Admin::instance();
        }

        if (did_action('tutor_loaded')) {
            if (version_compare(TUTOR_VERSION, self::MIN_TUTOR_VERSION, '<')) {
                return;
            }

            if (License::instance()->is_activated()) {
                Country::instance();

                if (tutor_utils()->is_monetize_by_tutor()) {
                    if (tutor_utils()->get_option('RY_enabled_ecpay', false)) {
                        EcpayGateway::instance();
                    }

                    if (tutor_utils()->get_option('RY_enabled_newebpay', false)) {
                        NewebpayGateway::instance();
                    }

                    if (tutor_utils()->get_option('RY_enabled_payuni', false)) {
                        PayuniGateway::instance();
                    }

                    if (tutor_utils()->get_option('RY_enabled_smilepay', false)) {
                        SmilepayGateway::instance();
                    }
                }

                if (is_admin()) {
                    Settings::instance();
                }
            }
        }
    }

    public static function usage_tracking(): void
    {
        if (get_option('RY_General_tracking', 'yes') !== 'yes') {
            return;
        }

        LinkServer::instance()->send_tracking();
    }

    public static function plugin_activation(): void {}

    public static function plugin_deactivation(): void
    {
        wp_unschedule_hook(self::get_prefix_name('check_expire'));
        wp_unschedule_hook(self::get_prefix_name('check_update'));
    }
}

<?php

defined('ABSPATH') or exit;

use RY\General\AbstractBasic;

final class RY_TFTUTOR extends AbstractBasic
{
    public const OPTION_PREFIX = 'RY_TFTUTOR_';

    public const PLUGIN_NAME = 'RY Tools for Tutor LMS';

    public const MIN_TUTOR_VERSION = '4.0.0';

    private static ?self $_instance = null;

    public static function instance(): RY_TFTUTOR
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
            include_once RY_TFTUTOR_PLUGIN_DIR . 'includes/update.php';
            RY_TFTUTOR_Update::update();
        }

        add_action('init', [$this, 'do_wp_init'], 9);
    }

    public function do_wp_init(): void
    {
        include_once RY_TFTUTOR_PLUGIN_DIR . 'includes/license.php';
        include_once RY_TFTUTOR_PLUGIN_DIR . 'includes/link-server.php';
        include_once RY_TFTUTOR_PLUGIN_DIR . 'includes/updater.php';
        RY_TFTUTOR_Updater::instance();

        if (is_admin()) {
            include_once RY_TFTUTOR_PLUGIN_DIR . 'admin/admin.php';
            RY_TFTUTOR_Admin::instance();
        }

        if (did_action('tutor_loaded')) {
            if (version_compare(TUTOR_VERSION, self::MIN_TUTOR_VERSION, '<')) {
                return;
            }

            if (RY_TFTUTOR_License::instance()->is_activated()) {
                RY\Tutor\Main::instance();

                if (tutor_utils()->is_monetize_by_tutor()) {
                    if (tutor_utils()->get_option('RY_enabled_ecpay', false)) {
                        RY\Tutor\Gateways\Ecpay\Gateway::instance();
                    }

                    if (tutor_utils()->get_option('RY_enabled_newebpay', false)) {
                        RY\Tutor\Gateways\Newebpay\Gateway::instance();
                    }

                    if (tutor_utils()->get_option('RY_enabled_payuni', false)) {
                        RY\Tutor\Gateways\Payuni\Gateway::instance();
                    }

                    if (tutor_utils()->get_option('RY_enabled_smilepay', false)) {
                        RY\Tutor\Gateways\Smilepay\Gateway::instance();
                    }
                }

                if (is_admin()) {
                    RY\Tutor\Admin\Settings::instance();
                }
            }
        }
    }

    public static function plugin_activation() {}

    public static function plugin_deactivation()
    {
        wp_unschedule_hook(self::OPTION_PREFIX . 'check_expire');
        wp_unschedule_hook(self::OPTION_PREFIX . 'check_update');
    }
}

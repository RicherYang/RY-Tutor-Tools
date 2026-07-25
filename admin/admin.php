<?php

defined('ABSPATH') or exit;

use RY\Paid\V20260724\AbstractAdmin;

final class RY_TFTUTOR_Admin extends AbstractAdmin
{
    private static ?self $_instance = null;

    protected RY_TFTUTOR_License $license;

    public static function instance(): RY_TFTUTOR_Admin
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
            self::$_instance->do_init();
        }

        return self::$_instance;
    }

    protected function do_init(): void
    {
        parent::do_init();

        add_action('admin_notices', [$this, 'need_tutor']);

        $this->license = RY_TFTUTOR_License::instance();
        add_filter('ry-plugin/license_list', [$this, 'add_license']);

        if ($this->license->is_activated()) {
            $this->license->check_expire_cron();
        }
    }

    public function need_tutor(): void
    {
        if (!defined('TUTOR_VERSION') || version_compare(TUTOR_VERSION, RY_TFTUTOR::MIN_TUTOR_VERSION, '<')) {
            $message = sprintf(
                /* translators: %1$s: Name of this plugin %2$s: min require version */
                __('<strong>%1$s</strong> is inactive. It require Tutor LMS version %2$s or newer.', 'ry-tutor-tools'),
                __('RY Tools for Tutor LMS', 'ry-tutor-tools'),
                RY_TFTUTOR::MIN_TUTOR_VERSION,
            );
            printf('<div class="error"><p>%s</p></div>', wp_kses($message, ['strong' => []]));
        }
    }

    public function add_license(array $license_list): array
    {
        $license_list[RY_TFTUTOR_PLUGIN_BASENAME] = [
            'name' => $this->license::$main_class::PLUGIN_NAME,
            'license' => $this->license,
            'version' => RY_TFTUTOR_VERSION,
            'basename' => RY_TFTUTOR_PLUGIN_BASENAME,
        ];

        return $license_list;
    }
}

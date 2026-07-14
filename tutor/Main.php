<?php

namespace RY\Tutor;

defined('ABSPATH') or exit;

final class Main
{
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
        if (tutor_utils()->get_option('RY_general_only_tw', false)) {
            add_filter('pre_do_shortcode_tag', [$this, 'change_country_json'], 10, 2);
            add_filter('tutor_should_load_checkout_page', [$this, 'change_country_json']);
        }
    }

    public function change_country_json($status, $tag = null)
    {
        if ($tag !== null) {
            if ($tag !== 'tutor_checkout') {
                return $status;
            }
        }
        $plugin_file = RY_TFTUTOR_PLUGIN_DIR . 'assets/json/countries.json';
        $tutor_file = trailingslashit(tutor()->path) . 'assets/json/countries.json';
        if (file_exists($plugin_file)) {
            if (file_exists($tutor_file)) {
                if (md5_file($tutor_file) !== md5_file($plugin_file)) {
                    @copy($plugin_file, $tutor_file);
                }
            } else {
                @copy($plugin_file, $tutor_file);
            }
        }

        return $status;
    }
}

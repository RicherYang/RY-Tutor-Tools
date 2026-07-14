<?php

defined('ABSPATH') or exit;

final class RY_TFTUTOR_Update
{
    public static function update()
    {
        $now_version = RY_TFTUTOR::get_option('version', '0.0.0');

        if (RY_TFTUTOR_VERSION === $now_version) {
            return;
        }

        if ($now_version === '0.0.0') {
            RY_TFTUTOR::update_option('version', RY_TFTUTOR_VERSION, true);
            return;
        }

        if (version_compare($now_version, '2026.7.14', '<')) {
            RY_TFTUTOR::update_option('version', '2026.7.14', true);
        }
    }
}

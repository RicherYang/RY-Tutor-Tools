<?php

namespace RY\Tutor;

defined('ABSPATH') or exit;

use RY\General\V20260810\Logs;

final class Update
{
    public static function update()
    {
        $now_version = Main::get_option('version', '0.0.0');

        if (RY_TFTUTOR_VERSION === $now_version) {
            return;
        }

        if ($now_version === '0.0.0') {
            Main::update_option('version', RY_TFTUTOR_VERSION, true);
            return;
        }

        if (version_compare($now_version, '2026.10.2', '<')) {
            Main::update_option('version', '2026.10.2', true);
        }
    }
}

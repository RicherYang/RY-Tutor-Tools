<?php

namespace RY\Tutor;

defined('ABSPATH') or exit;

use RY\Paid\V20260729\AbstractLinkServer;

final class LinkServer extends AbstractLinkServer
{
    private static ?self $_instance = null;

    protected string $plugin_slug = 'ry-tutor-tools';

    public static function instance(): LinkServer
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    protected function get_base_info(): array
    {
        return [
            'plugin' => RY_TFTUTOR_VERSION,
        ];
    }
}

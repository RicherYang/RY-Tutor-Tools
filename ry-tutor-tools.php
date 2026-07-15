<?php

/**
 * Plugin Name: RY Tools for Tutor LMS
 * Plugin URI: https://ry-plugin.com/ry-tutor-tools
 * Description: Tutor LMS payment tools
 * Version: 2026.7.14
 * Requires at least: 6.8
 * Requires PHP: 8.2
 * Requires Plugins: tutor
 * Author: Richer Yang
 * Author URI: https://richer.tw/
 * License: GPLv3
 *
 * Text Domain: ry-tutor-tools
 * Domain Path: /languages
 */

defined('ABSPATH') or exit;

define('RY_TFTUTOR_VERSION', ' 2026.7.14');
define('RY_TFTUTOR_PLUGIN_URL', plugin_dir_url(__FILE__));
define('RY_TFTUTOR_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('RY_TFTUTOR_PLUGIN_BASENAME', plugin_basename(__FILE__));

require_once RY_TFTUTOR_PLUGIN_DIR . 'includes/vendor/autoload.php';
require_once RY_TFTUTOR_PLUGIN_DIR . 'includes/main.php';

register_activation_hook(__FILE__, ['RY_TFTUTOR', 'plugin_activation']);
register_deactivation_hook(__FILE__, ['RY_TFTUTOR', 'plugin_deactivation']);

function RY_TFTUTOR(): RY_TFTUTOR
{
    return RY_TFTUTOR::instance();
}

RY_TFTUTOR();

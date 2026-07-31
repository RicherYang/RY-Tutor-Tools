<?php

namespace RY\Tutor\Tutor\Admin;

defined('ABSPATH') or exit;

use RY\Tutor\Main;
use TUTOR\Input;

final class Settings
{
    private static ?self $_instance = null;

    public static function instance(): Settings
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
            self::$_instance->do_init();
        }

        return self::$_instance;
    }

    protected function do_init(): void
    {
        add_action('admin_enqueue_scripts', [$this, 'admin_scripts']);
        add_filter('tutor/options/extend/attr', [$this, 'add_option_item']);
        add_action('tutor_option_save_before', [$this, 'validate_options']);
    }

    public function admin_scripts()
    {
        $page = Input::get('page', '');
        if ('tutor_settings' === $page) {
            $asset_info = include RY_TFTUTOR_PLUGIN_DIR . 'assets/admin/main.asset.php';
            wp_enqueue_script(Main::get_prefix_name('admin-main'), RY_TFTUTOR_PLUGIN_URL . 'assets/admin/main.js', $asset_info['dependencies'], $asset_info['version'], true);
            wp_enqueue_style(Main::get_prefix_name('admin-main'), RY_TFTUTOR_PLUGIN_URL . 'assets/admin/main.css', [], $asset_info['version']);
        }
    }

    public function add_option_item($attrs)
    {
        $attrs['ry-tools'] = [
            'label' => __('RY Tools', 'ry-tutor-tools'),
            'slug' => 'ry_tools',
            'template' => 'basic',
            'icon' => 'ry-icon-logo',
            'blocks' => [
                [
                    'label' => __('Service provider', 'ry-tutor-tools'),
                    'slug' => 'service_provider',
                    'block_type' => 'uniform',
                    'fields' => [
                        [
                            'key' => 'RY_enabled_ecpay',
                            'type' => 'toggle_switch',
                            'label' => __('ECPay support', 'ry-tutor-tools'),
                            'default' => 'off',
                            'desc' => __('Enable ECPay gateway method.', 'ry-tutor-tools'),
                            'toggle_blocks' => 'ecpay',
                        ],
                        [
                            'key' => 'RY_enabled_newebpay',
                            'type' => 'toggle_switch',
                            'label' => __('NewebPay support', 'ry-tutor-tools'),
                            'default' => 'off',
                            'desc' => __('Enable NewebPay gateway method.', 'ry-tutor-tools'),
                            'toggle_blocks' => 'newebpay',
                        ],
                        [
                            'key' => 'RY_enabled_payuni',
                            'type' => 'toggle_switch',
                            'label' => __('PAYUNi support', 'ry-tutor-tools'),
                            'default' => 'off',
                            'desc' => __('Enable PAYUNi gateway method.', 'ry-tutor-tools'),
                            'toggle_blocks' => 'payuni',
                        ],
                        [
                            'key' => 'RY_enabled_smilepay',
                            'type' => 'toggle_switch',
                            'label' => __('SmilePay support', 'ry-tutor-tools'),
                            'default' => 'off',
                            'desc' => __('Enable SmilePay gateway method.', 'ry-tutor-tools'),
                            'toggle_blocks' => 'smilepay',
                        ],
                        [
                            'type' => 'label',
                            'desc' => __('After switch enabled status, you need to reload the page to display the settings.', 'ry-tutor-tools'),
                        ],
                    ],
                ],
                [
                    'label' => __('General settings', 'ry-tutor-tools'),
                    'slug' => 'global_settings',
                    'block_type' => 'uniform',
                    'fields' => [
                        [
                            'key' => 'RY_general_prefix',
                            'type' => 'text',
                            'label' => __('Trade no prefix', 'ry-tutor-tools'),
                            'desc' => __('The prefix string of trade no. Only letters and numbers allowed.', 'ry-tutor-tools'),
                            'default' => '',
                            'placeholder' => '',
                        ],
                        [
                            'key' => 'RY_general_itemname',
                            'type' => 'text',
                            'label' => __('Payment item name', 'ry-tutor-tools'),
                            'desc' => __('If empty use the first course name.', 'ry-tutor-tools'),
                            'default' => '',
                            'placeholder' => '',
                        ],
                        [
                            'key' => 'RY_general_only_tw',
                            'type' => 'toggle_switch',
                            'label' => __('Address only TW', 'ry-tutor-tools'),
                            'desc' => __('Change country list only Taiwan.', 'ry-tutor-tools')
                                . __('Note: This will overwrite the TutorLMS original file, and it CANNOT be recovered.', 'ry-tutor-tools'),
                            'default' => 'off',
                        ],
                    ],
                ],
                'block_ecpay' => [
                    'label' => __('ECPay settings', 'ry-tutor-tools'),
                    'desc' => '',
                    'slug' => 'ecpay',
                    'block_type' => 'uniform',
                    'fields' => [
                        [
                            'key' => 'RY_ecpay_log',
                            'type' => 'toggle_switch',
                            'label' => __('Debug log', 'ry-tutor-tools'),
                            'default' => 'off',
                            'desc' => __('Note: this may log personal information.', 'ry-tutor-tools'),
                        ],
                        [
                            'key' => 'RY_ecpay_testmode',
                            'type' => 'toggle_switch',
                            'label' => __('Sandbox', 'ry-tutor-tools'),
                            'default' => 'off',
                            'desc' => __('Note: Recommend using this for development purposes only.', 'ry-tutor-tools'),
                        ],
                        [
                            'key' => 'RY_ecpay_MerchantID',
                            'type' => 'text',
                            'label' => _x('MerchantID', 'ECPay', 'ry-tutor-tools'),
                            'default' => '',
                        ],
                        [
                            'key' => 'RY_ecpay_HashKey',
                            'type' => 'text',
                            'label' => _x('HashKey', 'ECPay', 'ry-tutor-tools'),
                            'default' => '',
                        ],
                        [
                            'key' => 'RY_ecpay_HashIV',
                            'type' => 'text',
                            'label' => _x('HashIV', 'ECPay', 'ry-tutor-tools'),
                            'default' => '',
                        ],
                    ],
                ],
                'block_newebpay' => [
                    'label' => __('NewebPay settings', 'ry-tutor-tools'),
                    'desc' => '',
                    'slug' => 'newebpay',
                    'block_type' => 'uniform',
                    'fields' => [
                        [
                            'key' => 'RY_newebpay_log',
                            'type' => 'toggle_switch',
                            'label' => __('Debug log', 'ry-tutor-tools'),
                            'default' => 'off',
                            'desc' => __('Note: this may log personal information.', 'ry-tutor-tools'),
                        ],
                        [
                            'key' => 'RY_newebpay_testmode',
                            'type' => 'toggle_switch',
                            'label' => __('Sandbox', 'ry-tutor-tools'),
                            'default' => 'off',
                            'desc' => __('Note: Recommend using this for development purposes only.', 'ry-tutor-tools'),
                        ],
                        [
                            'key' => 'RY_newebpay_MerchantID',
                            'type' => 'text',
                            'label' => _x('MerchantID', 'NewebPay', 'ry-tutor-tools'),
                            'default' => '',
                        ],
                        [
                            'key' => 'RY_newebpay_HashKey',
                            'type' => 'text',
                            'label' => _x('HashKey', 'NewebPay', 'ry-tutor-tools'),
                            'default' => '',
                        ],
                        [
                            'key' => 'RY_newebpay_HashIV',
                            'type' => 'text',
                            'label' => _x('HashIV', 'NewebPay', 'ry-tutor-tools'),
                            'default' => '',
                        ],
                    ],
                ],
                'block_payuni' => [
                    'label' => __('PAYUNi settings', 'ry-tutor-tools'),
                    'desc' => '',
                    'slug' => 'payuni',
                    'block_type' => 'uniform',
                    'fields' => [
                        [
                            'key' => 'RY_payuni_log',
                            'type' => 'toggle_switch',
                            'label' => __('Debug log', 'ry-tutor-tools'),
                            'default' => 'off',
                            'desc' => __('Note: this may log personal information.', 'ry-tutor-tools'),
                        ],
                        [
                            'key' => 'RY_payuni_testmode',
                            'type' => 'toggle_switch',
                            'label' => __('Sandbox', 'ry-tutor-tools'),
                            'default' => 'off',
                            'desc' => __('Note: Recommend using this for development purposes only.', 'ry-tutor-tools'),
                        ],
                        [
                            'key' => 'RY_payuni_MerID',
                            'type' => 'text',
                            'label' => _x('MerID', 'PAYUNi', 'ry-tutor-tools'),
                            'default' => '',
                        ],
                        [
                            'key' => 'RY_payuni_HashKey',
                            'type' => 'text',
                            'label' => _x('HashKey', 'PAYUNi', 'ry-tutor-tools'),
                            'default' => '',
                        ],
                        [
                            'key' => 'RY_payuni_HashIV',
                            'type' => 'text',
                            'label' => _x('HashIV', 'PAYUNi', 'ry-tutor-tools'),
                            'default' => '',
                        ],
                    ],
                ],
                'block_smilepay' => [
                    'label' => __('SmilePay settings', 'ry-tutor-tools'),
                    'desc' => '',
                    'slug' => 'smilepay',
                    'block_type' => 'uniform',
                    'fields' => [
                        [
                            'key' => 'RY_smilepay_log',
                            'type' => 'toggle_switch',
                            'label' => __('Debug log', 'ry-tutor-tools'),
                            'default' => 'off',
                            'desc' => __('Note: this may log personal information.', 'ry-tutor-tools'),
                        ],
                        [
                            'key' => 'RY_smilepay_testmode',
                            'type' => 'toggle_switch',
                            'label' => __('Sandbox', 'ry-tutor-tools'),
                            'default' => 'off',
                            'desc' => __('Note: Recommend using this for development purposes only.', 'ry-tutor-tools'),
                        ],
                        [
                            'key' => 'RY_smilepay_Dcvc',
                            'type' => 'text',
                            'label' => _x('Dcvc', 'SmilePay', 'ry-tutor-tools'),
                            'default' => '',
                        ],
                        [
                            'key' => 'RY_smilepay_Rvg2c',
                            'type' => 'text',
                            'label' => _x('Rvg2c', 'SmilePay', 'ry-tutor-tools'),
                            'default' => '',
                        ],
                        [
                            'key' => 'RY_smilepay_Verifykey',
                            'type' => 'text',
                            'label' => _x('Verifykey', 'SmilePay', 'ry-tutor-tools'),
                            'default' => '',
                        ],
                        [
                            'key' => 'RY_smilepay_Rotcheck',
                            'type' => 'text',
                            'label' => _x('Rotcheck', 'SmilePay', 'ry-tutor-tools'),
                            'default' => '',
                        ],
                    ],
                ],
            ],
        ];

        return $attrs;
    }

    public function validate_options($options)
    {
        $success = true;
        $message = '';

        foreach (['ecpay', 'newebpay', 'payuni', 'smilepay'] as $key) {
            $enable_gateway = $options['RY_enabled_' . $key] ?? 'off';
            if ('on' === $enable_gateway) {
                if (!preg_match('/^[a-z0-9]{0,3}$/i', $options['RY_' . $key . '_prefix'] ?? '')) {
                    $success = false;
                    $message = __('Trade no prefix only letters and numbers allowed, and maximum length is 3 characters.', 'ry-tutor-tools');
                    break;
                }
            }
        }

        if (!$success) {
            wp_send_json([
                'success' => $success,
                'message' => $message,
            ]);
        }
    }
}

<?php

/**
 * @version 0.1.0
 */

/*
Plugin Name: Simple Digital Clock 🕒
Plugin URI: https://timenow.zone/widgets/simple-digital
Description: The Simple Digital Clock — is a magic 🪄 and easy-to-use with beautiful UI digital clock with support multi-language, locale, date, caption and time zone.
Version: 0.1.0
Author: TimeNow.zone
Author URI: https://timenow.zone/
License: The 3-Clause BSD
Text Domain: simple-digital-clock
Domain Path: /languages
*/

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

define('SDC_PATH', plugin_dir_path(__FILE__));
define('SDC_URL', plugin_dir_url(__FILE__));
define('SDC_NAME', 'Simple Digital Clock 🕒');
define('SDC_PLUGIN_SLUG', 'simple-digital-clock');

if(file_exists(plugin_dir_path(__FILE__) . 'includes/simple-digital-clock-admin-notices.php')) {
    include('includes/simple-digital-clock-admin-notices.php');

    $admin_notice = SDC_Admin_Notices::get_instance();
    $admin_notice->info(__('Rate', 'simple-digital-clock'), 'rate');
}

class SDC_digital_clock_widget
{
    protected static $_instance = null;

    public static function get_instance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function __construct()
    {
        add_action('admin_menu', [$this, 'SDC_add_plugin_page']);
        add_filter('plugin_action_links', [$this, 'SDC_plugin_action_links'], 10, 2);
    }

    public function SDC_plugin_action_links($links, $file)
    {
        if ($file == plugin_basename(SDC_PATH.'/widget_init.php')) {
            $links[] = '<a href="'.admin_url('admin.php?page='.SDC_PLUGIN_SLUG).'">'.__('Settings', 'simple-digital-clock').'</a>';
        }

        return $links;
    }

    /**
     * Add options page.
     */
    public function SDC_add_plugin_page()
    {
        add_options_page(
            'Settings Admin',
            'Simple Digital Clock',
            'manage_options',
            SDC_PLUGIN_SLUG,
            [$this, 'SDC_admin_settings_page']
        );
    }

    public function SDC_admin_settings_page()
    {
        require_once SDC_PATH.'includes/simple-digital-clock-admin-settings.php';
    }
}

function SDC_load_plugin_textdomain()
{
    load_plugin_textdomain('simple-digital-clock', false, basename(dirname(__FILE__)).'/languages/');
}

function simple_digital_clock_block_register_block() {
    wp_register_script(
        'simple-digital-clock-block-editor-script',
        plugins_url('block.js', __FILE__),
        ['wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor'],
        filemtime(plugin_dir_path(__FILE__) . 'block.js')
    );

    // Localize the script with necessary data
    wp_localize_script('simple-digital-clock-block-editor-script', 'blockData', [
        'locale' => get_locale(),
        'i18n' => [
            'title' => __('Simple Digital Clock Widget', 'simple-digital-clock'),
            'description' => __('A simple block for displaying Digital Clock.', 'simple-digital-clock'),
            'container' => __('Container', 'simple-digital-clock'),
            'caption' => __('Caption', 'simple-digital-clock'),
            'width' => __('Width', 'simple-digital-clock'),
            'fullSize' => __('Full Size', 'simple-digital-clock'),
            'rounded' => __('Rounded', 'simple-digital-clock'),
            'border' => __('Border', 'simple-digital-clock'),
            'textAlign' => __('Text Align', 'simple-digital-clock'),
            'textAlignHelp' => __('Select the alignment of the text within the container.', 'simple-digital-clock'),
            'shadow' => __('Shadow', 'simple-digital-clock'),
            'shadowHelp' => __('Select the shadow of the container.', 'simple-digital-clock'),
            'backgroundColor' => __('Background Color', 'simple-digital-clock'),
            'color' => __('Color', 'simple-digital-clock'),
            'gradient' => __('Gradient', 'simple-digital-clock'),
            'options' => __('Options', 'simple-digital-clock'),
            'timeZone' => __('Time Zone', 'simple-digital-clock'),
            'display' => __('Display', 'simple-digital-clock'),
            'googleFonts' => __('Google Fonts', 'simple-digital-clock'),
            'googleFontsHelp' => __('Google fonts are loading from the CDN, they are not included in the plugin.', 'simple-digital-clock'),
            'locale' => __('Locale', 'simple-digital-clock'),
            'localeHelp' => __('Language of date and including the time display format.', 'simple-digital-clock'),
            'about' => __('About', 'simple-digital-clock'),
            'ratePlugin' => __('❤️ Rate plugin ★★★★★', 'simple-digital-clock'),
            'second' => __('Second', 'simple-digital-clock'),
            'date' => __('Date', 'simple-digital-clock'),
            'amPm' => __('AM/PM', 'simple-digital-clock'),
            'left' => __('Left', 'simple-digital-clock'),
            'center' => __('Center', 'simple-digital-clock'),
            'right' => __('Right', 'simple-digital-clock'),
            'shadowHelp' => __('Select the Shadow of the text within the container.', 'simple-digital-clock'),
            'gradientHelp' => __('Select a gradient (font color is automatically inverted based on background color). Note: the gradient is used on top of the background color.', 'simple-digital-clock'),
            'time' => __('Time', 'simple-digital-clock'),
            'clock' => __('Clock', 'simple-digital-clock'),
            'digital' => __('Digital', 'simple-digital-clock'),
            'simple' => __('Simple', 'simple-digital-clock'),
            'widget' => __('Widget', 'simple-digital-clock'),
            'demoPlugin' => __('DEMO 👀', 'simple-digital-clock'),
            'none' => __('None', 'simple-digital-clock'),
            'clearColors' => __('Clear All Colors', 'simple-digital-clock'),
            'clearGradient' => __('Clear Gradient', 'simple-digital-clock'),
        ],
    ]);

    wp_register_script(
        'simple-digital-clock-script',
        'https://cdn.jsdelivr.net/gh/Timenow-zone/widgets@main/SimpleDigitalClockWidget/latest.min.js',
        [],
        null,
        true,
    );

    register_block_type('simple-digital-clock-block/widget-block', [
        'editor_script' => 'simple-digital-clock-block-editor-script',
        'script' => 'simple-digital-clock-script',
    ]);
}


add_action('init', 'simple_digital_clock_block_register_block');
add_action('plugins_loaded', 'SDC_load_plugin_textdomain');

$GLOBALS['SDC_digital_clock_widget'] = SDC_digital_clock_widget::get_instance();

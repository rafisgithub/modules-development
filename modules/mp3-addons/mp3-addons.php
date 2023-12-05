<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: MP3 Addons
Description: Custom module for mp3-addons with call log
Version: 1.0.0
Requires at least: 1.5.*
*/

define('MP3_ADDONS_MODULE_NAME', 'mp3-addons');

hooks()->add_action('admin_init', 'mp3_addons_module_init_menu_items');



/**
* Register activation module hook
*/
register_activation_hook(MP3_ADDONS_MODULE_NAME, 'mp3_addons_module_activation_hook');

function mp3_addons_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(MP3_ADDONS_MODULE_NAME, [MP3_ADDONS_MODULE_NAME]);

/**
* Init mp3-addons module menu items in setup in admin_init hook
* @return null
*/
function mp3_addons_module_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('mp3-addons', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('mp3-addons-tracking', [
            'slug'     => 'mp3-addons-tracking',
            'name'     => _l('mp3_addons'),
            'href'     => admin_url('mp3-addons/mp3_addons'),
            'icon'       => 'fa-regular fa-file-audio fa-beat',
            'position' => 24,
        ]);
    }
}

if (staff_can('view', 'settings')) {
    hooks()->add_action('admin_init', 'mp3_addons_add_settings_tab');
}

/**
 * @return void
 */
function mp3_addons_add_settings_tab()
{
    $CI = &get_instance();
    $CI->app_tabs->add_settings_tab('mp3_addons-settings', [
        // 'name'     => _l('mp3_addons_credential_setup'),
        'name'     => "MP3 Addons setup",
        'view'     => 'mp3-addons/settings',
        'icon'       => 'fa-solid fa-shield-halved fa-beat',
        'position' => 36,
    ]);
}

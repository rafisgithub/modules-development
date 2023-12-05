<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Ring Central
Description: Module for RingCentral integration
Version: 1.0.0
Requires at least: 2.3.*
*/

define('RINGCENTRAL_MODULE_NAME', 'ringcentral');

hooks()->add_action('admin_init', 'ringcentral_module_init_menu_items');
hooks()->add_action('admin_init', 'ringcentral_permissions');



function ringcentral_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
        'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit'   => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];

    register_staff_capabilities('ringcentral', $capabilities, _l('ringcentral'));
}


/**
* Register activation module hook
*/
register_activation_hook(RINGCENTRAL_MODULE_NAME, 'ringcentral_module_activation_hook');

function ringcentral_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(RINGCENTRAL_MODULE_NAME, [RINGCENTRAL_MODULE_NAME]);

/**
* Init ringcentral module menu items in setup in admin_init hook
* @return null
*/
function ringcentral_module_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('ringcentral', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('ringcentral-tracking', [
            'slug'     => 'ringcentral-tracking',
            'name'     => _l('ringcentral'),
            'href'     => admin_url('ringcentral'),
            'icon'       => 'fa-solid fa-mobile-screen fa-shake',
            'position' => 24,
        ]);
    }

}

if (staff_can('view', 'settings')) {
    hooks()->add_action('admin_init', 'ringcentral_add_settings_tab');
}

/**
 * @return void
 */
function ringcentral_add_settings_tab()
{
    $CI = &get_instance();
    $CI->app_tabs->add_settings_tab('ringcentral-settings', [
        // 'name'     => _l('mp3_addons_credential_setup'),
        'name'     => "Ringcentral Setup",
        'view'     => 'ringcentral/settings',
        'icon'       => 'fa-solid fa-user-secret',
        'position' => 36,
    ]);
}
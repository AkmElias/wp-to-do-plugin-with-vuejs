<?php

/*
Plugin Name: to-do
Plugin URI: #
Description: A WordPress boilerplate plugin with Vue js.
Version: 1.0.0
Author: #
Author URI: #
License: A "Slug" license name e.g. GPL2
Text Domain: textdomain
*/


/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 *
 * Copyright 2019 Plugin Name LLC. All rights reserved.
 */

if (!defined('ABSPATH')) {
    exit;
}
if (!defined('TODO_VERSION')) {
    define('TODO_VERSION_LITE', true);
    define('TODO_VERSION', '1.1.0');
    define('TODO_MAIN_FILE', __FILE__);
    define('TODO_URL', plugin_dir_url(__FILE__));
    define('TODO_ASSETS', TODO_URL . 'assets' );
    define('TODO_DIR', plugin_dir_path(__FILE__));
    define('TODO_UPLOAD_DIR', '/to-do');

    class toDo
    {
        public function boot()
        {
            if (is_admin()) {
                $this->adminHooks();
            }
                $this->frontendHooks();
        }

        public function adminHooks()
        {
            require TODO_DIR . 'includes/autoload.php';

            //Register Admin menu
            $menu = new \toDo\Classes\Menu();
            $menu->register();

            // Top Level Ajax Handlers
            $ajaxHandler = new \toDo\Classes\AdminAjaxHandler();
            $ajaxHandler->registerEndpoints();

            add_action('to-do/render_admin_app', function () {
                $adminApp = new \toDo\Classes\AdminApp();
                $adminApp->bootView();
            });
        }

        public function frontendHooks()
        {
            require TODO_DIR . 'includes/autoload.php';

            new \toDo\Classes\Assets();
            new \toDo\Classes\Shortcode();
            // Top Level Ajax Handlers
            $ajaxHandler = new \toDo\Classes\FrontendAjaxHandler();
            $ajaxHandler->registerEndpoints();

            add_action('to-do/render_frontend_app', function (){

            });
        }

        public function textDomain()
        {
            load_plugin_textdomain('to-do', false, basename(dirname(__FILE__)) . '/languages');
        }
    }

    add_action('plugins_loaded', function () {
        (new toDo())->boot();
    });

    register_activation_hook(__FILE__, function ($newWorkWide) {
        require_once(TODO_DIR . 'includes/Classes/Activator.php');
        $activator = new \toDo\Classes\Activator();
        $activator->migrateDatabases($newWorkWide);
    });

    // disabled admin-notice on dashboard
    add_action('admin_init', function () {
        $disablePages = [
            'to-do.php',
        ];
        if (isset($_GET['page']) && in_array($_GET['page'], $disablePages)) {
            remove_all_actions('admin_notices');
        }
    });
} else {
    add_action('admin_init', function () {
        deactivate_plugins(plugin_basename(__FILE__));
    });
}

<?php

namespace toDo\Classes;

class Menu
{
    public function register()
    {
        add_action('admin_menu', array($this, 'addMenus'));
    }

    public function addMenus()
    {
        $menuPermission = AccessControl::hasTopLevelMenuPermission();
        if (!$menuPermission) {
            return;
        }

        $title = __('to do', 'textdomain');
        global $submenu;
        add_menu_page(
            $title,
            $title,
            $menuPermission,
            'to-do.php',
            array($this, 'enqueueAssets'),
            'dashicons-admin-site',
            25
        );

        $submenu['to-do.php']['my_profile'] = array(
            __('Plugin Dashboard', 'textdomain'),
            $menuPermission,
            'admin.php?page=to-do.php#/',
        );
        $submenu['to-do.php']['settings'] = array(
            __('Settings', 'textdomain'),
            $menuPermission,
            'admin.php?page=to-do.php#/settings',
        );
        $submenu['to-do.php']['supports'] = array(
            __('Supports', 'textdomain'),
            $menuPermission,
            'admin.php?page=to-do.php#/supports',
        );
    }

    public function enqueueAssets()
    {
        do_action('to-do/render_admin_app');
        wp_enqueue_script(
            'to-do_boot',
            TODO_URL . 'assets/js/boot.js',
            array('jquery'),
            TODO_VERSION,
            true
        );

        // 3rd party developers can now add their scripts here
        do_action('to-do/booting_admin_app');
        wp_enqueue_script(
            'to-do_js',
            TODO_URL . 'assets/js/plugin-main-js-file.js',
            array('to-do_boot'),
            TODO_VERSION,
            true
        );

        //enque css file
        wp_enqueue_style('to-do_admin_css', TODO_URL . 'assets/css/element.css');

        $toDoAdminVars = apply_filters('to-do/admin_app_vars', array(
            //'image_upload_url' => admin_url('admin-ajax.php?action=wpf_global_settings_handler&route=wpf_upload_image'),
            'assets_url' => TODO_URL . 'assets/',
            'ajaxurl' => admin_url('admin-ajax.php')
        ));

        wp_localize_script('to-do_boot', 'toDoAdmin', $toDoAdminVars);
    }
}

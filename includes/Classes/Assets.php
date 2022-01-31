<?php

namespace toDo\Classes;

class Assets
{
    public function __construct()
    {
//        add_action('admin_enqueue_scripts', [$this, 'register']);
        add_action('wp_enqueue_scripts', [$this, 'register'], 999);
    }

    public function register()
    {
        $this->register_scripts($this->get_frontend_scripts());
        $this->register_styles($this->get_frontend_styles());
        wp_localize_script('todo_frontend_script','ajax_obj',array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce("aj-nonce")
        ));
    }

    public function register_scripts($scripts)
    {
        foreach ($scripts as $handle => $script) {
            $deps = $script['deps'] ?? false;
            $in_footer = $script['in_footer'] ?? false;
            $version = $script['version'] ?? TODO_VERSION;
            wp_enqueue_script($handle, $script['src'], $deps, $version, $in_footer);
        }
    }

    // register styles
    public function register_styles($styles)
    {
        foreach ($styles as $handle => $style) {
            $deps = $style['deps'] ?? false;
            $version = $style['version'] ?? TODO_VERSION;

            wp_enqueue_style($handle, $style['src'], $deps, $version);
        }
    }

    public function get_frontend_scripts():array
    {
        return [
            'todo_frontend_script' => [
                'src' => TODO_ASSETS . '/frontend/js/todo-frontend.js',
                'deps' => ['jquery'],
                'version' => time(),
                'in_footer' => true
            ]
        ];
    }

    public function get_frontend_styles(): array
    {
        return [
            'todo_frontend_style' => [
                'src' => TODO_ASSETS . '/frontend/css/todo-frontend.css',
                'version' => time(),
            ]
        ];
    }
}
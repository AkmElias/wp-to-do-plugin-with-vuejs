<?php

namespace toDo\Classes;

class Shortcode
{
    protected $frontend;

    public function __construct()
    {
        add_shortcode("wp-todo", [$this, 'wp_custom_todo_template']);

        $this->frontend = new Frontend();

    }

    public function wp_custom_todo_template($atts = [], $e = '', $tag = ''): string
    {
        global $wpdb;
        $customTodoTable = $wpdb->prefix . 'to_do';

        $wpctd_atts = shortcode_atts(
            array(
                'id' => ''
            ),
            $atts
        );

        if(!empty($wpctd_atts['id'])){
            //get custom todo by id
            $result = $wpdb->get_row("SELECT * FROM ". $customTodoTable . " WHERE id=". $wpctd_atts['id']);

            global $current_user;
            global $wp;

            $wp_custom_todo = json_encode($result);
            $wp_custom_todo = json_decode($wp_custom_todo, true);

            $e .= $this->getHeader($wp_custom_todo);
            $e .= '<div class="todo-container">';
            $e .= '<p class="todo-title">' . esc_html__('TODO\'S', 'to-do') . '</p>';
            $e .= '<form id="add-form">';
            $e .= '<div class="todo-input-group">';
            $e .= '<input id="task-input" class="task-input" type="text" name="task" placeholder="Enter a task..."/>';
            $e .= '<input id="to-do-id"  type="number" name="task" style="display: none;" value="'. $wp_custom_todo['id'] .'"/>';
            $e .= '<button id="add-button" class="add-button">' . esc_html__('ADD TASK', 'to-do') . '</button>';
            $e .= '</div>';
            $e .= '</form>';
            $e .= '<div id="todo-lists" class="todo-lists">';

            $e .= $this->frontend->get_tasks($wp_custom_todo['id']);

            $e .= '</div>';
            $e .= '<div class="done-section" style="margin-top: 10px;">';

            if($wp_custom_todo['show_completed'] === 'true') {
                $e .= ' <p class="done-title">'. esc_html__('Done','to-do') .'</p>';
                $e .= '<div id="done-lists" class="todo-lists">';
                $e .= $this->frontend->get_done_tasks($wp_custom_todo['id']);
                $e .= '</div>';
            }

            $e .= '</div>';
            $e .= '</div>';
        }

        return $e;
    }

    protected function getHeader($wp_custom_todo)
    {
        $e = '';

        $list_limit = '';
        $show_done = '';

        if ($wp_custom_todo['list_limit'] == 10) {
            $list_limit = 'Minimum';
        } else if ($wp_custom_todo['list_limit'] == 15) {
            $list_limit = 'Medium';
        } else {
            $list_limit = 'Maximum';
        }

        $show_done = $wp_custom_todo['show_completed'] === 'true' ? 'Yes' : 'No';

        $e .= '<div class="todo-header">';
        $e .= '<div class="header-item">';
        $e .= '<span class="header-level"> Title <p>' . esc_html($wp_custom_todo['title']). ' Todo</p></span>';
        $e .= '</div>';

        $e .= '<div class="header-item">';
        $e .= '<span class="header-level"> List Limit  <p>' . esc_html($list_limit, 'todo'). '</p></span>';
        $e .= '</div>';

        $e .= '<div class="header-item">';
        $e .= '<span class="header-level"> Show Done  <p>' . esc_html($show_done, 'todo'). '</p></span>';
        $e .= '</div>';

        $e .= '<div class="header-item">';
        $e .= '<span class="header-level"> Theme <p>' . esc_html($wp_custom_todo['theme'], 'todo'). '</p></span>';
        $e .= '</div>';

        $e .= '</div>';

        return $e;
    }
}
<?php

namespace toDo\Classes;

class Shortcode
{
    public function __construct()
    {
        add_shortcode("wp-todo", [$this, 'wp_custom_todo_template']);

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


            $e .= '<div class="todo-container">';
            $e .= '<h4>'. $wp_custom_todo['title'] .'</h4>';
            $e .= '<p class="todo-title">' . esc_html__('TODO\'S', 'to-do') . '</p>';
            $e .= '<form id="add-form">';
            $e .= '<div class="todo-input-group">';
            $e .= '<input id="task-input" class="task-input" type="text" name="task" placeholder="Enter a task..."/>';
            $e .= '<input id="to-do-id"  type="number" name="task" style="display: none;" value="'. $wp_custom_todo['id'] .'"/>';
            $e .= '<button id="add-button" class="add-button">' . esc_html__('ADD TASK', 'to-do') . '</button>';
            $e .= '</div>';
            $e .= '</form>';
            $e .= '<div id="todo-lists" class="todo-lists">';
            $e .= '<p class="todo-item">' . esc_html__('No Task Assigned Yet', 'to-do') . '</p>';
            $e .= '</div>';
            $e .= '<div class="done-section" style="margin-top: 10px;">';
            $e .= ' <p class="done-title">'. esc_html__('Done','to-do') .'</p>';
            $e .= '<div id="done-lists" class="todo-lists">';
            $e .= '<p class="done-item">'. esc_html__('No Complete task found','to-do').  '</p>';
            $e .= '</div>';
            $e .= '</div>';
            $e .= '</div>';
        }

        return $e;
    }
}
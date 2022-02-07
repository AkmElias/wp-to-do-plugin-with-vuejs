<?php

namespace toDo\Classes;

class Shortcode
{
    protected $frontend;
    protected $to_do_id;
    protected $title;
    protected $theme;
    protected $list_limit;
    protected $show_done;
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

            if(isset($wp_custom_todo['id'])){
                $this->to_do_id = $wp_custom_todo['id'];
            }
            if(isset($wp_custom_todo['title'])){
                $this->title = $wp_custom_todo['title'];
            }
            if(isset($wp_custom_todo['theme'])){
                $this->theme = $wp_custom_todo['theme'];
            }

            if (isset($wp_custom_todo['list_limit']) == 10) {
                $this->list_limit = 'Minimum';
            } else if (isset($wp_custom_todo['list_limit']) == 15) {
                $this->list_limit = 'Medium';
            } else {
                $this->list_limit = 'Maximum';
            }

            if(isset($wp_custom_todo['show_completed'])){
                $this->show_done = $wp_custom_todo['show_completed'] === 'true' ? 'Yes' : 'No';
            }

            $e .= $this->getHeader();
            $e .= '<div class="todo-container">'; 
               //processing spinner
            $e .= '<div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>';
            
            $e .= '<p class="todo-title">' . esc_html__('TODO\'S', 'to-do') . '</p>';
            $e .= '<form id="add-form">';
            $e .= '<div class="todo-input-group">';
            $e .= '<input id="task-input" class="task-input" type="text" name="task" placeholder="Enter a task..."/>';
            $e .= '<input id="to-do-id"  type="number" name="task" style="display: none;" value="'. $this->to_do_id .'"/>';
            $e .= '<button id="add-button" class="add-button">' . esc_html__('ADD TASK', 'to-do') . '</button>';
            $e .= '</div>';
            $e .= '</form>';
            $e .= '<div id="todo-lists" class="todo-lists">';

            $e .= $this->frontend->get_tasks($this->to_do_id);

            $e .= '</div>';

           
            $e .= '<div class="done-section" style="margin-top: 10px;">';

            if($this->show_done === 'Yes') {
                $e .= ' <p class="done-title">'. esc_html__('Done','to-do') .'</p>';
                $e .= '<div id="done-lists" class="todo-lists">';
                $e .= $this->frontend->get_done_tasks($this->to_do_id);
                $e .= '</div>';
            }

            $e .= '</div>';
            $e .= '</div>';
        }

        return $e;
    }

    protected function getHeader() : string
    {
        $e = '';

        $e .= '<div class="todo-header">';
        $e .= '<div class="header-item">';
        $e .= '<span class="header-level"> Title <p>' . esc_html($this->title). ' Todo</p></span>';
        $e .= '</div>';

        $e .= '<div class="header-item">';
        $e .= '<span class="header-level"> List Limit  <p>' . esc_html($this->list_limit, 'todo'). '</p></span>';
        $e .= '</div>';

        $e .= '<div class="header-item">';
        $e .= '<span class="header-level"> Show Done  <p>' . esc_html($this->show_done, 'todo'). '</p></span>';
        $e .= '</div>';

        $e .= '<div class="header-item">';
        $e .= '<span class="header-level"> Theme <p>' . esc_html($this->theme, 'todo'). '</p></span>';
        $e .= '</div>';

        $e .= '</div>';

        return $e;
    }
}
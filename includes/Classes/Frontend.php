<?php

namespace toDo\Classes;

class Frontend
{
    protected $_wpdb;
    protected $table;

    public function __construct()
    {
        global $wpdb;
        $this->_wpdb = $wpdb;
        $this->table = $wpdb->prefix. 'custom_todo';
    }

    /**
     * @param $to_do_id
     * @return string
     */
    public function get_tasks($to_do_id): string
    {
        //error data
        $error = false;
        $errors = array();

        $results = $this->_wpdb->get_results("SELECT * from $this->table WHERE to_do_id = " . $to_do_id . " AND task_status = 'in_progress'");

        if ($results) {
            error_log('results found......');
        }
        if (is_wp_error($results)) {
            wp_send_json([
                'success' => false,
                'status' => 404,
                'message' => $results->get_error_message()
            ]);
            wp_die();
        }

        if ($this->_wpdb->num_rows == 0) {
            return '<p class="todo-item"> No Tasks assigned.. </p>';
        }

        return $this->get_tasks_html($results);
    }

    /**
     * @param $to_do_id
     * @return string
     */
    public function get_done_tasks($to_do_id): string
    {
        $results = $this->_wpdb->get_results("SELECT * from $this->table WHERE to_do_id = ". $to_do_id . " AND task_status != 'in_progress'");

        if(is_wp_error($results)){
            wp_send_json([
                'success' => false,
                'status' => 404,
                'message' => $results->get_error_message()
            ]);
            wp_die();
        }

        if($this->_wpdb->num_rows == 0){
            return '<p class="done-item"> No Completed task found.. </p>';
        }

        return $this->get_done_tasks_html($results);

    }

    /**
     * @param $results
     * @return string
     */
    public function get_tasks_html($results): string
    {
        $html = '';

        foreach ($results as $task){
            $html .= '<div id="todo-item" class="todo-item" draggable="true" data-id="'.$task->task_id .'">';
            $html .= '<p style="margin: 0;">'. $task->task_title  .'</p>';
            $html .= '<div class="todo-item-actions">';
            $html .= '<span class="done-task" data-id="'.$task->task_id .'"><i class="fa fa-check done-icon" style="color: green;"></i></span>';
            $html .= '<span class="delete-task" data-id="'.$task->task_id .'"><i class="fa fa-times remove" style="color: red;"></i></span>';
            $html .= '</div>';
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * @param $results
     * @return string
     */
    public function get_done_tasks_html($results): string
    {
        $html = '';

        foreach ($results as $task){
            $html .= '<div id="done-item" class="done-item" draggable="true" data-id="'.$task->task_id .'">';
            $html .= '<p style="margin: 0;">'. $task->task_title  .'</p>';
            $html .= '<div class="todo-item-actions">';
            $html .= '<span class="delete-task" data-id="'.$task->task_id .'"><i class="fa fa-times done-remove" style="color: red;"></i></span>';
            $html .= '</div>';
            $html .= '</div>';
        }

        return $html;
    }
}
<?php

namespace toDo\Classes;

class FrontendAjaxHandler
{
    protected $_wpdb;
    protected $table;
    protected $frontend;

    public function __construct()
    {
        global $wpdb;
        $this->_wpdb = $wpdb;
        $this->table = $wpdb->prefix. 'custom_todo';
        $this->frontend = new Frontend();
    }

    public function registerEndpoints()
    {
        add_action('wp_ajax_to_do_frontend_ajax', array($this, 'handleEndPoints'));
    }

    public function handleEndPoints()
    {
//        check nonce, if it fails return
        if(!wp_verify_nonce($_POST['nonce'], 'aj-nonce')){
            error_log("nonce failed........");
            wp_send_json([
                "status" => 403,
                "nonce" => $_POST['nonce'],
                "success"=> false,
                "message" => "Something went wrong! Request not valid.",
            ]);
            wp_die();
        }

        $route = sanitize_text_field($_REQUEST['route']);

        $validRoutes = array(
            'get_all_custom_todo' => 'getAllCustomTodo',
            'get_all_done_todo'=> 'getAllDoneTodo',
            'add_custom_todo' => 'addCustomTodo',
            'update_custom_todo' => 'updateCustomTodo',
            'delete_custom_todo' => 'deleteCustomTodo'
        );

        if (isset($validRoutes[$route])) {
            do_action('to-do/doing_ajax_forms_' . $route);
            return $this->{$validRoutes[$route]}();
        }

        do_action('to-do/frontend_ajax_handler_catch', $route);
    }

    protected function getAllCustomTodo()
    {
        $to_do_id = $_POST['to_do_id'];

        error_log($this->frontend->get_tasks($to_do_id));

        $data =  $this->frontend->get_tasks($to_do_id);

        if($data){
            wp_send_json_success( $data, 200);
        } else {
            return false;
        }
    }

    protected function getAllDoneTodo()
    {
        $to_do_id = $_POST['to_do_id'];

        $data = $this->frontend->get_done_tasks($to_do_id);

        if($data){
            wp_send_json_success( $data, 200);
        } else {
            return false;
        }
    }

    protected function addCustomTodo()
    {
        //error data
        $error = false;
        $errors = array();

        //get board data
        $data = array();
        $data['task_title'] = sanitize_text_field($_POST['task_title']);
        $data['task_status'] = sanitize_text_field($_POST['task_status']);
        $data['to_do_id'] = sanitize_text_field($_POST['to_do_id']);

        $to_do_id = $data['to_do_id'];

        // to_do data validation
        if (empty($data['task_title'])) {
            //title is empty
            $error = true;
            $errors['title'] = 'Task Title is required.';
        }

        //check error and send response
        if($error){
            wp_send_json_error( $errors, 403 );
            wp_die();
        }

        //get list limit from current to do table
        $result = $this->_wpdb->get_row("SELECT * FROM wp_to_do WHERE id = ". $to_do_id);

//        dd($result->list_limit);

        $results = $this->_wpdb->get_results("SELECT * FROM $this->table WHERE to_do_id = $to_do_id");

        if($this->_wpdb->num_rows > $result->list_limit){

            $errors['list_limit'] = "Task limit exceeded";

            wp_send_json_error( $errors, 403 );
            wp_die();
        }
        //Call the create function after all verifications
        $result = $this->_wpdb->insert($this->table, $data);
        if($result){
            wp_send_json_success( $result, 200 );
        } else {
            return false;
        }
    }

    protected function updateCustomTodo()
    {
        //error data
        $error = false;
        $errors = array();

        //get ID
        $where = array();
        $id = sanitize_text_field($_POST['id']);
        $where['task_id'] = $id;

        //get Data
        $data = array();
        $data['task_status'] = "Done";

        $result = $this->_wpdb->update($this->table, $data, $where);

        if($result){
            wp_send_json_success( $result, 200 );
        } else {
            return false;
        }
    }

    protected function deleteCustomTodo(){
        //error data
        $error = false;
        $errors = array();

        //get ID
        $where = array();
        $id = sanitize_text_field($_POST['id']);
        $where['task_id'] = $id;

        $result = $this->_wpdb->delete($this->table,$where);

        if($result){
            wp_send_json_success( $result, 200 );
        } else {
            return false;
        }

    }
}
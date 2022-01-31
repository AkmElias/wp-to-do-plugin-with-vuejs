<?php

namespace toDo\Classes;

class FrontendAjaxHandler
{
    protected $_wpdb;
    protected $table;

    public function __construct()
    {
        global $wpdb;
        $this->_wpdb = $wpdb;
        $this->table = $wpdb->prefix. 'custom_todo';
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

    }

    protected function getAllDoneTodo()
    {

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

        // to_do data validation
        if (empty($data['task_title'])) {
            //title is empty
            $error = true;
            $errors['title'] = 'Task Title is required.';
        }

        //check error and send response
        if($error){
            wp_send_json_error( $errors );
            wp_die();
        }

//        error_log('reached here..........');
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

    }

    protected function deleteCustomTodo(){

    }
}
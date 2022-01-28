<?php

namespace toDo\Classes;

class AdminAjaxHandler
{
    protected $_wpdb;
    protected $table;

    public function __construct()
    {
        global $wpdb;
        $this->_wpdb = $wpdb;
        $this->table = $wpdb->prefix. 'to_do';
    }

    public function registerEndpoints()
    {
        add_action('wp_ajax_to_do_admin_ajax', array($this, 'handleEndPoints'));
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
            'add_custom_todo' => 'addCustomTodo',
            'update_custom_todo' => 'updateCustomTodo',
            'delete_custom_todo' => 'deleteCustomTodo'
        );

        if (isset($validRoutes[$route])) {
            do_action('to-do/doing_ajax_forms_' . $route);
            return $this->{$validRoutes[$route]}();
        }
        do_action('to-do/admin_ajax_handler_catch', $route);
    }

    protected function getAllCustomTodo()
    {
        //error data
        $error = false;
        $errors = array();

        $result = $this->_wpdb->get_results("SELECT * from $this->table", ARRAY_A);

        if(is_wp_error($result)){
            wp_send_json([
                'success' => false,
                'status' => 404,
                'message' => $result->get_error_message()
            ]);
            wp_die();
        }
        wp_send_json_success( $result, 200 );
        wp_die();
    }

    protected function addCustomTodo()
    {
        //error data
        $error = false;
        $errors = array();

        //get board data
        $data = array();
        $data['title'] = sanitize_text_field($_POST['title']);
        $data['list_limit'] = sanitize_text_field($_POST['list_limit']);
        $data['show_completed'] = sanitize_text_field($_POST['show_completed']);
        $data['theme'] = sanitize_text_field($_POST['theme']);

        // to_do data validation
        if (empty($data['title'])) {
            //title is empty
            $error = true;
            $errors['title'] = 'Todo Title is required.';
        }

        if(empty($data['list_limit'])){
            $data['list_limit'] = 10;
        }

        if(empty($data['theme'])){
            $data['theme'] = 'default';
        }

        //check error and send response
        if($error){
            wp_send_json_error( $errors );
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
        $where['id'] = $id;

        //get Data
        $data = array();
        $data['title'] = sanitize_text_field($_POST['title']);
        $data['list_limit'] = sanitize_text_field($_POST['list_limit']);
        $data['show_completed'] = sanitize_text_field($_POST['show_completed']);
        $data['theme'] = sanitize_text_field($_POST['theme']);

        // to_do data validation
        if (empty($data['title'])) {
            //title is empty
            $error = true;
            $errors['title'] = 'Todo Title is required.';
        }

        $result = $this->_wpdb->update($this->table, $data, $where);

        if($result){
            wp_send_json_success( $result, 200 );
        } else {
            return false;
        }

    }

    protected function  deleteCustomTodo()
    {
        //error data
        $error = false;
        $errors = array();

        //get ID
        $where = array();
        $id = sanitize_text_field($_POST['id']);
        $where['id'] = $id;
//      $where['creatorId'] = get_current_user_id();

        //delete
        $result = $this->_wpdb->delete($this->table, $where);

        if($result){
            wp_send_json_success( $result, 200 );
        } else {
            return false;
        }
    }
}

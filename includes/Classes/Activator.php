<?php

namespace toDo\Classes;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Ajax Handler Class
 * @since 1.0.0
 */
class Activator
{
    public function migrateDatabases($network_wide = false)
    {
        global $wpdb;
        if ($network_wide) {
            // Retrieve all site IDs from this network (WordPress >= 4.6 provides easy to use functions for that).
            if (function_exists('get_sites') && function_exists('get_current_network_id')) {
                $site_ids = get_sites(array('fields' => 'ids', 'network_id' => get_current_network_id()));
            } else {
                $site_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs WHERE site_id = $wpdb->siteid;");
            }
            // Install the plugin for all these sites.
            foreach ($site_ids as $site_id) {
                switch_to_blog($site_id);
                $this->migrate();
                restore_current_blog();
            }
        } else {
            $this->migrate();
        }
    }

    private function migrate()
    {
        /*
        * database creation commented out,
        * If you need any database just active this function bellow
        * and write your own query at createBookmarkTable function
        */
         $this->createToDoTable();
    }

    public function createToDoTable()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $wp_to_do_table = $wpdb->prefix . 'to_do';
        $custom_todo_table = $wpdb->prefix . 'custom_todo';

        $sql_to_do = "CREATE TABLE $wp_to_do_table (
          id INT NOT NULL AUTO_INCREMENT,
          title TEXT NOT NULL,
          list_limit INT(3) NOT NULL DEFAULT '10',
          show_completed TEXT NOT NULL DEFAULT 'yes',
          theme VARCHAR(10) NOT NULL DEFAULT 'default',
          PRIMARY KEY (id)
         ) $charset_collate;";

        $sql_custom_todo = "CREATE TABLE $custom_todo_table(
                 task_id INT AUTO_INCREMENT,
                 task_title VARCHAR(100) NOT NULL,
                 task_status VARCHAR(30) NOT NULL,
                 to_do_id INT NOT NULL, 
                 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                 FOREIGN KEY (to_do_id) REFERENCES $wp_to_do_table(id),
                 PRIMARY KEY (task_id)
                ) $charset_collate;";

//        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $this->runSQL( $sql_to_do , $wp_to_do_table);
        $this->runSQL($sql_custom_todo, $custom_todo_table);
    }

    /**
     * @param $sql
     * @param $tableName
     * @return void
     */
    private function runSQL($sql, $tableName)
    {
        global $wpdb;
        if ($wpdb->get_var("SHOW TABLES LIKE '$tableName'") != $tableName) {
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }
}

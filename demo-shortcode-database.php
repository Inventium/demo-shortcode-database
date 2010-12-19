<?php 
/*
 Plugin Name: Demo Shortcode Database
 Plugin URI: http://website-in-a-weekend.net/demo-plugins/
 Description: A brief description of the Plugin.
 Version: 0.1
 Author: Dave Doolin
 Author URI: http://website-in-a-weekend.net/
 */

/*  
Copyright (c) 2009, David M. Doolin
http://website-in-a-weekend.net/

All rights reserved.

Redistribution and use in source and binary forms, with or without modification, 
are permitted provided that the following conditions are met:

* Redistributions of source code must retain the above copyright notice, 
this list of conditions and the following disclaimer.

* Redistributions in binary form must reproduce the above copyright 
notice, this list of conditions and the following disclaimer in the 
documentation and/or other materials provided with the distribution.

* Neither the name of Website In A Weekend nor the names of its contributors 
may be used to endorse or promote products derived from this software 
without specific prior written permission.


THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, 
THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR 
PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR 
CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, 
EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, 
PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR 
PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY 
OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING 
NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS 
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
 
// FirePHP initialization. 
require_once('FirePHPCore/FirePHP.class.php');
ob_start();

/* On activation, register a new shortcode, and build a database table.
 * Find something to put into that database table.
 * Have the shortcode pull from the table.
 * On deactivation, disable the the shortcode and remove the database table.
 */
if (!class_exists("demo_shortcode_database")) {


    
    class demo_shortcode_database {

        private $tablename = 'shortcodedb';
        private $dbtable1 = '';  // Will be set in activation at some point
        
        function demo_shortcode_database() {
            
            add_shortcode('short_db_code', array(&$this, 'wiawcode_handler'));
            register_activation_hook(__FILE__, array(&$this, 'on_activation'));
            register_deactivation_hook(__FILE__, array(&$this, 'on_deactivation'));
        }
        
        function on_activation() {
 
            $firephp = FirePHP::getInstance(true); 
            $dt = date("l dS \of F Y h:i:s A");
            $firephp->log($dt,'on_activation');       	
            
			$this->create_table();		
        }
        
        function on_deactivation() {

            $firephp = FirePHP::getInstance(true); 
            $dt = date("l dS \of F Y h:i:s A");
            $firephp->log($dt,'on_deactivation');       	

            remove_shortcode('short_db_code');
			$this->drop_table();
        }
        
        function wiawcode_handler($atts) {
            
            global $wpdb;
            
            // Check this first...
            $output = "Shortcode Database text...";
            // Grab the recipe slug
            // Read up on shortcode_atts() function along
            // the extract() function.
            extract(shortcode_atts($atts));

            $firephp = FirePHP::getInstance(true); 
            $firephp->log($recipe,'wiawcode_handler');
            
            // then make a database call.
            $dbtable = $wpdb->prefix.$this->tablename;
            $query = "SELECT * FROM ".$dbtable. " WHERE slug = '".$recipe."'";
            $firephp->log($query,'wiawcode_handler');
            
            $demodata = $wpdb->get_row($query);
            
            // SELECT * from recipetable where slug = $slug
            //$output = dbresult;
            
            //return $output;
            return "Recipe name: ".$demodata->name;
        }
		
        function create_table() {
        
            global $wpdb;
            $dbtable = $wpdb->prefix.$this->tablename;
            
            $firephp = FirePHP::getInstance(true); 
            $dt = date("l dS \of F Y h:i:s A");
            $firephp->log($dt,'date/time');
            $firephp->log($dbtable,'create_table: dbtable');
            
            if ($wpdb->get_var("show tables like '$dbtable'") != $dbtable) {
        
                $sql = "CREATE TABLE ".$dbtable." (
        	        id mediumint(9) NOT NULL AUTO_INCREMENT,
        	        slug text NOT NULL,
        	        name text NOT NULL,
        	        UNIQUE KEY id (id)
        	        );";
        
                require_once (ABSPATH.'wp-admin/includes/upgrade.php');
                dbDelta($sql);        
            }
            
            
            // Stick something in the database to display on the options page.
            $dbdata = array('id' => 1,
                            'slug' => 'blue-corn-pozole',
                            'name' => 'Blue Corn Pozole');
            $wpdb->insert($dbtable, $dbdata);
            $dbdata = array('slug' => 'garlic-ancho-soup', 'name' => 'Garlic Ancho Soup');
            $wpdb->insert($dbtable, $dbdata);

            
        }

        /* find a plugin to use for an example for deleting 
         * database on uninstall.
         */
        function drop_table($plugin) {
        	
            global $wpdb;
            $wpdb->query("DROP TABLE ".$wpdb->prefix.$this->tablename);
        }
            
    }
}


$wpdpd = new demo_shortcode_database();

?>

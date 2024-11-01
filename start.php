<?php
 function autostart () {
   global $wpdb;
    if ( ! get_option('newstitle')){
      add_option('newstitle' , "");
    } else {
      update_option('newstitle' , "");
    }
	
	
	if ( ! get_option('newstext')){
      add_option('newstext' , "");
    } else {
      update_option('newstext' , "");
    }

   $table_name = "wp_newsdates";
   if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
      
      $sql = "CREATE TABLE " . $table_name . " (
	  nextdate TEXT NOT NULL
	  
	);";
	
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);
		
     }
	 $nextweek = date("W")+1;
		if($nextweek > 52) $nextweek=1;
		$wpdb->query("TRUNCATE wp_newsdates");
		$wpdb->insert('wp_newsdates',array('nextdate' => $nextweek), array( '%s' ));


	 $table_name = "wp_newsoptions";
  if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
      
      $sql = "CREATE TABLE " . $table_name . " (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  often text NOT NULL,
	  value text NOT NULL,
	  UNIQUE KEY id (id)
	);";
	
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);
		
     }
		$wpdb->query("TRUNCATE wp_newsoptions");
		$wpdb->query('INSERT INTO wp_newsoptions  VALUES(1,"often","weekly")');
		$wpdb->query('INSERT INTO wp_newsoptions  VALUES(2,"recent","5")');
		$wpdb->query('INSERT INTO wp_newsoptions  VALUES(3,"subject","")');
		$wpdb->query('INSERT INTO wp_newsoptions  VALUES(4,"head","")');
		$wpdb->query('INSERT INTO wp_newsoptions  VALUES(5,"email","")');
		$wpdb->query('INSERT INTO wp_newsoptions  VALUES(6,"smtphost","")');
		$wpdb->query('INSERT INTO wp_newsoptions  VALUES(7,"smtpuser","")');
		$wpdb->query('INSERT INTO wp_newsoptions  VALUES(8,"smtppass","")');
		$wpdb->query('INSERT INTO wp_newsoptions  VALUES(9,"port","")');
		$wpdb->query('INSERT INTO wp_newsoptions  VALUES(10,"length","")');
		
	  
	  $table_name = "wp_newsletteremails";
  if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
      
      $sql = "CREATE TABLE " . $table_name . " (
	  name text NOT NULL,
	  email text NOT NULL
	  
	);";
	
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);
     }
}
?>
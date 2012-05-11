<?php
	include("includes/db_config.php");	// including the database


	mysql_select_db($database);	// selecting the database
 

	 /*$user_details= "CREATE TABLE `user_details` (
					`user_id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
					`user_name` VARCHAR( 30 ) NOT NULL,
					`user_email` VARCHAR( 30 ) NOT NULL,
					`user_password` VARCHAR( 30 ) NOT NULL,
					`user_age` INT( 11 ) NOT NULL ,
					`user_gender` VARCHAR( 30 ) NOT NULL,
					`user_phone` BIGINT( 12 ) NOT NULL,
					`user_address` VARCHAR( 30 ) NOT NULL,
					`user_country` VARCHAR( 30 ) NOT NULL,
					`user_image` VARCHAR( 30 ) NOT NULL
					)";
 
	mysql_query($user_details) or die(mysql_error());
	 */
?>













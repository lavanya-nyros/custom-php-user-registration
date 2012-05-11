<?php

include("includes/db_config.php");	// Including database connection
        
            mysql_select_db($database);
			
		
			// insert a record	
			if(isset($_POST['new_form'])=='new_form')
			{	
				extract($_POST);
				$image= $_FILES["file"]["name"];
				$mylang = implode("|", $_POST["language"]); //This will give you a pipeline delimeted string like : computer|laptop|monitor

				$mylang = mysql_real_escape_string($mylang); //Just to santize before inserting in DB
				
				// if the email already exists in the database , it returns failure else inserts the record.
				$verify_email= mysql_query("select * from user_details where user_email = '".$email."'");
				$email_rows= mysql_num_rows($verify_email);
				 
				 if($email_rows>0)	// if admin exists
				 {
					@header('Location:index.php?action=failure');
				 }
				else	
				{
					$sql_2="insert into user_details(user_name,user_email,user_password,user_age,user_gender,user_phone,user_address,user_country,user_image,user_language)
				values('$username','$email','$password','$age','$gender','$phone','$address','$country','$image','$mylang')";  
				
					mysql_query($sql_2);
				
					@header('Location:index.php?action=success');
				}
				
			}
			
		
?>            


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html> 

<head> 
	<title>User Details </title> 
 </head>
 
 <body>
 </body>
 </html>
    

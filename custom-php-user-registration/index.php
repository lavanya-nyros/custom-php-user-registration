<?php

	// ================php code for captcha===============//
	
	// Make the page validate
	ini_set('session.use_trans_sid', '0');
	
	// Include the random string file
	require 'rand.php';
	
	// Begin the session
	session_start();
	
	// Set the session contents
	$_SESSION['captcha_id'] = $str;	
	
	
	// ================Code for Fconnect===============//
	
	require 'src/facebook.php';
	
	// Create our Application instance (replace this with your appId and secret).
	$facebook = new Facebook(array(
	  'appId'  => '***********',
	  'secret' => '***********',
	));
	
	// Get Facebook User ID
	$user = $facebook->getUser();
	
	// We may or may not have this data based on whether the user is logged in.
	// If we have a $user id here, it means we know the user is logged into
	// Facebook, but we don't know if the access token is valid. An access
	// token is invalid if the user logged out of Facebook.
	
	if ($user) {
	  try {
		// Proceed knowing you have a logged in user who's authenticated.
		$user_profile = $facebook->api('/me');
		
	  } 
	  catch (FacebookApiException $e) {
		error_log($e);
		$user = null;
	  }
	}
	
	// Login or logout url will be needed depending on current user state.
	if ($user_profile) {
	  $logoutUrl = $facebook->getLogoutUrl();
	} else {
	  	$loginUrl = $facebook->getLoginUrl(array(
	  	 'scope' => 'email,user_birthday'	
		));
	}
	
	require 'includes/db_config.php';	// Including database connection
			
	mysql_select_db($database);
	
	
	if ($user_profile) {
	
		$Birthdate= $user_profile["birthday"];
		
		// function to convert date of birth into age.
		function getAge($Birthdate)
		{
			// Explode the date into meaningful variables
			list($BirthMonth,$BirthDay,$BirthYear) = explode("/", $Birthdate);
			
			// Find the differences
			$YearDiff = date("Y") - $BirthYear;
			$MonthDiff = date("m") - $BirthMonth;
			$DayDiff = date("d") - $BirthDay;
			// If the birthday has not occured this year
			if ($DayDiff < 0 || $MonthDiff < 0)
			  $YearDiff--;
			return $YearDiff;
		}
		
		$age= getAge($Birthdate);	// calls getAge function and stores the age in $age.
		
		$res = mysql_query("SELECT user_email FROM user_details WHERE user_email='$user_profile[email]'");
	
		$row= mysql_num_rows($res);	// gives the number of rows selected
	
		if($row>0)	// if the user already exists in database update his/her information
		{
			$update_fb = 	"UPDATE 
									user_details 
								SET
									user_name = '" .$user_profile[name]. "',
									user_email = '" .$user_profile[email]. "',
									user_age = '" .$age. "',
									user_gender = '" .$user_profile[gender]. "',
										
								WHERE
									user_email = " .$user_profile[email]; 
										
			mysql_query($update_fb);
		 }
		else	// else insert his information into database
		{  
			$sql_insert = "INSERT INTO user_details (user_name,user_email,user_age,user_gender) VALUES ('$user_profile[name]','$user_profile[email]','$age','$user_profile[gender]')";
			
			mysql_query($sql_insert) or die("Error: ". mysql_error(). " with query ". $query);
		}
	} 


?>

<!DOCTYPE html PUBLIC "-//W3Czz//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
	<html> 
	  <head> 
	  <title>User Registration</title> 
      
      <!-- ======================= CSS file ========================= -->
      <link href="includes/css/style.css" rel="stylesheet" type="text/css" />
      
      <!-- ======================= Script files ========================= -->
	  <script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script> 
	  <script type="text/javascript" src="includes/js/jquery.validate.js"></script> 
      
    
 </head> 
 
 
 <body> 
 <div id="main">
 
 <?php if (!$user): ?>	
 
 	<!-- Header starting -->
     <div id="header">
     	<h1> Sign Up </h1>
        <?php 
			if($_GET['action'] == "success")	// executed when the records are successfully inserted
			{ ?>
				<h4 style="color:#669900; float: right;">You have registered successfully</h4>
		<?php 	} ?>
        
       
		
     </div><!-- end of header -->
        
        
     <!-- Content div starting -->
     <div id="content">
     
         <div class="registration_form">
          
            <h3>Create your own account</h3><br />
              
            <form id="registration_form" name="registration_form" method="post" action="insert_user.php" enctype="multipart/form-data" >
             
             
                <!-- hidden type for checking the condition while inserting the data-->
                <input type="hidden" name="new_form" id="new_form" value="new_form" /> 
               
               
               <!-- ======= UserName field using TextBox ====== -->
                <p>
                    <label>UserName *</label>
                    <input type="text" name="username" placeholder="User Name" />
                </p>
               
               
               <!-- ======= Email field using TextBox with email validation ====== -->
                <p>
					 <?php 
                        if($_GET['action'] == "failure")	// executed when the records are successfully inserted
                        { ?>
                            <h5 style="color:red; float: right;">your email already exists</h5>
                    <?php 	} ?>
                    <label for="email">Email *</label>
                    <input id="email" name="email" type="text" placeholder="Enter your Email" value=""/>
                    
                </p>
                
                
                
                <!-- ======= Password field using password type ====== -->
                <p>
                
                    <label for="password">Password *</label>
                    <input id="password" name="password" type="password" placeholder="Password" value="" />
                    
                   
                </p>
                
                <p>
                
                    <label for="confirm_password">Confirm password *</label>
                    <input id="confirm_password" name="confirm_password" type="password" placeholder="Repeat Password" />
                
                </p>
                
                
                <!-- ======= Age field using TextBox ====== -->
                <p>
                    <label>Age *</label>
                    <input type="text" name="age" id="age" placeholder="Enter your Age"/>
                   
                </p>
                
                
                <!-- ======= Gender field using Radio buttons ====== -->
                <p>
                    <label for="gender">Gender</label>
                    <input type="radio" name="gender" value="male" /><span style="margin:0 30px 0 3px;">Male</span>
                    <input type="radio" name="gender" value="female" checked /><span> Female</span>
                       
                </p>
                
                
                <!-- ======= Phone field using Masked  ====== -->
                <p>
                    <label for="phone">Phone:</label>
                    <input id="phone" maxlength="14" name="phone" type="text" class="required phone" value="" placeholder="Phone Number" />
                       
                </p>
                
               
                <!-- ======= Address field using TextArea ====== -->
                <p>
                
                    <label for="address">Address </label>
                    <textarea id="textarea" name="address"></textarea> 
                    
                </p>
                
                
                <!-- ======= single select Combo box ====== -->
                <p>
                
                    <label for="country">Country</label>
                    <select name="country" id="country">
                    	<option></option>
                        <option value="australia">Australia</option>
                        <option value="africa">Africa</option>
                        <option value="india">India</option>
                        <option value="usa">USA</option>
                        <option value="japan">Japan</option>
                        <option value="nepal">Nepal</option>
                        <option value="germany">Germany</option>
                        <option value="england">england</option>
                    </select>
                    
                </p>
                
                
                
                 <!-- ======= Multi select Combo box ====== -->
                <p>
                
                    <label for="language">Languages you know</label>
                    <select name="language[]" multiple="multiple" id="language">
                        <option value="english">English</option>
                        <option value="telugu">Telugu</option>
                        <option value="hindi">Hindi</option>
                        <option value="tamil">Tamil</option>
                        <option value="malayalam">Malayalam</option>
                        <option value="panjabi">Panjabi</option>
                        <option value="urdhu">Urdhu</option>
                        <option value="marathi">Marathi</option>
                    </select>
                    
                </p>
                
                
                
                 <!-- ======= Uploading an image ====== -->
                <p>
                    <label for="file">Upload your photo </label>
                    <input type="file" name="file" id="file"  onclick="progress();"/>
                    <div id="progressbar-container" style="display:none;">  
                        <div id="progressbar">  
                            <div id="progressbar-fill"></div>  
                        </div>  
                    </div> 
                   
                </p>
                
                <p>
                <!-- ======= Captcha ====== -->
                 <label for="captcha">Enter the characters as seen on the image below (case insensitive):</label>
                   <input type="text" maxlength="6" name="captcha" id="captcha" />
                                 <span id="captchaimage" style="margin-left:5px;"><a href="" id="refreshimg" title="Click to refresh image"><img src="includes/images/image.php?<?php echo time(); ?>" width="132" height="46" alt="Captcha image" id="capimg" /></a></span>
            </p>
                
                
                 <!-- ======= Check box for agree ====== -->
                <p>
                    <label for="agree" class="checkbox" style="width:300px; font-weight: normal;">
                    Agree to our Terms<input type="checkbox" class="checkbox" id="agree" name="agree" /></label>
                    
                </p>
                
               
                <!-- Submit button -->
                <div class="button">
                       <input class="submit btn btn-success" type="submit" name="submit" value="Create an account" s><br /><br />
                </div>
                  
            </form> 
                
          </div>
      <?php endif ?>   
       
       
       <!-- ============= When the user logs in it shows the user details ====== --> 
       <?php if ($user): ?>
          <a href="<?php echo $logoutUrl; ?>">Logout</a>
          <h3>Welcome <?php print_r($user_profile["first_name"]); ?> </h3>
          <img src="https://graph.facebook.com/<?php echo $user; ?>/picture">
         
          <?php 
            session_destroy(); 	// destroy the session to delete the user details from browser after logout.
					
           else: ?>		<!-- === else show the login page === -->
          
             <div id="right_panel">
               <b><i style="font-size:large;color: #999999;"> Or </i></b><br />
               <a href="<?php echo $loginUrl; ?>"><img src="includes/images/facebook-connect.png" alt="" /></a>
             </div>
          
       <?php endif ?>
        
          
        </div><!-- End of Content div -->
        
        <?php if (!$user): ?>
        <!-- ==footer== -->
        <div id="footer">
        	
        </div>
        <?php endif ?>
      
   </div><!-- main div closing -->
 </body>
   
</html> 

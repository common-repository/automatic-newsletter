<?php
/*
Plugin Name: Automatic Newsletter
Plugin URI: http://craigfarnes.co.uk/
Description: Automaticly send a newsletter daily,weekly or monthly.
Author: Craig Farnes
Version: 3.0
Author URI: http://craigfarnes.co.uk/
*/
require_once "Mail.php";
include('Mail/mime.php');
include('start.php');
include('newsfunctions.php');
include('widget.php');
register_activation_hook(__FILE__,'autostart');



add_action( 'wp_head', 'javascripthead' );
function javascripthead() {
echo ' <script language = "Javascript">
/**
 * DHTML email validation script. Courtesy of SmartWebby.com (http://www.smartwebby.com/dhtml/)
 */

function echeck(str) {

		var at="@"
		var dot="."
		var lat=str.indexOf(at)
		var lstr=str.length
		var ldot=str.indexOf(dot)
		if (str.indexOf(at)==-1){
		   alert("Invalid E-mail ID")
		   return false
		}

		if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr){
		   alert("Invalid E-mail ID")
		   return false
		}

		if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr){
		    alert("Invalid E-mail ID")
		    return false
		}

		 if (str.indexOf(at,(lat+1))!=-1){
		    alert("Invalid E-mail ID")
		    return false
		 }

		 if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot){
		    alert("Invalid E-mail ID")
		    return false
		 }

		 if (str.indexOf(dot,(lat+2))==-1){
		    alert("Invalid E-mail ID")
		    return false
		 }
		
		 if (str.indexOf(" ")!=-1){
		    alert("Invalid E-mail ID")
		    return false
		 }

 		 return true					
	}

function ValidateForm(){
	var emailID=document.frmSample.email
	
	if ((emailID.value==null)||(emailID.value=="")){
		alert("Please Enter your Email ID")
		emailID.focus()
		return false
	}
	if (echeck(emailID.value)==false){
		emailID.value=""
		emailID.focus()
		return false
	}
	return true
 }
</script>';
}

add_action( is_admin() ? 'admin_head' : 'wp_head', 'load_into_head212' ); 
function load_into_head212() {  
	global $wpdb;
	$wpdb->show_errors();
	$when = news_get_options(1);
	$number = $wpdb->get_row("SELECT * FROM wp_newsoptions WHERE id=2");
	$number = $number->value;
	$next = $wpdb->get_row("SELECT * FROM wp_newsdates");
	$next = $next->nextdate;
	//echo $when;
	if($when=="Daily"){
		if(date("j")>=$next){
			news_mail();
			//add check if over days of the month etc
			$wpdb->query("TRUNCATE wp_newsdates");
			$nextday = date("j")+1;
			$month = date("n");
			switch ($month){
				case 1:
				$days=31;
				break;
				
				case 2:
				$days=28;
				break;
				
				case 3:
				$days=31;
				break;
				
				case 4:
				$days=30;
				break;
				
				case 5:
				$days=31;
				break;
				
				case 6:
				$days=30;
				break;
				
				case 7:
				$days=31;
				break;
				
				case 8:
				$days=31;
				break;
				
				case 9:
				$days=30;
				break;
				
				case 10:
				$days=31;
				break;
				
				case 11:
				$days=30;
				break;
				
				case 12:
				$days=31;
				break;
			}
			if($nextday > $days){
			$nextday = 1;
			}
			$wpdb->insert('wp_newsdates',array('nextdate' => $nextday), array( '%s' ));
		}
		}
	
	if($when=="weekly"){
		//echo date("W");
		if($next == date("W")){
			news_mail();
		}
		$nextweek = date("W")+1;
		if($nextweek > 52) $nextweek=1;
		$wpdb->query("TRUNCATE wp_newsdates");
		$wpdb->insert('wp_newsdates',array('nextdate' => $nextweek), array( '%s' ));
	}
	if($when=="Monthly"){
		if($next == date("n")){
			news_mail();
		}
		$nextmonth = date("n")+1;
		if($nextmonth > 12) $nextmonth=1;
		$wpdb->query("TRUNCATE wp_newsdates");
		$wpdb->insert('wp_newsdates',array('nextdate' => $nextmonth), array( '%s' ));
	}
}


add_action('admin_menu', 'autonewspage');
function autonewspage(){
	add_options_page('options-general.php','Automatic Newsletter Settings','administrator','autonews','autonewsdisplay');
}
function autonewsdisplay() {
global $wpdb;
$often = news_get_options(1);
?>
<h1>Automatic Newsletter Settings</h1>
<form action="" method="POST">
How often to send newsletters:
<select name="often">
<?php
if($often == "Daily"){
echo '<option value="Daily" selected="selected">Daily</option>';
}else{ 
echo '<option value="Daily">Daily</option>';
}
if($often == "weekly"){
echo '<option value="weekly" selected="selected">Weekly</option>';
}else{
echo '<option value="weekly">Weekly</option>';
}
if($often == "Monthly"){
echo '<option value="Monthly" selected="selected">Monthly</option>';
}else{
echo '<option value="Monthly">Monthly</option>';
}
?>

</select><br />
<br />
Number of recent posts to email:<input value="<?php echo news_get_options(2); ?>" name="recent"><br />
Subject:<input name="subject" value="<?php echo news_get_options(3); ?>"><br />
Header Text:<textarea name="header"><?php echo news_get_options(4); ?></textarea><br />
Length of Post Extract (letters):<input name="length" value="<?php echo news_get_options(10); ?>"><br />
<input type="Submit" name="often2" value="Save">
</form>
<?php

if(isset($_POST['often2'])){
	//$wpdb->query("TRUNCATE wp_newsoptions");
         $often=$_POST['often'];
         $wpdb->query("UPDATE wp_newsoptions SET value='$often' WHERE id= 1");
	
	
	$length=$_POST['length'];
	$when = $wpdb->get_row("SELECT * FROM wp_newsoptions WHERE id=1");
	$number = $wpdb->get_row("SELECT * FROM wp_newsoptions WHERE id=2");
	$number = $number->value;
	$next = $wpdb->get_row("SELECT * FROM wp_newsdates");
	$next = $next->nextdate;
	$when=$_POST['often'];
	often($when);
	$often=$_POST['recent'];
    $wpdb->query("UPDATE wp_newsoptions SET value='$often' WHERE id= 2");
	$subject = $_POST['subject'];
	$head = $_POST['header'];
	$wpdb->query("UPDATE wp_newsoptions SET value='$subject' WHERE id= 3");
	$wpdb->query("UPDATE wp_newsoptions SET value='$head' WHERE id= 4");
	$wpdb->query("UPDATE wp_newsoptions SET value='$length' WHERE id= 10");
	
	echo '<meta http-equiv="refresh" content="1">';
	echo "Saved";
}




?>
<form action="" method="POST">
<h3>SMTP (leave fields blank to use php mail function)</h3>
SMTP Hostname:<input name="host" value="<?php echo news_get_options(6); ?>" ><br />
SMTP Username:<input name="username" value="<?php echo news_get_options(7); ?>"><br />
SMTP Password:<input name="password" value="<?php echo news_get_options(8); ?>"><br />
SMTP Port(default for most is 25):<input name="port" value="<?php echo news_get_options(9); ?>"><br />
<input type="submit" value="Save" name="smtp"><br />
</form>
<?php
if(isset($_POST['smtp'])){
	$host = $_POST['host'];
	$user = $_POST['username'];
        $pass = $_POST['password'];
        $port = $_POST['port'];
	$wpdb->query("UPDATE wp_newsoptions SET value='$host' WHERE id= 6");
	$wpdb->query("UPDATE wp_newsoptions SET value='$user' WHERE id= 7");
        $wpdb->query("UPDATE wp_newsoptions SET value='$pass' WHERE id= 8");
        $wpdb->query("UPDATE wp_newsoptions SET value='$port' WHERE id= 9");
	echo "Saved";
	echo '<meta http-equiv="refresh" content="1">';
}
if(isset($_POST['send'])){
	news_mail();
}
?><br />
<h3>Email Management</h3><br />
<form action="" method="POST">
<input type="submit" name="send" value="Send a Newsletter Now">
</form>
<?php
//print_r (news_posts(1));
$wpdb->show_errors();
$get = $wpdb->get_row("SELECT * FROM wp_newsoptions WHERE id=2");
$recent = $get->value;
$get = $wpdb->get_row("SELECT * FROM wp_newsoptions WHERE id=1");
$often = $get->value;
?>
<?php


echo '<form action="" method="POST">';
echo 'Email Address (to be used to send Emails) <input name="sendemail" value='.news_get_options(5).'><input type="submit"></form>';
echo '<form action="" method="POST">';
echo '<select name="emails">'; 
$emailsn = $wpdb->get_var("SELECT COUNT(*) FROM wp_newsletteremails");
$j=0;
while($emailsn != $j){
$emails = $wpdb->get_row("SELECT * FROM wp_newsletteremails",OBJECT,$j);
$email = $emails->email;
echo "<option value=".$email.">".$email."</option>";
$j++;
}
echo "</select>";
echo '<input type="submit" name="remove" value="Remove">';
echo '</form>';
if(isset($_POST['remove'])){
	$wpdb->query("DELETE FROM wp_newsletteremails WHERE email='".$_POST['emails']."'");
	echo '<meta http-equiv="refresh" content="1">';
	echo "Removed";
}

if(isset($_POST['sendemail'])){
	$often = $_POST['sendemail'];
	$wpdb->query("UPDATE wp_newsoptions SET value='$often' WHERE id= 5");
	echo "saved";
	echo '<meta http-equiv="refresh" content="1">';
}
?>
<form action="" method="POST">
For Bulk Email Submission seperate each Email with a comma (,)<br />
Email:<input name="email2">
<input type="submit" name="add" value="Add Email">
</form>
<?php
if(isset($_POST['add'])){
	$pieces = explode(",", $_POST['email2']);
	print_r($pieces);
	foreach($pieces as $email){
	echo $email;
	$wpdb->insert( 'wp_newsletteremails', array( 'email' => $email), array( '%s'));
	}
	echo "Added";
	//echo '<meta http-equiv="refresh" content="1">';
}
$get = $wpdb->get_row("SELECT * FROM wp_newsoptions WHERE id=3");
$subject = $get->value;
$get = $wpdb->get_row("SELECT * FROM wp_newsoptions WHERE id=4");
$head = $get->value;

}

?>
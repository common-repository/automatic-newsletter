<?php
add_action("widgets_init", array('newsletter', 'register'));
class newsletter {
  function control(){
    $data = get_option('newstitle');
    $data2 = get_option('newstext');
	
	?>
	<form action="" method="POST">
	Title:<input name="Title" value=<?php echo $data; ?>><br />
	Text Above Email Box:<textarea name="text"><?php echo $data2; ?></textarea>
	</form>
	<?php
	//print_r($_POST);
	if(isset($_POST['Title'])){
	//echo "ran";
	update_option('newstitle', $_POST['Title']);
	update_option('newstext', $_POST['text']);
	}
  }
  function widget($args){
  $data2 = get_option('newstext');
  $data = get_option('newstitle');
    echo $args['before_widget'];
    echo $args['before_title'] .$data. $args['after_title'];
	//print_r($data);
	echo $data2;
    ?>
	<form action="" method="POST" name="frmSample" onSubmit="return ValidateForm()">
Name:<input name="name"><br />
Email:<input name="email"><br />
Subscribe:<input type="radio" name="subscribe" value="subscribe" checked><br />
Un-Subscribe<input type="radio" name="subscribe" value="unsubscribe"><br />
<input type="submit" name="submit">
</form>
<?php
if(isset($_POST['submit'])){
	//print_r($_POST);
	global $wpdb;
	if($_POST['subscribe']=='subscribe'){
	$emails = $wpdb->get_row("SELECT * FROM `wp_newsletteremails` WHERE email='".$_POST['email']."'");
	//print_r ($emails);
	if($emails != NULL){
	echo "Email Already Exists";
	}else{
	$wpdb->insert( 'wp_newsletteremails', array( 'name' => $_POST['name'], 'email' => $_POST['email']), array('%s','%s') );
	echo "Email Added, Thank you";
	}
	}else{
	$wpdb->query("DELETE FROM wp_newsletteremails WHERE email = '".$_POST['email']."'");
	echo "You have Un-subscribed";
	}
}
    echo $args['after_widget'];
  }
  function register(){
    register_sidebar_widget('Newsletter', array('newsletter', 'widget'));
    register_widget_control('Newsletter', array('newsletter', 'control'));
  }
}
?>
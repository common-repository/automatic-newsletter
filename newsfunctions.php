<?php
	function news_mail(){
		global $wpdb;
		$emails = $wpdb->get_results("SELECT * FROM `wp_newsletteremails`"); 
		$user = news_get_options(7);
		$host = news_get_options(6);
		$pass = news_get_options(8);
		$sendemail = news_get_options(5);
		$subject = news_get_options(3);
		$recent = news_get_options(2);
		$head = news_get_options(4);
		
		$posts = news_posts($recent);
		$j=1;
		$message=$head."<br />";
		while($j != $recent){
			$message.="<p><h2><a href=".$posts[3][$j].">".$posts[1][$j]."</a></h2>".$posts[2][$j]."<br /><br /> <a href=".$posts[3][$j].">View More..</a><br /></p><br />";
			$j++;
		}
		//print_r($posts);
		//echo $to;
		//echo $subject;
		//echo $message;
		foreach ($emails as $to) {
		$name=$to->name;
		$to=$to->email;
		
		if($host == NULL){
		mail($to, $subject, $message,"To: ".$name." <".$to.">\n" .   
		"From: ".$sendemail." \n".
		"MIME-Version: 1.0\n" .   
		"Content-type: text/html; charset=iso-8859-1");
		echo "Sent!";
		}else{
		smtp($user,$host,$pass,$message,$subject,$to,$sendemail);
		echo "Sent!!";
		
		}
		
		
		}
		}
	function smtp($user,$host,$pass,$message,$subject,$to,$sendemail){
	
		$html = $message;
		$crlf = "\n";
		$hdrs = array(
              'From'    => $sendemail,
              'Subject' => $subject
              );

		$mime = new Mail_mime($crlf);

		$mime->setTXTBody($text);
		$mime->setHTMLBody($html);

		//do not ever try to call these lines in reverse order
		$body = $mime->get();
		$hdrs = $mime->headers($hdrs);

		$mail =& Mail::factory('mail');
		$mail->send($to, $hdrs, $body);

	};
	
	function often(){
		if($when=="Daily"){
		
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
		
	
		if($when=="weekly"){
			//echo date("W");
			
			$nextweek = date("W")+1;
			if($nextweek > 52) $nextweek=1;
			$wpdb->query("TRUNCATE wp_newsdates");
			$wpdb->insert('wp_newsdates',array('nextdate' => $nextweek), array( '%s' ));
		}
		if($when=="Monthly"){
			
			$nextmonth = date("n")+1;
			if($nextmonth > 12) $nextmonth=1;
			$wpdb->query("TRUNCATE wp_newsdates");
			$wpdb->insert('wp_newsdates',array('nextdate' => $nextmonth), array( '%s' ));
		}
	}
	 
	function news_posts ($limit)
	{
		global $wpdb;
		$fivesdrafts = $wpdb->get_results("SELECT * FROM `wp_posts` WHERE post_status='publish' AND post_type='post' ORDER BY post_date DESC LIMIT 0,".$limit); 
		$url = $wpdb->get_row("SELECT * FROM `wp_options` WHERE option_id=2");
		$j=1;
		foreach ($fivesdrafts as $fivesdraft) {
			$return[1][$j] = $fivesdraft->post_title;
			$return[2][$j] = $fivesdraft->post_content;
			
			$return[2][$j] = substr($return[2][$j],0,news_get_options(10));
			$return[3][$j] = $url->option_value;
			$return[3][$j] = $return[3][$j]."/?p=".$fivesdraft->ID;
			$j++;
		}
		
		return $return;
	}
	function news_get_options($id){
		global $wpdb;
		$number = $wpdb->get_row("SELECT * FROM wp_newsoptions WHERE id=".$id);
		$number = $number->value;
		return $number;
	}
?>
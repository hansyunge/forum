<?php
//check for required fields from the form
if ((!$_POST['topic_owner']) || (!$_POST['topic_title']) || (!$_POST['post_text'])) {
     header("Location: addtopic.html");
     exit;
   }
   
$dbname = 'st_forum';
$dbuser = 'hyunge';
$dbpass = 'password';
$dbhost = 'localhost';


   //connect to server and select database
  $conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname) or die(mysqli_error($conn));
  mysqli_select_db($conn,$dbname) or die(mysqli_error($conn));
  
  //create and issue the first query
  $add_topic = "INSERT INTO forum_topics (topic_id, topic_title, topic_create_time, topic_owner) values ('', '$_POST[topic_title]', now(), '$_POST[topic_owner]')";
	  
  mysqli_query($conn,$add_topic) or die(mysqli_error($conn));
  
  //get the id of the last query

$topic_id = $conn->insert_id;
$topic_title = $_POST['topic_title'];
  
  //create and issue the second query
  $add_post = "INSERT INTO forum_posts (post_id, topic_id, post_text, post_create_time, post_owner) values ('', '$topic_id', '$_POST[post_text]', now(), '$_POST[topic_owner]')";
  
  mysqli_query($conn,$add_post) or die(mysqli_error($conn));
  
  //create nice message for user
  $msg = "<P>The <strong>$topic_title</strong> topic has been created.</p>";
  ?>
  <html lang="en-us">
  <head>
  <title>New Topic Added</title>
  </head>
  <body>
  <h1>New Topic Added</h1>
  <?php print $msg; ?>
  </body>
  </html>
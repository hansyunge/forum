  <?php
  $dbname = 'st_forum';
  $dbuser = 'hyunge';
  $dbpass = 'password';
  $dbhost = 'localhost';

   //check for required info from the query string
   if (!$_GET['topic_id']) {
      header("Location: topiclist.php");
      exit;
   }

   //connect to server and select database
   $conn = mysqli_connect($dbhost, $dbuser, $dbpass)
      or die(mysqli_error($conn));
  mysqli_select_db($conn,$dbname) or die(mysqli_error($conn));

  //verify the topic exists
  $verify_topic = "select topic_title from forum_topics where
      topic_id = $_GET[topic_id]";
  $verify_topic_res = mysqli_query( $conn,$verify_topic)
      or die(mysqli_error($conn));

  if (mysqli_num_rows($verify_topic_res) < 1) {
      //this topic does not exist
     $display_block = "<P><em>You have selected an invalid topic.
      Please <a href=\"topiclist.php\">try again</a>.</em></p>";
  } else {
      //get the topic title
     $topic_title = stripslashes(mysqli_result($verify_topic_res,0,
          'topic_title'));

     //gather the posts
     $get_posts = "select post_id, post_text, date_format(post_create_time,
          '%b %e %Y at %r') as fmt_post_create_time, post_owner from
          forum_posts where topic_id = $_GET[topic_id]
          order by post_create_time asc";

     $get_posts_res = mysqli_query($conn,$get_posts) or die(mysqli_error($conn));

     //create the display string
     $display_block = "
     <P>Showing posts for the <strong>$topic_title</strong> topic:</p>
  
     <table width=100% cellpadding=3 cellspacing=1 border=1>
     <tr>
     <th>AUTHOR</th>
     <th>POST</th>
     </tr>";

     while ($posts_info = mysqli_fetch_array($get_posts_res)) {
         $post_id = $posts_info['post_id'];
         $post_text = nl2br(stripslashes($posts_info['post_text']));
         $post_create_time = $posts_info['fmt_post_create_time'];
         $post_owner = stripslashes($posts_info['post_owner']);

         //add to display
         $display_block .= "
         <tr>
         <td width=35% valign=top>$post_owner<br>[$post_create_time]</td>
         <td width=65% valign=top>$post_text<br><br>
          <a href=\"replytopost.php?post_id=$post_id\"><strong>REPLY TO
          POST</strong></a></td>
         </tr>";
     }

     //close up the table
     $display_block .= "</table>";
  }

  function mysqli_result($res,$row=0,$col=0){
      $numrows = mysqli_num_rows($res);
      if ($numrows && $row <= ($numrows-1) && $row >=0){
          mysqli_data_seek($res,$row);
          $resrow = (is_numeric($col)) ? mysqli_fetch_row($res) : mysqli_fetch_assoc($res);
          if (isset($resrow[$col])){
              return $resrow[$col];
          }
      }
      return false;
  }
  ?>
  <html>
  <head>
  <title>Posts in Topic</title>
  </head>
  <body>
  <h1>Posts in Topic</h1>
  <?php print $display_block; ?>
  </body>
  </html>

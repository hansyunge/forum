   <?php
   error_reporting(0); // undealt with warning on line 15 - undefine array key 'OP'
   
   $dbname = 'st_forum';
   $dbuser = 'hyunge';
   $dbpass = 'password';
   $dbhost = 'localhost';

   //connect to server and select database; we'll need it soon
   $conn = mysqli_connect($dbhost, $dbuser, $dbpass)
       or die(mysqli_error($conn));
   mysqli_select_db($conn,$dbname) or die(mysqli_error($conn));

  //check to see if we're showing the form or adding the post

   if ($_POST["op"] != "addpost") { // add ?? "" to get rid of warning
          // showing the form; check for required item in query string
         if (!$_GET['post_id']) {
             header("Location: topiclist.php");
             exit;
         }

         //still have to verify topic and post

       $verify = "select ft.topic_id, ft.topic_title from
        forum_posts as fp left join forum_topics as ft on
        fp.topic_id = ft.topic_id where fp.post_id = $_GET[post_id]";

         $verify_res = mysqli_query($conn,$verify ) or die(mysqli_error($conn));
          if (mysqli_num_rows($verify_res) < 1) {
             //this post or topic does not exist
             header("Location: topiclist.php");
             exit;
         } else {
             //get the topic id and title
             $topic_id = mysqli_result($verify_res,0,'topic_id');
             $topic_title = stripslashes(mysqli_result($verify_res,
              0,'topic_title'));

             print "
             <html lang='en-us'>
             <head>
             <title>Post Your Reply in $topic_title</title>
             </head>
             <body>
             <h1>Post Your Reply in $topic_title</h1>
             <form method=post action=\"$_SERVER[PHP_SELF]\" name= 'addpost'>  <!-- from 'addpost' to 'op' -->
      
             <p><strong>Your E-Mail Address:</strong><br>
             <input type=\"text\" name=\"post_owner\" size=40 maxlength=150>
      
             <P><strong>Post Text:</strong><br>
             <textarea name=\"post_text\" rows=8 cols=40 wrap=virtual></textarea>
      
             <input type=\"hidden\" name=\"op\" value=\"addpost\">
             <input type=\"hidden\" name=\"topic_id\" value=\"$topic_id\">
      
             <P><input type=\"submit\" name=\"addpost\" value=\"Add Post\"></p>
      
             </form>
             </body>
             </html>";
         }
          } else if ($_POST['op'] == "addpost") {
              //check for required items from form
              if ((!$_POST['topic_id']) || (!$_POST['post_text']) ||
                  (!$_POST['post_owner'])) {
                  header("Location: topiclist.php");
                  exit;
              }

              //add the post
              $add_post = "insert into forum_posts values ('', '$_POST[topic_id]',
         '$_POST[post_text]', now(), '$_POST[post_owner]')";
              mysqli_query($conn, $add_post) or die(mysqli_error($conn));

              //redirect user to topic
              header("Location: showtopic.php?topic_id=$topic_id");
              exit;
          }
   // }
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

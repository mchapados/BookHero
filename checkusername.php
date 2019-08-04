<!--
  ____________________________________________________________
   FILE: checkusername.php (php script)
   DESCRIPTION: Searches database for a username specified in
   $_GET['username']
  ____________________________________________________________

-->
<?php
  include 'includes/library.php';
  $pdo = connectdb();
  // search database for username
  $stmt = $pdo->prepare("SELECT username FROM project_users WHERE username = ?");
  $stmt->execute([$_GET['username']]);
  // return true if match found
  if ($stmt->rowCount() > 0) {
    echo true;
  } else {
    echo false;
  }
?>

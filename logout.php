<!--
  ____________________________________________________________
   FILE: logout.php (php script)
   DESCRIPTION: Logs user out of their account and redirects
   to home page
  ____________________________________________________________

-->
<?php
session_start();
if (isset($_SESSION['id'])) {
  // reset session variables
  unset($_SESSION['id']);
  unset($_SESSION['username']);
  session_destroy(); }
// redirect to home page
header('Location: index.php');
exit();
?>

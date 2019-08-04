<!--
  ____________________________________________________________
   FILE: recoverpassword.php
   DESCRIPTION: Book Hero password recovery page
   Allows user to reset their forgotten password
  ____________________________________________________________

-->
<?php
session_start();
// redirect if user is already logged in
if (isset($_SESSION['id'])) {
  header('Location: books.php');
  exit();
}

include 'includes/library.php';
$pdo = connectdb(); // connect to database

$question = "";
$formDisplay = "display: none;";
$userID = "";
$result = "";

if (isset($_POST['getQ'])) {
  $username = strip_tags($_POST['user']);
  // search database for security question
  $stmt = $pdo->prepare("SELECT id, securityQuestion FROM project_users WHERE username = ?");
  $stmt->execute([$username]);
  if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch();
    $question = $row['securityQuestion'];
    $formDisplay = "";
    $userID = $row['id'];
  } else { // username not found
    $result = "<span class='error'>ERROR -- username not found</span>";
  }
}

if (isset($_POST['submit'])) {
  // search database for security answer
  $stmt = $pdo->prepare("SELECT id, securityQuestion, securityAnswer FROM project_users WHERE id = ?");
  $stmt->execute([$_POST['userID']]);
  if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch();
    $question = $row['securityQuestion'];
    $formDisplay = "";
    $userID = $row['id'];
    // check if answers match
    if (password_verify(strtoupper($_POST['answer']), $row['securityAnswer'])) {
      // update database
      $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);
      $stmt = $pdo->prepare("UPDATE project_users SET password = ? WHERE id = ?");
      $stmt->execute([$pass, $userID]);
      $result = "Your password has been reset";
    }
  } else { // username not found
    $result = "<span class='error'>ERROR -- username not found</span>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <!--meta data goes here-->
    <?php $PAGE_TITLE = "Password Recovery";
    include 'includes/head.php';?>
  </head>
  <body>
    <div id="container">
      <!--HEADER-->
      <?php include 'includes/header.php'; ?>
      <section>
        <!--page content goes here-->
        <main>
          <h2>Password Recovery</h2>

          <form id="getSecurityQ" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
            <div>
              <label for="user">Username: </label>
              <input type="text" name="user" />
              <button type="submit" name="getQ">Go</button>
            </div>
          </form>

          <form id="resetPass" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" style="<?php echo $formDisplay;?>">
            <div>
              <p><strong>Security Question: </strong><?php echo $question;?></p>
            </div>
            <div>
              <label for="answer">Answer: </label>
              <input type="text" name="answer" />
            </div>
            <div>
              <label for="pass">New Password: </label>
              <input type="password" name="pass" />
            </div>
            <div>
              <label for="confirmPass">Confirm Password: </label>
              <input type="password" name="confirmPass" />
            </div>
            <div>
              <input type="hidden" name="userID" value="<?php echo $userID;?>" />
              <button type="submit" name="submit">Reset Password</button>
            </div>
          </form>

          <p><?php echo $result;?></p>

        </main>
      </section> <!--end main content area-->
    </div> <!--end container-->
    <!--scripts go here-->
    <?php include 'includes/jsincludes.php';?>
  </body>
</html>

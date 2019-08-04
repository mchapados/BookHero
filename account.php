<!--
  ____________________________________________________________
   FILE: account.php
   DESCRIPTION: Book Hero edit account page
   Allows users to change their account info and/or password
  ____________________________________________________________

-->
<?php
session_start();
// redirect if not logged in
if (!isset($_SESSION['id'])) {
  header('Location: index.php');
  exit(); }

include 'includes/library.php';
$pdo = connectdb(); // connect to database
$stmt = $pdo->prepare("SELECT username, email FROM project_users WHERE id = ?");
$stmt->execute([$_SESSION['id']]);

if ($stmt->rowCount() > 0) {
  $row = $stmt->fetch();
  $username = $row['username'];
  $email = $row['email'];
}

// FORM PROCESSING
if (isset($_POST['update'])) {
  // get form data
  if (strlen($_POST['email']) > 0) {
    $email = strip_tags($_POST['email']);
    // update email
    $stmt = $pdo->prepare("UPDATE project_users SET email = ? WHERE id = ?");
    $stmt->execute([$email, $_SESSION['id']]);
  }
  if ($_POST['securityQuestion'] == "custom")
    $question = strip_tags($_POST['customQuestion']);
  else
    $question = $_POST['securityQuestion'];
  // hash security answer and password if set
  if (strlen($_POST['answer']) > 0) {
    $answer = password_hash(strtoupper($_POST['answer']), PASSWORD_DEFAULT);
    // update security question and answer
    $stmt = $pdo->prepare("UPDATE project_users SET securityQuestion = ?, securityAnswer = ? WHERE id = ?");
    $stmt->execute([$question, $answer, $_SESSION['id']]);
  }
  if (strlen($_POST['pass']) > 0) {
    $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);
    // update password
    $stmt = $pdo->prepare("UPDATE project_users SET password = ? WHERE id = ?");
    $stmt->execute([$pass, $_SESSION['id']]);
  }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <!--meta data goes here-->
    <?php $PAGE_TITLE = "Register";
    include 'includes/head.php';?>
  </head>
  <body>
    <div id="container">
      <!--HEADER-->
      <?php include 'includes/header.php';?>
      <section>
        <!--SIDE NAVIGATION-->
        <?php include 'includes/nav.php';?>
        <!--page content goes here-->
        <main>
          <h2>Edit Account Info</h2>

          <form id="editAccount" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
             <div>
               <label for="email">Email Address: </label>
               <input type="email" name="email" value="<?php echo $email;?>" />
             </div>
             <div>
               <label for="pass">New Password: </label>
               <input type="password" name="pass" value="" />
             </div>
             <div>
               <label for="confirmPass">Confirm Password: </label>
               <input type="password" name="confirmPass" value="" />
             </div>
             <div>
               <label for="securityQuestion">Security Question: </label>
               <select name="securityQuestion">
                 <option value="What street did you grow up on?">What street did you grow up on?</option>
                 <option value="In which city were your parents married?">In which city were your parents married?</option>
                 <option value="What was your first pet's name?">What was your first pet's name?</option>
                 <option value="custom">Custom Question</option>
               </select>
             </div>
             <div>
               <label for="answer">Answer: </label>
               <input type="text" name="answer" value="" />
             </div>
             <div>
               <button type="submit" name="update">Update Info</button>
             </div>
             <div>
               <a href="deleteaccount.php">
                 <button id="deleteAccount" type="button">Delete Account</button>
               </a>
             </div>
          </form>

        </main>
      </section> <!--end main content area-->
    </div> <!--end container-->
    <!--scripts go here-->
    <?php include 'includes/jsincludes.php';?>
    <script src="js/register.js"></script>
  </body>
</html>

<!--
  ____________________________________________________________
   FILE: register.php
   DESCRIPTION: Book Hero register page
   Allows new user to create an account
  ____________________________________________________________

-->
<?php
session_start();
// redirect if user is already logged in
if (isset($_SESSION['id'])) {
  header('Location: books.php');
  exit();
}
$user = "";
$email = "";
$error = false;

// FORM PROCESSING
if (isset($_POST['register'])) {
  include 'includes/library.php';
  $pdo = connectdb(); // connect to database

  // get form data
  $user = strip_tags($_POST['user']);
  $email = strip_tags($_POST['email']);
  if ($_POST['securityQuestion'] == "custom")
    $question = strip_tags($_POST['customQuestion']);
  else
    $question = $_POST['securityQuestion'];

  // hash security answer and password
  $answer = password_hash(strtoupper($_POST['answer']), PASSWORD_DEFAULT);
  $pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);
  $today = getdate(); // set date that terms were accepted
  $terms = $today['year']."-".$today['mon']."-".$today['mday'];

  // check that username is unique
  $stmt = $pdo->prepare("SELECT username FROM project_users WHERE username = ?");
  $stmt->execute([$user]);
  if ($stmt->rowCount() > 0) // username is taken
    $error = "ERROR -- username must be unique";

  if (!$error) {
    // add to database
    $stmt = $pdo->prepare("INSERT INTO project_users (username, password, email,".
            "terms, securityQuestion, securityAnswer) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user, $pass, $email, $terms, $question, $answer]);
    // log user in and redirect to library
    $stmt = $pdo->prepare("SELECT id FROM project_users WHERE username = ?");
    $stmt->execute([$user]);
    $row = $stmt->fetch();
    $_SESSION['id'] = $row['id']; // store user id
    header('Location: books.php'); // redirect to library page
    exit();
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
        <!--page content goes here-->
        <main>
          <h2>Register</h2>
          <!--REGISTRATION FORM-->
          <form id="regF" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
             <div>
               <label for="user">Username: </label>
               <input type="text" name="user" value="<?php echo $user;?>" />
             </div>
             <div>
               <label for="email">Email Address: </label>
               <input type="email" name="email" value="<?php echo $email;?>" />
             </div>
             <div>
               <label for="pass">Password: </label>
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
               <input type="checkbox" name="terms" />
               <label for="terms">I have read and accepted the <a id="showTerms" href="#">terms and conditions</a>.</label>
             </div>
             <div>
               <button type="submit" name="register">Register</button>
             </div>
          </form>
          <!--error message-->
          <?php if ($error) { echo "<span class='error'>".$error."</span>"; } ?>
        </main>
      </section> <!--end main content area-->
    </div> <!--end container-->
    <!--scripts go here-->
    <?php include 'includes/jsincludes.php';?>
    <script src="js/register.js"></script>
  </body>
</html>

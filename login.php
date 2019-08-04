<!--
  ____________________________________________________________
   FILE: login.php
   DESCRIPTION: Book Hero login page
   Allows existing user to login to their account
  ____________________________________________________________

-->
<?php
session_start();
// redirect if user is already logged in
if (isset($_SESSION['id'])) {
  header('Location: books.php');
  exit();
}

// check for cookie
if (isset($_COOKIE['chipsAhoy']))
  $user = $_COOKIE['chipsAhoy'];
else
  $user = "";

$error = false;

// LOGIN FORM PROCESSING
if (isset($_POST['login'])) {
  include 'includes/library.php';
  $pdo = connectdb(); // connect to database
  // search database for username
  $user = strip_tags($_POST['user']);
  $pass = $_POST['password'];
  $stmt = $pdo->prepare("SELECT id, password FROM project_users WHERE username = ?");
  $stmt->execute([$user]);
  if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch();
    // check if password matches
    if (password_verify($pass, $row['password'])) {
      $_SESSION['id'] = $row['id']; // store user id
      // if remember me is checked, set cookie
      if (isset($_POST['remember']))
        setcookie("chipsAhoy", $user ,time()+60*60*24*30); // expires in 30 days
      header('Location: books.php'); // redirect to library page
      exit();
    }
    else {
      $error = true; // password does not match
    }
  }
  else {
    $error = true; // user not found
  }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <!--meta data goes here-->
    <?php $PAGE_TITLE = "Login";
    include 'includes/head.php';?>
  </head>
  <body>
    <div id="container">
      <!--HEADER-->
      <?php include 'includes/header.php'; ?>
      <section>
        <!--page content goes here-->
        <main>
          <h2>Login</h2>
          <!--LOGIN FORM-->
          <form id="loginF" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
             <div>
               <label for="user">Username: </label>
               <input type="text" name="user" value="<?php echo $user;?>" />
             </div>
             <div>
               <label for="pass">Password: </label>
               <input type="password" name="password" value="" />
             </div>
             <div>
               <label for="remember">Remember Me</label>
               <input type="checkbox" name="remember" />
             </div>
             <div>
               <button type="submit" name="login">Login</button>
             </div>
          </form>
          <?php if ($error): ?>
            <span class="error">Login failed -- try again or <a href="register.php">register</a></span>
          <?php endif;?>
          <p><a href="recoverpassword.php">Forgot password</a></p>
        </main>
      </section> <!--end main content area-->
    </div> <!--end container-->
    <!--scripts go here-->
    <?php include 'includes/jsincludes.php';?>
  </body>
</html>

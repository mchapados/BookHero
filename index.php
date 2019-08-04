<!--
  ____________________________________________________________
   FILE: index.php
   DESCRIPTION: Book Hero home page
  ____________________________________________________________

-->
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <!--meta data goes here-->
    <?php $PAGE_TITLE = "Home";
    include 'includes/head.php';?>
  </head>
  <body>
    <div id="container">
      <!--HEADER-->
      <?php include 'includes/header.php'; ?>
      <section>
        <!--page content goes here-->
        <main>
          <h2>Welcome to Book Hero, your virtual library and eBook management system!</h2>
        </main>
      </section> <!--end main content area-->
    </div><!--end container-->
  </body>
</html>

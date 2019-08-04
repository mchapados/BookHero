<div id="hNav" class="nav">
  <?php
    if(isset($_SESSION['id']))
      echo "<a href='logout.php'>Logout</a>";
    else
      echo "<a href='login.php'>Login</a><a href='register.php'>Register</a>";
   ?>
</div>
<header>
  <h1>Book Hero</h1>
  <h2>Saves your library!</h2>
</header>

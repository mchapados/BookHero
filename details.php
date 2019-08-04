<!--
  ____________________________________________________________
   FILE: details.php
   DESCRIPTION: Book Hero book details page
   Displays all database information about a particular book
   specified in $_GET['id']
  ____________________________________________________________

-->
<?php
session_start();
// redirect if not logged in
if (!isset($_SESSION['id'])) {
  header('Location: index.php');
  exit(); }

// check if book id is set
if (!isset($_GET['id']))
  $error = true;

if (isset($_GET['id'])) {
  $error = false;
  include 'includes/library.php';
  $pdo = connectdb(); // connect to database
  // get book data
  $stmt = $pdo->prepare("SELECT * FROM project_books WHERE id = ?");
  $stmt->execute([$_GET['id']]);
  if ($stmt->rowCount() > 0)
    $row = $stmt->fetch();
  else
    $error = true;
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <!--meta data goes here-->
    <?php $PAGE_TITLE = "Book Details";
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
          <h2>Book Details</h2>
          <?php if (!$error): ?>
            <div id="BookDetails">
              <div> <!--cover image-->
                <img width="120" alt="<?php echo $row['title'];?>" src="<?php
                  if ($row['cover'] == null)
                    echo "img/covers/nocover120.png";
                  else
                    echo "https://loki.trentu.ca/~schapadoswlodarczyk/".$row['cover'];?>" />
                    <!--eBook link-->
                <?php if ($row['file'] != null) {
                  $ebooklink = "https://loki.trentu.ca/~schapadoswlodarczyk/".$row['file'];
                  echo "<p><a href='".$ebooklink."'>Download eBook</a></p>";}?>
                  <p>
                    <a href="edit.php?id=<?php echo $row['id'];?>"><i class="fas fa-edit"></i></a>
                    <a class="deleteBook" href="delete.php?id=<?php echo $row['id'];?>"><i class="fas fa-trash"></i></a>
                  </p>
              </div>

              <div> <!--book details-->
                <p><strong>Title: </strong><?php echo $row['title'];?></p>
                <p><strong>Author: </strong><?php echo $row['author'];?></p>
                <p><strong>Publisher: </strong><?php echo $row['publisher'];?></p>
                <p><strong>Year: </strong><?php echo $row['year'];?></p>
                <p><strong>ISBN: </strong><?php echo $row['isbn'];?></p>
                <p>
                  <strong>Copies Owned: </strong>
                  <?php if ($row['digital'])
                      echo "eBook";
                    if ($row['digital'] && $row['paper'])
                      echo ", ";
                    if ($row['paper'])
                      echo "Paper";?>
                </p>
                <p>
                  <strong>Rating: </strong>
                  <?php // display star rating
                  for ($x = 0; $x < $row['rating']; $x++)
                    echo "<i class='fas fa-star'></i>";?>
                </p>
                <p><strong>Notes/Review: </strong></p>
                  <p><?php echo $row['notes'];?></p>
              </div>
            </div>

          <?php endif;
          if ($error): ?>
            <span class="error">ERROR -- book could not be found</span>
          <?php endif;?>

        </main>
      </section> <!--end main content area-->
    </div> <!--end container-->
<!--scripts go here-->
<?php include 'includes/jsincludes.php';?>
</body>
</html>

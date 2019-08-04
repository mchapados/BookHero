<!--
  ____________________________________________________________
   FILE: edit.php
   DESCRIPTION: Book Hero edit book page
   Allows a user to edit a book in their collection specified
   in $_GET['id'], then redirects to that book's details page
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

$error = false;
if (!isset($_GET['id']) && !isset($_POST['submit']))
  $error = true;

// pre-populate form data
if (isset($_GET['id']))
{
  $id = $_GET['id'];
  $stmt = $pdo->prepare("SELECT * FROM project_books WHERE id = ?");
  $stmt->execute([$id]);
  // check if user owns the book they are trying to edit
  if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch();
    if ($row['userID'] != $_SESSION['id'])
      $error = true;
    else {
      $title = $row['title'];
      $author = $row['author'];
      $pub = $row['publisher'];
      $year = $row['year'];
      $isbn = $row['isbn'];
      $rating = $row['rating'];
      $notes = $row['notes'];
      $cover = $row['cover'];
      if ($row['digital'])
        $digi = "checked";
      else
        $digi = "";
      if ($row['paper'])
        $paper = "checked";
      else
        $paper = "";
    }
  } else {
    $error = true;
  }
}

// FORM PROCESSING
if (isset($_POST['submitBook'])) {
  // get values
  $id = $_POST['id'];
  $title = strip_tags($_POST['title']);
  $author = strip_tags($_POST['author']);
  $pub = strip_tags($_POST['pub']);
  $year = strip_tags($_POST['year']);
  $isbn = strip_tags($_POST['isbn']);
  $digi = $_POST['digi'] ?? 0;
  $paper = $_POST['paper'] ?? 0;
  $rating = $_POST['rating'];
  $notes = strip_tags($_POST['notes']);

  // update database
  $stmt = $pdo->prepare("UPDATE project_books SET title = ?, author = ?, publisher = ?, year = ?, isbn = ?, digital = ?, paper = ?, rating = ?, notes = ? WHERE id = ?");
  $stmt->execute([$title, $author, $pub, $year, $isbn, $digi, $paper, $rating, $notes, $id]);

  // upload files
  if (is_uploaded_file($_FILES['coverImg']['tmp_name'])) {
  $newname = createFilename('coverImg', WEBROOT.'www_data/', 'cover', $id);
  checkAndMoveFile('coverImg', 1048576, $newname); // max size = 1 mb
  // add cover link to database
  $newname = str_replace(WEBROOT, '', $newname);
  $stmt = $pdo->prepare("UPDATE project_books SET cover = ? WHERE id = ?");
  $stmt->execute([$newname, $id]);
  }
  if (is_uploaded_file($_FILES['eBookFile']['tmp_name'])) {
  $newname = createFilename('eBookFile', WEBROOT.'www_data/', '', $id);
  checkAndMoveFileBook('eBookFile', 10485760, $newname); // max size = 10 mb
  // add ebook link to database
  $newname = str_replace(WEBROOT, '', $newname);
  $stmt = $pdo->prepare("UPDATE project_books SET file = ? WHERE id = ?");
  $stmt->execute([$newname, $id]);
  }
  // redirect to book details page
  header('Location: details.php?id='.$id);
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <!--meta data goes here-->
    <?php $PAGE_TITLE = "Edit Book";
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
          <h2>Edit Book</h2>
          <?php if (!$error): ?>
          <form id="editBook" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data">
            <div>
              <label for="title">Title: <strong>*</strong></label>
              <input type="text" name="title" value="<?php echo $title;?>" required />
            </div>
            <div>
              <label for="author">Author: <strong>*</strong></label>
              <input type="text" name="author" value="<?php echo $author;?>" required />
            </div>
            <div>
              <label for="pub">Publisher: </label>
              <input type="text" name="pub" value="<?php echo $pub;?>" />
            </div>
            <div>
              <label for="year">Year: </label>
              <input type="text" name="year" value="<?php echo $year;?>" />
            </div>
            <div>
              <label for="isbn">ISBN: </label>
              <input type="text" name="isbn" value="<?php echo $isbn;?>" />
            </div>
            <div>
              <label for="cover">Upload Cover Image: </label>
              <input type="file" name="coverImg" />
            </div>
            <div>
              <span class="small">jpeg, png, or gif only. Max File Size: 1 mb</span>
            </div>
            <div>
              <label>Copies owned:</label>
              <?php  echo "<input type='checkbox' value='1' name='digi' ".$digi." />";?> eBook
              <?php  echo "<input type='checkbox' value='1' name='paper' ".$paper." />";?> Paper
            </div>
            <div>
              <label for="eBook">Upload eBook: </label>
              <input type="file" name="eBookFile" />
            </div>
            <div>
              <span class="small">pdf, mobi, or epub only. Max File Size: 10 mb</span>
            </div>
            <div>
              <label for="rating">Rating: </label>
              <select name="rating">
                <option value="0">select</option>
                <?php
                for ($x = 1; $x <= 5; $x++)
                  if ($x == $rating)
                    echo "<option value='".$x."' selected>".$x."</option>";
                  else
                    echo "<option value='".$x."'>".$x."</option>";
                ?>
              </select>
            </div>
            <div class="textbox">
              <label for="notes">Notes/Review: </label>
              <textarea name="notes" rows="4" cols="36"><?php echo $notes;?></textarea>
            </div>
            <div>
              <input type="hidden" name="id" value="<?php echo $id;?>" />
              <button type="submit" name="submitBook">Submit Changes</button>
            </div>
          </form>
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

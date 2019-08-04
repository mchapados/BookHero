<!--
  ____________________________________________________________
   FILE: add.php
   DESCRIPTION: Book Hero 'add book' page
   Allows a user to add a book to their collection
  ____________________________________________________________

-->
<?php
session_start();
// redirect if not logged in
if (!isset($_SESSION['id'])) {
  header('Location: index.php');
  exit(); }

if (isset($_POST['submitBook'])) {
  include 'includes/library.php';
  $pdo = connectdb(); // connect to database
  // set input values
  $title = strip_tags($_POST['title']);
  $author = strip_tags($_POST['author']);
  $pub = strip_tags($_POST['pub']);
  $year = strip_tags($_POST['year']);
  $isbn = strip_tags($_POST['isbn']);
  $digi = $_POST['digi'] ?? 0;
  $paper = $_POST['paper'] ?? 0;
  $rating = $_POST['rating'];
  $notes = strip_tags($_POST['notes']);
  // add new book
  $stmt = $pdo->prepare("INSERT INTO project_books (userID, title, author, ".
    "publisher, year, isbn, digital, paper, rating, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->execute([$_SESSION['id'], $title, $author, $pub, $year, $isbn, $digi, $paper, $rating, $notes]);
  // get book id -- logic: most recent book added by user will come up first
  $stmt = $pdo->prepare("SELECT id FROM project_books WHERE userID = ? ORDER BY id DESC");
  $stmt->execute([$_SESSION['id']]);
  $row = $stmt->fetch();
  $bookID = $row['id'];
  // upload files
  if (is_uploaded_file($_FILES['coverImg']['tmp_name'])) {
  $newname = createFilename('coverImg', WEBROOT.'www_data/', 'cover', $bookID);
  checkAndMoveFile('coverImg', 1048576, $newname); // max size = 1 mb
  // add to database
  $newname = str_replace(WEBROOT, '', $newname);
  $stmt = $pdo->prepare("UPDATE project_books SET cover = ? WHERE id = ?");
  $stmt->execute([$newname, $bookID]);
  }
  if (is_uploaded_file($_FILES['eBookFile']['tmp_name'])) {
  $newname = createFilename('eBookFile', WEBROOT.'www_data/', '', $bookID);
  checkAndMoveFileBook('eBookFile', 10485760, $newname); // max size = 10 mb
  // add to database
  $newname = str_replace(WEBROOT, '', $newname);
  $stmt = $pdo->prepare("UPDATE project_books SET file = ? WHERE id = ?");
  $stmt->execute([$newname, $bookID]);
  }
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <!--meta data goes here-->
    <?php $PAGE_TITLE = "Add a Book";
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
          <h2>Add a Book</h2>
          <form id="addBook" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data">
            <div>
              <label for="title">Title: <strong>*</strong></label>
              <input type="text" name="title" value="" required />
            </div>
            <div>
              <label for="author">Author: <strong>*</strong></label>
              <input type="text" name="author" required />
            </div>
            <div>
              <label for="pub">Publisher: </label>
              <input type="text" name="pub" />
            </div>
            <div>
              <label for="year">Year: </label>
              <input type="text" name="year" />
            </div>
            <div>
              <label for="isbn">ISBN: </label>
              <input type="text" name="isbn" />
            </div>
            <div>
              <label for="cover">Upload Cover Image: </label>
              <input type="file" name="coverImg" />
            </div>
            <div>
              <span class="small">jpeg, png, or gif only. Max File Size: 1 mb</span>
            </div>
            <div>
              <label>Copies owned: </label>
              <input type="checkbox" name="digi" value="1" /> eBook
              <input type="checkbox" name="paper" value="1" /> Paper
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
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
              </select>
            </div>
            <div class="textbox">
              <label for="notes">Notes/Review: </label>
              <textarea name="notes" rows="4" cols="36"></textarea>
            </div>
            <div>
              <button type="submit" name="submitBook">Add Book</button>
            </div>
          </form>
        </main>
      </section> <!--end main content area-->
    </div> <!--end container-->
    <!--scripts go here-->
    <?php include 'includes/jsincludes.php';?>
  </body>
</html>

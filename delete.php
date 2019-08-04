<!--
  ____________________________________________________________
   FILE: delete.php (php script)
   DESCRIPTION: Removes a book from user's account specified
   in $_GET['id'] as well as associated files, then redirects
   to library page
  ____________________________________________________________

-->
<?php
session_start();
// redirect if not logged in
if (!isset($_SESSION['id'])) {
  header('Location: index.php');
  exit(); }

if (isset($_GET['id']))
{
  $id = $_GET['id'];
  include 'includes/library.php';
  $pdo = connectdb(); // connect to database
  // confirm that user owns the book they are trying to delete
  $stmt = $pdo->prepare("SELECT userID, cover, file FROM project_books WHERE id = ?");
  $stmt->execute([$id]);
  if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch();
    if ($row['userID'] == $_SESSION['id']) {
      // delete cover and ebook files
      if ($row['cover'] != null)
        deleteFile($row['cover']);
      if ($row['file'] != null)
        deleteFile($row['file']);
      // remove book from database
      $stmt = $pdo->prepare("DELETE FROM project_books WHERE id = ?");
      $stmt->execute([$id]);
      // redirect to library page
      header('Location: books.php');
      exit();
    }
  }
}
header('Location: books.php');
exit();
?>

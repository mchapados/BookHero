<!--
  ____________________________________________________________
   FILE: deleteaccount.php (php script)
   DESCRIPTION: Deletes a user's account and all books and
   files associated with it
  ____________________________________________________________

-->
<?php
session_start();
// redirect if not logged in
if (!isset($_SESSION['id'])) {
  header('Location: index.php');
  exit(); }

$user = $_SESSION['id'];
include 'includes/library.php';
$pdo = connectdb(); // connect to database

// find and delete all of user's books
// Remove cover and eBook files
$stmt = $pdo->prepare("SELECT id, cover, file FROM project_books WHERE userID = ?");
$stmt->execute([$user]);
foreach ($stmt as $row) {
  if ($row['cover'] != null)
    deleteFile($row['cover']);
  if ($row['file'] != null)
    deleteFile($row['file']);
}
// Remove book data
$stmt = $pdo->prepare("DELETE FROM project_books WHERE userID = ?");
$stmt->execute([$user]);

// Delete user account
$stmt = $pdo->prepare("DELETE FROM project_users WHERE id = ?");
$stmt->execute([$user]);

// redirect to logout page
header('Location: logout.php');
exit();
?>

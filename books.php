<!--
  ____________________________________________________________
   FILE: books.php
   DESCRIPTION: Book Hero main library page
   Displays a logged-in user's book collection
  ____________________________________________________________

-->
<?php
session_start();
// redirect if not logged in
if (!isset($_SESSION['id'])) {
  header('Location: index.php');
  exit(); }
if (!isset($_GET['page']))
  $page = 1;
else
  $page = $_GET['page'];
include 'includes/library.php';
$pdo = connectdb(); // connect to database
// get book data
$stmt = $pdo->prepare("SELECT id, title, author, rating, cover FROM project_books WHERE userID = ? ORDER BY author ASC");
$stmt->execute([$_SESSION['id']]);
$pageCount = 1;
// figure out number of pages (10 books per page)
for ($x = 10; $x < $stmt->rowCount(); $x+=10)
  $pageCount++;
$results = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <!--meta data goes here-->
    <?php $PAGE_TITLE = "Library";
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
          <h2>Library</h2>

          <?php if ($stmt->rowCount() == 0)
          echo "<p>There are no books in your collection</p>";?>
          <?php if ($stmt->rowCount() > 0): ?>
            <p>Displaying <strong><?php if ($stmt->rowCount() < 10)
                    echo "1 - ".$stmt->rowCount();
                  else if (count($results) < $page * 10)
                    echo (($page * 10) - 9)." - ".count($results);
                  else
                    echo (($page * 10) - 9)." - ".($page * 10);?></strong> of <strong>
              <?php echo $stmt->rowCount();?></strong> books in your collection</p>

          <div class="pagination">
            <a href="books.php?page=1">&laquo;</a><?php for ($x = 1; $x <= $pageCount; $x++) {
                if ($x == $page)
                  echo "<a class='current' href='books.php?page=".$x."'>".$x."</a>";
                else
                  echo "<a href='books.php?page=".$x."'>".$x."</a>";
              }?><a href="books.php?page=<?php echo $pageCount;?>">&raquo;</a>
          </div>

          <table>
            <tr> <!--header row-->
              <th>Cover</th>
              <th>Title</th>
              <th>Author</th>
              <th>Rating</th>
              <th>&nbsp;</th> <!--edit-->
              <th>&nbsp;</th> <!--delete-->
            </tr>
            <!--display each book in user's library, sorted by author-->
            <?php for ($i = (($page * 10) - 10); ($i < ($page * 10) && $i < count($results)); $i++): ?>
              <tr>
                <td>
                  <!--cover image-->
                  <img width="80" alt="<?php echo $results[$i]['title'];?>" src="<?php
                    if ($results[$i]['cover'] == null)
                      echo "img/covers/nocover.png";
                    else
                      echo "https://loki.trentu.ca/~schapadoswlodarczyk/".$results[$i]['cover'];?>" />
                </td>
                <td><a href="details.php?id=<?php echo $results[$i]['id'];?>"><?php echo $results[$i]['title'];?></a></td>
                <td><?php echo $results[$i]['author'];?></td>
                <td>
                  <?php // display star rating
                  for ($x = 0; $x < $results[$i]['rating']; $x++)
                    echo "<i class='fas fa-star'></i>";?>
                </td>
                <td><a href="edit.php?id=<?php echo $results[$i]['id'];?>"><i class="fas fa-edit"></i></a></td>
                <td><a class="deleteBook" href="delete.php?id=<?php echo $results[$i]['id'];?>"><i class="fas fa-trash"></i></a></td>
              </tr>
            <?php endfor;?>
            </table>

            <div class="pagination">
              <a href="books.php?page=1">&laquo;</a><?php for ($x = 1; $x <= $pageCount; $x++) {
                  if ($x == $page)
                    echo "<a class='current' href='books.php?page=".$x."'>".$x."</a>";
                  else
                    echo "<a href='books.php?page=".$x."'>".$x."</a>";
                }?><a href="books.php?page=<?php echo $pageCount;?>">&raquo;</a>
            </div>
            <?php endif;?>

        </main>
      </section> <!--end main content area-->
    </div> <!--end container-->
    <!--scripts go here-->
    <?php include 'includes/jsincludes.php';?>
  </body>
</html>

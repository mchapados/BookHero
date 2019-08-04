<!--
  ____________________________________________________________
   FILE: search.php
   DESCRIPTION: Book Hero search page
   Allows a user to search their collection by title,
   author, or publisher
  ____________________________________________________________

-->
<?php
session_start();
// redirect if not logged in
if (!isset($_SESSION['id'])) {
  header('Location: index.php');
  exit(); }

// FORM PROCESSING
if (isset($_POST['submit'])) {
  include 'includes/library.php';
  $pdo = connectdb(); // connect to database
  // get form data
  $searchTerm = strip_tags($_POST['searchTerm']);
  $searchField = $_POST['searchField'];
  $sortBy = $_POST['sortBy'];
  $sortOrder = $_POST['sortOrder'];
  // create search query
  $searchQuery = "SELECT id, title, author, rating, cover FROM project_books WHERE userID = ".$_SESSION['id']." AND ".$searchField." LIKE '%".$searchTerm."%' ORDER BY ".$sortBy." ".$sortOrder;
  // search database
  $stmt = $pdo->prepare($searchQuery);
  $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <!--meta data goes here-->
    <?php $PAGE_TITLE = "Search";
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
          <h2>Search</h2>
          <!--Search Form-->
          <form id="search" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
            <div>
              <label for"searchTerm">Search for: </label>
              <input type="text" name="searchTerm" />
              <label for="searchField"> in: </label>
              <select name="searchField">
                <option value="title">Title</option>
                <option value="author">Author</option>
                <option value="publisher">Publisher</option>
              </select>
              <button type="submit" name="submit">Search</button>
            </div>

            <div>
              <label for="sortBy">Sort by: </label>
              <select name="sortBy">
                <option value="author">Author</option>
                <option value="title">Title</option>
                <option value="publisher">Publisher</option>
                <option value="rating">Rating</option>
                <option value="year">Year</option>
              </select>
              <select name="sortOrder">
                <option value="ASC">ASC</option>
                <option value="DESC">DESC</option>
              </select>
            </div>
          </form>

          <!--Display Results-->
          <?php if (isset($_POST['submit']) && $stmt->rowCount() > 0):?>
            <p><strong><?php echo $stmt->rowCount();?></strong> results found</p>
            <table>
              <tr> <!--header row-->
                <th>Cover</th>
                <th>Title</th>
                <th>Author</th>
                <th>Rating</th>
                <th>&nbsp;</th> <!--edit-->
                <th>&nbsp;</th> <!--delete-->
              </tr>
            <?php foreach ($stmt as $row): ?>
              <tr>
                <td>
                  <!--cover image-->
                  <img width="80" alt="<?php echo $row['title'];?>" src="<?php
                    if ($row['cover'] == null)
                      echo "img/covers/nocover.png";
                    else
                      echo "https://loki.trentu.ca/~schapadoswlodarczyk/".$row['cover'];?>" />
                </td>
                <td><a href="details.php?id=<?php echo $row['id'];?>"><?php echo $row['title'];?></a></td>
                <td><?php echo $row['author'];?></td>
                <td>
                  <?php // display star rating
                  for ($x = 0; $x < $row['rating']; $x++)
                    echo "<i class='fas fa-star'></i>";?>
                </td>
                <td><a href="edit.php?id=<?php echo $row['id'];?>"><i class="fas fa-edit"></i></a></td>
                <td><a class="deleteBook" href="delete.php?id=<?php echo $row['id'];?>"><i class="fas fa-trash"></i></a></td>
              </tr>
          <?php endforeach;?>
          </table>
        <?php endif;
        if (isset($_POST['submit']) && $stmt->rowCount() == 0)
          echo "<p>No results found</p>";?>
        </main>
      </section> <!--end main content area-->
    </div> <!--end container-->
    <!--scripts go here-->
    <?php include 'includes/jsincludes.php';?>
  </body>
</html>

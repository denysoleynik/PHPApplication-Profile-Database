<?php
session_start();
require_once "pdo.php";
require_once "bootstrap.php";
require_once "util.php";
flashMessages();


if ( !isset ($_SESSION['name'])) {
  echo ("<a href=login.php>Please log in</a>");
}
// If the user requested logout go back to index.php
if ( isset($_POST['logout']) ) {
    header('Location: logout.php');
    return;
}

if ( isset($_POST['add']) ) {
    header('Location: add.php');
    return;
}
?>




<!DOCTYPE html>
<html>
<head>
<title>Denis Oleynik's Index Page a419a784</title>
</head>
<body>

  <?php
  if ( isset($_SESSION['name'])) {
      echo "<p>Welcome: ";
      echo htmlentities($_SESSION['name']);
      echo "</p>\n";
  }
  ?>

<div class="container">

<?php
if (isset ($_SESSION['user_id'])) {
echo ("<h1>Profiles</h1>");
echo ("<table border='1'>");
$stmt = $pdo->prepare("SELECT profile_id, user_id, first_name, last_name, email, headline, summary FROM Profile where user_id = :id");
$stmt->execute(array(':id' => $_SESSION['user_id']));
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ( $rows !== false ) {
echo "<tr><td>";
echo ("Name");
echo("</td><td>");
echo ("Headline");
echo("</td><td>");
echo ("View");
//if (isset ($_SESSION['user_id'])) {
  echo("</td><td>");
  echo ("Action");
//}
echo("</td></tr>\n");

foreach ( $rows as $row ) {
    echo "<tr><td>";
    echo($row['first_name']." ".$row['last_name']);
    echo("</td><td>");
    echo($row['headline']);
    echo("</td><td>");
    echo('<a href="view.php?profile_id='. $row['profile_id'].'">View</a>');
       //if (isset ($_SESSION['user_id'])) {
       echo("</td><td>");
       echo('<a href="edit.php?profile_id='. $row['profile_id'].'">Edit</a> / ');
       //echo('<input type="submit" name="delete" value="Delete" onclick="delete.php?profile_id='.$row['profile_id'].'"> / ');
       echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
      // }
    echo("</td></tr>\n");

}
}
}

?>
</table>
<div>
<?php
if (isset ($_SESSION['user_id'])) {
echo ('<a href="add.php?user_id='.$_SESSION['user_id'].'">Add New Entry</a>');
echo ("<p>");
echo ('<a href="logout.php">Logout</a>');
echo ("</p>");
}
?>

</div>
</div>
</form>
</body>
</html>

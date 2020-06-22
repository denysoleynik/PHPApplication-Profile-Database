<?php
session_start();
require_once "pdo.php";
require_once "bootstrap.php";
require_once "util.php";


if ( !isset ($_SESSION['name']) || !isset ($_SESSION['user_id']) || !isset ($_REQUEST['profile_id'])) {
  echo ("<a href=login.php>Please log in</a>");
}
// If the user requested logout go back to index.php
if ( isset($_POST['back']) ) {
    header('Location: index.php');
    return;
}

?>

<!DOCTYPE html>
<html>
<head>
<?php require_once "bootstrap.php"; ?>
<title>Profile View Page a419a784</title>
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
<h1>Profile</h1>
<table border="1">
<?php
$stmt = $pdo->prepare("SELECT profile_id, user_id, first_name, last_name, email, headline, summary FROM Profile WHERE user_id = :id AND profile_id = :pi");
$stmt->execute(array(':pi' => $_GET['profile_id'],
':id' => $_SESSION['user_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<tr><td>";
echo ("First Name");
echo("</td><td>");
echo ("Last Name");
echo("</td><td>");
echo ("Email");
echo("</td><td>");
echo ("Headline");
echo("</td><td>");
echo ("Summary");
echo("</td><td>");
echo ("Action");
echo("</td></tr>\n");

if ( $row !== false ) {
    echo("<tr><td>");
    echo($row['first_name']);
    echo("</td><td>");
    echo($row['last_name']);
    echo("</td><td>");
    echo($row['email']);
    echo("</td><td>");
    echo($row['headline']);
    echo("</td><td>");
    echo($row['summary']);
    echo("</td><td>");
    echo('<a href="edit.php?profile_id='. $row['profile_id'].'">Edit</a> / ');
    echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
    echo("</td></tr>\n");
}

?>
</table>
<?php
$stmt2 = $pdo->prepare("SELECT profile_id, rank, year, description FROM Position WHERE profile_id = :id2");
$stmt2->execute(array(':id2' => $_REQUEST['profile_id']));
echo ("<p> Positions:</p>");
echo("<ul>");
while ($rows = $stmt2->fetch(PDO::FETCH_ASSOC)) {
echo ("<li> Year: ".$rows['year']." / Position : ".$rows['description']."</li>");
}
echo("</ul>");

// Educations view
$educations = loadEdu( $pdo, $_REQUEST['profile_id']);
echo ("<p> Education:</p>");
echo("<ul>");
foreach ($educations as $education) {
echo ("<li> Year: ".$education['year']." / Institution : ".$education['name']."</li>");
}
echo("</ul>");

?>

<div>
<form method="post">
<input type="submit" name="back" value="Back">
</form>
</div>
</div>
</form>
</body>
</html>

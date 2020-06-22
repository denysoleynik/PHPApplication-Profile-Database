<?php
session_start();
require_once "pdo.php";
require_once "util.php";
require_once "bootstrap.php";

flashMessages();


if ( ! isset($_SESSION['name']) ) {
  die('Not logged in<p>
  <a href="login.php">Please Log In</a>
  </p>');
}

// If the user requested logout go back to index.php
if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}


// We are deleting auto
if (isset ($_POST['profile_id']) && $_POST['delete']) {
  $sql ="DELETE FROM Profile WHERE profile_id = :xyz";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(':xyz' => $_POST['profile_id']));
  $_SESSION['success'] = "RECORD DELETED";
  header("Location: index.php");
  return;
}

//show the selected Profile to delete
$stmt2 = $pdo->prepare("SELECT profile_id, user_id, first_name, last_name, email, headline, summary FROM Profile WHERE profile_id = :xyz");
$stmt2->execute(array(":xyz" => $_REQUEST['profile_id']));
$row = $stmt2->fetch(PDO::FETCH_ASSOC);
if ($row === false) {
  $_SESSION['error'] = "BAD VALUE FOR Profile ID";
  header("Location: index.php");
  return;
}
?>




<!DOCTYPE html>
<html>
<head>
<title>Profile Delete 58770e07</title>
</head>

<body>

<div class="container">
<h1>Do you want to DELETE <?=htmlentities($row['last_name'])?> ? </h1>
<form method="post" action="delete.php">
<p> First Name: <?=htmlentities($row['last_name'])?> </p>
<input type="hidden" name="profile_id" value="<?=$row['profile_id']?>">
<input type="submit" name="delete" value="Delete">
<input type="submit" name="cancel" value="Cancel">
<p></p>
</form>

<!-- onclick="return confirm('Are you sure?')" -->
</div>


</body>
</html>

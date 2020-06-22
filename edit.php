<?php
session_start();
require_once "pdo.php";
require_once "util.php";
require_once "bootstrap.php";
flashMessages();


if ( ! isset($_SESSION['name']) && ! isset($_SESSION['user_id']) ) {
  die('ACCESS DENIED<p>
  <a href="login.php">Please Log In</a>
  </p>');
}

// If the user requested logout go back to index.php
if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}

// Updating
if (isset ($_POST['first_name']) && isset ($_POST['last_name']) && isset ($_POST['email']) && isset ($_POST['headline']) && isset ($_POST['summary'])) {
       $msg = validateProfile();
       $msg2 = validatePos();
       $msg3 = validateEdu();

       if (is_string($msg) ) {
         $_SESSION['error'] = $msg;
         header("Location: edit.php?profile_id=" . $_POST["profile_id"]);
         return;
       }
       elseif (is_string($msg2) ) {
         $_SESSION['error'] = $msg2;
         header("Location: edit.php?profile_id=" . $_POST["profile_id"]);
         return;
       }
       elseif (is_string($msg3) ) {
         $_SESSION['error'] = $msg3;
         header("Location: edit.php?profile_id=" . $_POST["profile_id"]);
         return;
       }

// Validation done - Updating data

  else {
      //Update Data - Profile
      updateProfile ($pdo, $_POST['profile_id']);
      //Update the Positions Data
      updatePosition($pdo,$_POST['profile_id']);
      //Insert the Education Data
      updateEducation($pdo, $_POST['profile_id']);
      header("Location: index.php");
      return;
     }
}


//DATA to show

$stmt2 = $pdo->prepare("SELECT profile_id, user_id, first_name, last_name, email, headline, summary FROM Profile WHERE profile_id = :xyz AND user_id=:id");
$stmt2->execute(array(":xyz" => $_GET['profile_id'], ":id" => $_SESSION['user_id']));
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
<title>Denis 's Profiles a419a784</title>
</head>

<body>

<div class="container">
<h1>Profile Update : <?=htmlentities($row['first_name']." ".$row['last_name'])?> </h1>
<form method="post">
<input type="hidden" name="profile_id" value="<?=$row['profile_id']?>">
<p>First Name</p>
<input type="text" name="first_name" value="<?=$row['first_name']?>">
<p>Last Name</p>
<input type="text" name="last_name" value="<?=$row['last_name']?>">
<p>Email</p>
<input type="text" name="email" value="<?=$row['email']?>">
<p>Headline</p>
<input type="text" name="headline" value="<?=$row['headline']?>">
<p>Summary</p>
<input type="text" name="summary" value="<?=$row['summary']?>">

<p>Positions: <input type="submit" id="addPos" value="+">
<div id="position_fields">

<?php
$positions = loadPos($pdo, htmlentities($_REQUEST['profile_id']));
/*
$stmt3 = $pdo->prepare('SELECT * FROM Position WHERE profile_id = :prof ORDER BY rank');
$stmt3->execute(array(":prof" => $_REQUEST['profile_id']));
$positions = array();
while ($row = $stmt3->fetch(PDO::FETCH_ASSOC) ) {
$positions[] = $row;
}
*/
$pos = 0;
foreach ($positions as $position) {
  $pos++;
  echo ('<div id="position'.$pos.'">'."\n");
  echo('<p>Year: <input type="text" name="year'.$pos.'"');
  echo ('value="'.$position['year'].'"/>'."\n");
  echo ('<input type="button" value="-"');
  echo ('onclick="$(\'#position'.$pos.'\').remove();return false;">'."\n");
  echo ("</p>\n");
  echo ('<textarea name="desc'.$pos.'" rows="8" cols="80">'."\n");
  echo (htmlentities($position['description'])."\n");
  echo ("\n</textarea>\n</div>\n");
}
 ?>
</div></p>

<p>Education: <input type="submit" id="addEdu" value="+">
<div id="education_fields">

<?php
$educations = loadEdu($pdo, htmlentities($_REQUEST['profile_id']));
$posedu = 0;
foreach ($educations as $education) {
  $posedu++;
  echo ('<div id="education'.$posedu.'">'."\n");
  echo('<p>Year: <input type="text" name="edu_year'.$posedu.'"');
  echo ('value="'.$education['year'].'"/>'."\n");
  echo ('<input type="button" value="-"');
  echo ('onclick="$(\'#education'.$posedu.'\').remove();return false;">'."\n");
  echo ("</p>\n");
  echo ('<p>School: <input type="text" name="edu_school'.$posedu.'" value="'.$education['name'].'" class="school" />'."\n");
  echo ("</p>\n");
  echo ("</div>\n");
}
 ?>
</div></p>


<input type="submit" name="save" value="Save">
</form>

<p></p>
<form method="post">
<input type="submit" name="cancel" value="Cancel">
</form>
</p>
</div>

<script>

countPos = <?= $pos ?>;
countEdu = <?= $posedu?>;
// http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
$(document).ready(function(){
    window.console && console.log('Document ready called');
    $('#addPos').click(function(event){
        // http://api.jquery.com/event.preventdefault/
        event.preventDefault();
        if ( countPos >= 9 ) {
            alert("Maximum of nine position entries exceeded");
            return;
        }
        countPos++;
        window.console && console.log("Adding position "+countPos);
        $('#position_fields').append(
            '<div id="position'+countPos+'"> \
            <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#position'+countPos+'\').remove();return false;"> </p> \
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
    });

    //clicl event on +
    $('#addEdu').click(function(event){
        // http://api.jquery.com/event.preventdefault/
        event.preventDefault();
        if ( countEdu >= 9 ) {
            alert("Maximum of nine position entries exceeded");
            return;
        }
        countEdu++;
        window.console && console.log("Adding education "+countEdu);
        $('#education_fields').append(
            '<div id="education'+countEdu+'"> \
            <p>Year: <input type="text" name="edu_year'+countEdu+'" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#education'+countEdu+'\').remove();return false;"> </p> \
            <p>School: <input type="text" name="edu_school'+countEdu+'" value="" class="school"/> \
            </p> </div>');

        $('.school').autocomplete({source: "school.php" });
    });
        $('.school').autocomplete({source: "school.php" });
});

</script>

</body>
</html>

<?php
session_start();
require_once "pdo.php";
require_once "util.php";
require_once "bootstrap.php";
header ("Content-Type: text/html; charset=UTF-8");

flashMessages();


if ( ! isset($_SESSION['name']) || ! isset($_SESSION['user_id'])) {
  die('Not logged in<p>
  <a href="login.php">Please Log In</a>
  </p>');
}

// If the user requested logout go back to index.php
if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}

// Validate Data
if (isset ($_POST['first_name']) && isset ($_POST['last_name']) && isset ($_POST['email']) && isset ($_POST['headline']) && isset ($_POST['summary'])) {
$msg = validateProfile();
     if (is_string($msg) ) {
       $_SESSION['error'] = $msg;
       header("Location: add.php");
       return;
       }
$msgPos = validatePos();
     if (is_string($msgPos) ) {
       $_SESSION['error'] = $msgPos;
       header("Location: add.php");
       return;
       }
$msgEdu = validateEdu();
     if (is_string($msgEdu) ) {
       $_SESSION['error'] = $msgEdu;
       header("Location: add.php");
       return;
       }


//Insert Data - Profile
insertProfile($pdo);
$profile_id = $pdo->lastInsertId();
//Insert the Positions Data
insertPosition($pdo,$profile_id);
//Insert the Education Data
insertEducation($pdo,$profile_id);
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
<h1>ADD A PROFILE</h1>

Welcome

<p>Add A New PROFILE</p>
<form method="post" id="addProfile">
<p>First Name</p>
<input type="text" name="first_name" id="id1">
<p>Last Name</p>
<input type="text" name="last_name" id="id2">
<p>Email</p>
<input type="text" name="email" id="id3">
<p>Headline</p>
<input type="text" name="headline" id="id4">
<p>Summary</p>
<input type="text" name="summary" id="id5">
<p></p>
<p>
Position: <input type="submit" id="addPos" value="+">
<div id="position_fields">
</div>
</p>
<p></p>
<p>
Education: <input type="submit" id="addEdu" value="+">
<div id="education_fields">
</div>
</p>
<p></p>

</p>
<p></p>
<input type="submit" name="add" value="Add Profile">
</form>

<script>

countPos = 0;
countEdu = 0;
// http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
$(document).ready(function(){
    window.console && console.log('Document ready called');

    //Click on add position
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
                onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
    });
    // Click on add education
    $('#addEdu').click(function(event){
        // http://api.jquery.com/event.preventdefault/
        event.preventDefault();
        if ( countEdu >= 9 ) {
            alert("Maximum of nine educations entries exceeded");
            return;
        }
        countEdu++;
        window.console && console.log("Adding educations "+countEdu);
        $('#education_fields').append(
            '<div id="education'+countEdu+'"> \
            <p>Year: <input type="text" name="edu_year'+countEdu+'" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#education'+countEdu+'\').remove();return false;"></p> \
            <p>School: \
            <input type="text" size="80" id="edu_school" name="edu_school'+countEdu+'" value="" class="school"/> \
            </p> </div>');
          $('.school').autocomplete({source: "school.php" });
    });


});



</script>

<p></p>
<form method="post">
<input type="submit" name="cancel" value="Cancel">
</form>
</p>
</div>

</body>
</html>

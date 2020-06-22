<?php
require_once "pdo.php";
require_once "util.php";
require_once "bootstrap.php";

// line added to turn on color syntax highlight
function validateProfile() {
if (strlen($_POST['first_name']) == 0 || strlen($_POST['last_name']) == 0
    || strlen($_POST['email']) == 0 || strlen($_POST['headline']) == 0
    || strlen($_POST['summary']) == 0) {
    return "All values are required";
    }

if (strpos($_POST['email'], "@") === false) {
    return  "Email address must contain @";
    }
return true;
}

function validatePos() {
  for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['year'.$i]) ) continue;
    if ( ! isset($_POST['desc'.$i]) ) continue;

    $year = $_POST['year'.$i];
    $desc = $_POST['desc'.$i];

    if ( strlen($year) == 0 || strlen($desc) == 0 ) {
      return "All fields are required";
    }

    if ( ! is_numeric($year) ) {
      return "Position year must be numeric";
    }
  }
  return true;
}

function validateEdu() {
  for($i=1; $i<=9; $i++) {
    if ( ! isset($_POST['edu_year'.$i]) ) continue;
    if ( ! isset($_POST['edu_school'.$i]) ) continue;

    $year = htmlentities($_POST['edu_year'.$i]);
    $desc = htmlentities($_POST['edu_school'.$i]);

    if ( strlen($year) == 0 || strlen($desc) == 0 ) {
      return "All fields are required";
    }

    if ( ! is_numeric($year) ) {
      return "Education year must be numeric";
    }
  }
  return true;
}

function insertProfile($pdo) {
  $stmt = $pdo->prepare('INSERT INTO Profile
      (user_id, first_name, last_name, email, headline, summary)
      VALUES ( :uid, :fn, :ln, :em, :he, :su)');

  $stmt->execute(array(
      ':uid' => $_SESSION['user_id'],
      ':fn' => htmlentities($_POST['first_name']),
      ':ln' => htmlentities($_POST['last_name']),
      ':em' => htmlentities($_POST['email']),
      ':he' => htmlentities($_POST['headline']),
      ':su' => htmlentities($_POST['summary']))
    );
    $_SESSION['success'] = "Record added";
}


function insertPosition($pdo,$profile_id) {
$rank=1;
    for ($i=1;$i<=9; $i++) {
        if (! isset ($_POST['year'.$i]) ) continue;
        if (! isset ($_POST['desc'.$i]) ) continue;
    $year = htmlentities($_POST['year'.$i]);
    $desc = htmlentities($_POST['desc'.$i]);
    $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description)
    VALUES (:pid, :rank, :year, :desc)');
    $stmt->execute(array(
    ':pid' => $profile_id,
    ':rank'=> $rank,
    ':year'=> $year,
    ':desc'=>$desc)
    );
    $rank++;
    $_SESSION['success'] = "Education added";
    }
}

function insertEducation($pdo,$profile_id) {
  $rank=1;
      for ($i=1;$i<=9; $i++) {
          if (! isset ($_POST['edu_year'.$i]) ) continue;
          if (! isset ($_POST['edu_school'.$i]) ) continue;
      $edu_year = htmlentities($_POST['edu_year'.$i]);
      $edu_school = htmlentities($_POST['edu_school'.$i]);

      // checking for unique institution
      $inst = $pdo->prepare('SELECT * FROM Institution Where name = :edu_school');
      $inst->execute(array(':edu_school' => $edu_school));
      $rowedu = $inst->fetch(PDO::FETCH_ASSOC);

      //if does not exist - insert and get id
      if ($rowedu === false) {
      $institution_id = insertInstitution($pdo,$edu_school);
      }

      //if exist - get id
      else {
      $institution_id =  $rowedu['institution_id'];
      }

      $stmt = $pdo->prepare('INSERT INTO Education (profile_id, institution_id, rank, year)
      VALUES (:pid, :inst, :rank, :year)');
      $stmt->execute(array(
      ':pid' => $profile_id,
      ':inst' => $institution_id,
      ':rank'=> $rank,
      ':year'=> $edu_year)
      );
      $rank++;
      $_SESSION['success'] = "Education added";
      }
  }

  function insertInstitution($pdo,$edu_school) {
    $stmt = $pdo->prepare('INSERT INTO Institution (name)
    VALUES (:name)');
    $stmt->execute(array(
    ':name' => $edu_school));
    $institution_id = $pdo->lastInsertId();
    return $institution_id;
  }


function updateProfile($pdo, $profile_id) {
  $sql ="UPDATE Profile SET first_name = :fn, last_name = :ln, email = :em, headline = :hd, summary = :sm WHERE profile_id = :pid";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(':fn' => $_POST['first_name'],
  ':ln' => htmlentities($_POST['last_name']),
  ':em' => htmlentities($_POST['email']),
  ':hd' => htmlentities($_POST['headline']),
  ':sm' => htmlentities($_POST['summary']),
  ':pid' => $profile_id
  ));
  $_SESSION['success'] = "RECORD UPDATED";
  }

function updatePosition($pdo, $profile_id) {
  //Clear the previous position data
  $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id =:pid');
  $stmt->execute (array(':pid'=> $profile_id));
  //Insert new data into PROFILE
  $rankpos=1;
         for ($i=1;$i<=9;$i++) {
            if (! isset ($_POST['year'.$i]) ) continue;
            if (! isset ($_POST['desc'.$i]) ) continue;
            $year = htmlentities($_POST['year'.$i]);
            $desc = htmlentities($_POST['desc'.$i]);
            $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description)
            VALUES (:pid, :rank, :year, :desc)');
            $stmt->execute (array(
              ':pid' => $profile_id,
              ':rank' => $rankpos,
              ':year' => $year,
              ':desc' => $desc)
            );
            $rankpos++;
         }
  $_SESSION['success'] = "Profile with Position UPDATED";
}



function updateEducation($pdo, $profile_id) {
// Delete Education update

$stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id=:pid');
$stmt->execute (array(':pid'=> $profile_id));

//Insert new data into Education
$rankedu=1;
       for ($i=1;$i<=9;$i++) {
          if (! isset ($_POST['edu_year'.$i]) ) continue;
          if (! isset ($_POST['edu_school'.$i]) ) continue;
          $edu_year = htmlentities($_POST['edu_year'.$i]);
          $edu_school = htmlentities($_POST['edu_school'.$i]);

          // checking for unique institution
          $inst = $pdo->prepare('SELECT * FROM Institution Where name = :edu_school');
          $inst->execute(array(':edu_school' => $edu_school));
          $rowedu = $inst->fetch(PDO::FETCH_ASSOC);

          //if does not exist - insert and get id
          if ($rowedu === false) {
          $institution_id = insertInstitution($pdo,$edu_school);
          }

          //if exist - get id
          else {
          $institution_id =  $rowedu['institution_id'];
          }

          $stmt = $pdo->prepare('INSERT INTO Education (profile_id, institution_id, rank, year)
          VALUES (:pid, :inst_id, :rank, :year)');
          $stmt->execute (array(
            ':pid' => $profile_id,
            ':rank' => $rankedu,
            ':year' => $edu_year,
            ':inst_id' => $institution_id )
          );
          $rankedu++;
       }

$_SESSION['success'] = "Full profile UPDATED";
}




function flashMessages() {
  if (isset($_SESSION['error']) ) {
    echo ('<p style="color:red;">'.htmlentities($_SESSION['error'])."</p>\n");
    unset($_SESSION['error']);
  }
  if (isset($_SESSION['success']) ) {
    echo ('<p style="color:green;">'.htmlentities($_SESSION['success'])."</p>\n");
    unset($_SESSION['success']);
  }
}

function loadPos ($pdo, $profile_id) {
  $stmt3 = $pdo->prepare('SELECT * FROM Position WHERE profile_id = :prof ORDER BY rank');
  $stmt3->execute(array(":prof" => $_REQUEST['profile_id']));
  $positions = array();
  while ($row = $stmt3->fetch(PDO::FETCH_ASSOC) ) {
  $positions[] = $row;
  }
  return $positions;
}

function loadEdu ($pdo, $profile_id) {
  $stmt4 = $pdo->prepare('SELECT name, year FROM Education Join Institution on Education.institution_id = Institution.institution_id WHERE profile_id = :prof ORDER BY rank');
  $stmt4->execute(array(":prof" => $profile_id));
  $educations = $stmt4->fetchAll(PDO::FETCH_ASSOC);
  return $educations;
}
?>

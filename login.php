<?php // Do not put any HTML above this line
session_start();
require_once "pdo.php";
require_once "bootstrap.php";
require_once "util.php";
flashMessages();
if ( isset($_POST['cancel'] ) ) {
    header("Location: index.php");
    session_destroy();
    return;
  }

if ( isset($_POST['email']) && isset($_POST['pass']) ) {

       $salt = 'XyZzy12*_';
       $check = hash('md5', $salt.$_POST['pass']);
       $stmt = $pdo->prepare('SELECT user_id, name FROM users
       WHERE email = :em AND password = :pw');
       $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
       $row = $stmt->fetch(PDO::FETCH_ASSOC);

       if ( $row !== false ) {
       error_log("Login success ".$_POST['email']);
       $_SESSION['name'] = $row['name'];
       $_SESSION['user_id'] = $row['user_id'];
       $_SESSION['success'] = "You are Logged In";
       header("Location: index.php");
       return;
       }
       else {
       $_SESSION["error"] = "Incorrect password or email";
       error_log("Login fail ".$_POST['email']." $check");
       header("Location: login.php");
       return;
       }
  }




// Fall through into the View
?>
<!DOCTYPE html>
<html>
<head>
<title>Denis Oleynik's Login Page a419a784 </title>
</head>
<body>



<div class="container">
<h1>Please Log In</h1>
<form method="POST">
<label for="id1">Enter your email</label>
<input type="text" name="email" id="id1"><br/>
<label for="id2">Password</label>
<input type="password" name="pass" id="id2"><br/>
<input type="submit" onclick="return doValidate();" value="Log In">
<input type="submit" name="cancel" value="Cancel">
</form>
</div>

<script>
function doValidate() {
console.log('Validating...');
try {
       em = document.getElementById('id1').value;
       pw = document.getElementById('id2').value;
       console.log("Validating em="+em);
       console.log("Validating pw="+pw);
       if (pw == null || pw == "" || em == null || em == "") {
          alert("Both fields must be filled out");
          return false;
          }
       if (!em.includes("@")) {
        alert("Email should have @");
        return false;
       }


return true;
}
catch(e) {
return false;
}
return false;
}

</script>

</body>

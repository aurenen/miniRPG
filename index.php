<?php
require_once "utils/functions.php";
if( isLogged() ) {
  $profile = getProfile($_SESSION['uid']);
include "header.php";

?>

<h1>Welcome!</h1>
<p>Successful logged in.</p>

<?php } else { 
include "header.php"; ?>

<h1>Welcome!</h1>
<p>Logged out, please log in or register for an account.</p>

<?php } include_once "footer.php"; ?>
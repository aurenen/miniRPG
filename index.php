<?php
require_once "utils/functions.php";
include "header.php";
if( isLogged() ) {
?>

<h1>Welcome!</h1>
<p>Successful logged in.</p>

<?php } else { ?>

<h1>Welcome!</h1>
<p>Logged out, please log in or register for an account.</p>

<?php } include_once "footer.php"; ?>
<?php
require_once "utils/functions.php";
include_once "header.php";

if( isLogged() ) {
?>

<p>Logged in</p>

<?php } else { ?>

      
<p>Logged out</p>

<?php } include_once "footer.php"; ?>
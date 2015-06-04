<?php
require_once "utils/functions.php";
include "header.php";
if( isLogged() ) :
?>

<?php if ( isNew($_SESSION['uid']) ): ?>
<div class="jumbotron first">
  <h1>Thank you for registering!</h1>
  <p>The first thing you should do is select your character's class. Without picking a class, you can't participate in many of the activities, such as the <em>Battleground</em> and user <em>Ranking</em>.</p>
  <p>If you don't know what class to pick, <a href="features.php?classes">read about them here</a> or take this <a href="quiz.php">quiz</a> to find out which one is best suited for you.</p>
  <p><a class="btn btn-default btn-lg" href="selectclass.php" role="button">Select Your Class</a></p>
</div>

<?php endif; // isNew ?>

<h1>Welcome!</h1>
<p>Successfully logged in.</p>



<?php else: ?>

<h1>Welcome!</h1>
<p>Logged out, please log in or register for an account.</p>

<?php endif; ?>
<p>
    <?php echo $site_title; ?> is currently in alpha testing, if you would like to sign up for the closed beta, please 
    <a href="http://eepurl.com/bf_sOL">sign up for the mailing list</a>, and when it's ready you'll be notified.
</p>

<?php include_once "footer.php"; ?>
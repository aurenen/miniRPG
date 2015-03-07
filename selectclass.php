<?php
require_once "utils/functions.php";
include_once "header.php";

if( isLogged() && isNew($_SESSION['uid']) ) {
  $profile = getProfile($_SESSION['uid']);
  $stats = getStats($_SESSION['uid']);

?>

<h1>Select Your Class</h1>

<div class="container-fluid">
  <form action="selectclass.php" method="post">
    <div class="row">
      <div class="col-sm-4"><div class="itembox">
        <h3>Warrior</h3>
        <img class="center-block img-responsive" src="http://placehold.it/300x160" alt="Warrior" />
        <button type="submit" name="class_warrior" class="btn btn-default">Play as Warrior</button>
      </div></div>
      <div class="col-sm-4"><div class="itembox">
        <h3>Enchanter</h3>
        <img class="center-block img-responsive" src="http://placehold.it/300x160" alt="Enchanter" />
        <button type="submit" name="class_enchanter" class="btn btn-default">Player as Enchanter</button>
      </div></div>
      <div class="col-sm-4"><div class="itembox">
        <h3>Ranger</h3>
        <img class="center-block img-responsive" src="http://placehold.it/300x160" alt="Ranger" />
        <button type="submit" name="class_ranger" class="btn btn-default">Play as Ranger</button>
      </div></div>
    </div>
    <div class="row">
      <div class="col-sm-4"><div class="itembox">
        <h3>Templar</h3>
        <img class="center-block img-responsive" src="http://placehold.it/300x160" alt="Templar" />
        <button type="submit" name="class_templar" class="btn btn-default">Play as Templar</button>
      </div></div>
      <div class="col-sm-4"><div class="itembox">
        <h3>Mystic</h3>
        <img class="center-block img-responsive" src="http://placehold.it/300x160" alt="Mystic" />
        <button type="submit" name="class_mystic" class="btn btn-default">Play as Mystic</button>
      </div></div>
      <div class="col-sm-4"><div class="itembox">
        <h3>Rogue</h3>
        <img class="center-block img-responsive" src="http://placehold.it/300x160" alt="Rogue" />
        <button type="submit" name="class_rogue" class="btn btn-default">Play as Rogue</button>
      </div></div>
    </div>
  </form>
</div> <!-- end .container-fluid -->


<?php } // end isLogged() && isNew()
else { 
  header('Location: index.php');
  exit();
}
include_once("footer.php"); ?>
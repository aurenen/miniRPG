<?php
require_once "utils/functions.php";

if( isLogged() && !isNew($_SESSION['uid']) ) {
  $profile = getProfile($_SESSION['uid']);
  $stats = getStats($_SESSION['uid']);
  $chara_class = getClass($_SESSION['uid']);

  // set $url to page.php?QUERY
  if (isset($_SERVER['QUERY_STRING'])) {
    $url = $_SERVER['QUERY_STRING'];
    parse_str($url, $vars);
  } 
  else {
    $url = $_GET;
  }

include_once "header.php";
?>

      <div class="media">
        <h1><?php echo $profile["character_name"]; ?> <small><?php echo $chara_class ?></small></h1>
        <p class="lead">
          <abbr title="Strength">STR</abbr> : <?php echo $stats["str"] ?> / 
          <abbr title="Vitality">VIT</abbr> : <?php echo $stats["vit"] ?> / 
          <abbr title="Dexterity">DEX</abbr> : <?php echo $stats["dex"] ?> / 
          <abbr title="Agility">AGI</abbr> : <?php echo $stats["agi"] ?>  / 
          <abbr title="Cunning">CUN</abbr> : <?php echo $stats["cun"] ?> / 
          <abbr title="Wisdom">WIS</abbr> : <?php echo $stats["wis"] ?>
        </p>
      </div>

<div class="container-fluid">
  <div class="row">
  <?php if ($url == "setclass") { ?>
  <div class="alert alert-info" role="alert">You have successfully selected your character's class.</div>

  <?php } if ($url == "updated") { ?>
  <div class="alert alert-info" role="alert">You have successfully updated your profile.</div>
  <?php } ?>
    <div class="col-sm-4">
      <div class="profile-avatar">
        <img class="center-block" src="http://placehold.it/200x300" alt="Avatar" />
      </div>
    </div>
    <div class="col-sm-8">
        <table class="table table-striped">
          <thead>
            <tr>
              <th></th>
              <th width="75%"></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th>Email</th>
              <td><?php echo $profile['email'] ?></td>
            </tr>
            <tr>
              <th>Character Name</th>
              <td><?php echo $profile['character_name'] ?></td>
            </tr>
            <tr>
              <th>Class</th>
              <td><?php echo $chara_class ?></td>
            </tr>
            <tr>
              <th>Level</th>
              <td><?php echo $profile['level'] ?></td>
            </tr>
            <tr>
              <th>Money</th>
              <td><?php echo $profile['money'] ?></td>
            </tr>
            <tr>
              <th>Stats</th>
              <td>
                <abbr title="Strength">STR</abbr> : <?php echo $stats["str"] ?> / 
                <abbr title="Vitality">VIT</abbr> : <?php echo $stats["vit"] ?> / 
                <abbr title="Dexterity">DEX</abbr> : <?php echo $stats["dex"] ?> / 
                <abbr title="Agility">AGI</abbr> : <?php echo $stats["agi"] ?>  / 
                <abbr title="Cunning">CUN</abbr> : <?php echo $stats["cun"] ?> / 
                <abbr title="Wisdom">WIS</abbr> : <?php echo $stats["wis"] ?>

                <hr class="clear">
                Your stat points are out of a total of 100, divided into 6 categories based on your class.
                <hr class="clear">
                Higher VIT means more health points, which may help you survive monster encounters. More WIS means more magic power, so you can defeat monsters better. And the greater your AGI, the greater chance you have of dodging attacks.
              </td>
            </tr>
          </tbody>
        </table>
    </div>
  </div>
</div> <!-- end .container-fluid -->


<?php } // end isLogged()
else { 
  header('Location: index.php');
  exit();
}
include_once("footer.php"); ?>
<?php
require_once "utils/functions.php";

if( isLogged() && !isNew($_SESSION['uid']) ) {
  $profile = getProfile($_SESSION['uid']);
  $stats = getStats($_SESSION['uid']);
  $chara_class = getClass($_SESSION['uid']);
  $user_exp = getExp($_SESSION['uid']);

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
        <img class="center-block" src="images/ani/<?php echo strtolower($chara_class) ?>_idle.gif" alt="Avatar" />
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
              <th>Gender</th>
              <td><?php echo ucwords($profile['gender']) ?></td>
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
                <abbr title="Health Points">HP</abbr> : <?php echo $stats["hp"] ?> / 
                <abbr title="Skill Points">SP</abbr> : <?php echo $stats["sp"] ?>
                <hr class="clear">
                <abbr title="Experience">EXP</abbr> : <?php echo $user_exp ?> / <?php echo getMaxExp($profile["level"]); ?>
                <div class="progress">
                  <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="<?php echo $stats["exp"] ?>" aria-valuemin="0" aria-valuemax="<?php echo $stats["exp"] ?>" style="min-width: 2em; width:<?php echo round($user_exp / getMaxExp($profile["level"]), 3) * 100; ?>%">
                     <?php echo round($user_exp / getMaxExp($profile["level"]), 3) * 100; ?>%
                  </div>
                </div>
                <abbr title="Strength">STR</abbr> : <?php echo $stats["str"] ?> / 
                <abbr title="Vitality">VIT</abbr> : <?php echo $stats["vit"] ?> / 
                <abbr title="Dexterity">DEX</abbr> : <?php echo $stats["dex"] ?> / 
                <abbr title="Agility">AGI</abbr> : <?php echo $stats["agi"] ?>  / 
                <abbr title="Cunning">CUN</abbr> : <?php echo $stats["cun"] ?> / 
                <abbr title="Wisdom">WIS</abbr> : <?php echo $stats["wis"] ?>

                <hr class="clear">
                Your stat points are out of a total of <?php echo $profile['level'] * 30 ?>, divided into 6 categories based on your class.
                <hr class="clear">
                Higher VIT, WIS, and AGI means more health and defense, which may help you survive monster encounters. More STR, DEX, and INT means more attack power, so you can defeat monsters better.
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
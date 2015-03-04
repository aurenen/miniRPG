<?php
require_once "utils/functions.php";

if( isLogged() ) {
	$profile = getProfile($_SESSION['uid']);

include_once "header.php";
?>

    <div class="container">
      <div class="media">
        <h1><?php echo $profile["character_name"]; ?> <small><?php echo "Enchanter" ?></small></h1>
        <p class="lead">
          <abbr title="Strength / Attack">STR</abbr> : <?php echo $profile["str"] ?> / 
          <abbr title="Vitality / Mana">VIT</abbr> : <?php echo $profile["vit"] ?> / 
          <abbr title="Agility / Evasion">AGI</abbr> : <?php echo $profile["agi"] ?>  / 
          <abbr title="Wisdom / Mana">WIS</abbr> : <?php echo $profile["wis"] ?> / 
          <abbr title="Dexterity / Evasion">DEX</abbr> : <?php echo $profile["dex"] ?>  / 
          <abbr title="Cunning / Mana">CUN</abbr> : <?php echo $profile["cun"] ?>


        </p>
      </div>

      <table class="table">
        <thead>
          <tr>
            <th></th>
            <th width="75%">Current</th>
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
            <td><?php echo "Enchanter" ?></td>
          </tr>
          <tr>
            <th>Money</th>
            <td><?php echo $profile['money'] ?></td>
          </tr>
          <tr>
            <th>Stats</th>
            <td>
              
	          <abbr title="Strength / Attack">STR</abbr> : <?php echo $profile["str"] ?> / 
	          <abbr title="Vitality / Mana">VIT</abbr> : <?php echo $profile["vit"] ?> / 
	          <abbr title="Agility / Evasion">AGI</abbr> : <?php echo $profile["agi"] ?>  / 
	          <abbr title="Wisdom / Mana">WIS</abbr> : <?php echo $profile["wis"] ?> / 
	          <abbr title="Dexterity / Evasion">DEX</abbr> : <?php echo $profile["dex"] ?>  / 
	          <abbr title="Cunning / Mana">CUN</abbr> : <?php echo $profile["cun"] ?>

              <hr class="clear">
              Your stat points are out of a total of 100, divided into 6 categories based on your class.
              <hr class="clear">
              Higher VIT means more health points, which may help you survive monster encounters. More WIS means more magic power, so you can defeat monsters better. And the greater your AGI, the greater chance you have of dodging attacks.
            </td>
          </tr>
        </tbody>
      </table>


<?php } // end isLogged()
else { 
  header('Location: index.php');
  exit();
}
include_once("footer.php"); ?>
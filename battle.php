<?php
require_once "utils/functions.php";

if( isLogged() && !isNew($_SESSION['uid']) ) {
  $isBattle = true;
  $profile = getProfile($_SESSION['uid']);
  $stats = getStats($_SESSION['uid']);
  $user_atk = floor(($stats["str"] + $stats["dex"] / 5) + ($stats["wis"] / 3) + ($profile["level"] / 4));
  $user_def = floor(($stats["agi"] * 0.35) + max($stats["vit"] * 0.3, (pow($stats["vit"], 2) / 170) - 1) + ($stats["cun"] * 0.25));
  $chara_class = strtolower(getClass($_SESSION['uid']));
  $user_exp = getExp($_SESSION['uid']);
  $class_num = $profile["character_class"] - 1;
  $monster_id = rand(1,3);

  $skills = array
    (
    array("Smash","Charge"),
    array("Fire Ball","Flame Blast"),
    array("Sharp Shooting","Explosive Arrows"),
    array("Charge","Impale"),
    array("Holy Nova","Seal of Light"),
    array("Backstab","Cloak and Dagger"),
    );

  $token = md5(uniqid(rand(),true));
  $_SESSION['token'] = $token;

  // set $url to page.php?QUERY
  if (isset($_SERVER['QUERY_STRING'])) {
    $url = $_SERVER['QUERY_STRING'];
    parse_str($url, $vars);
  } 
  else {
    $url = $_GET;
  }
  if(($_SERVER["REQUEST_METHOD"] == "POST") /*&& isset($_SESSION['token']) && ($_POST['token'] == $_SESSION['token'])*/) {
    gainExp($_SESSION['uid']);
  }


include_once "header.php";
?>

<h1>Battleground</h1>
<div class="table-responsive battle-wrap">
  <div class="battle-info">
    <div id="battle-text">Ready for battle</div>
  </div>
  <div id="battle-attack-user">
  </div>
  <div id="battle-attack-monster">
  </div>
  <table class="table battle">
    <thead>
      <tr>
        <th class="battle-chara-name"><?php echo $profile["character_name"] . " lvl " . $profile["level"]; ?></th>
        <th width="50%"><?php echo "" ?></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="battle-chara">
          <img id="battle-user" class="center-block img-responsive" src="images/ani/<?php echo $chara_class ?>_idle.gif" alt="Avatar" />

          <div class="progress">
            <div id="playerHPbar" class="progress-bar progress-bar-danger progress-bar-striped" role="progressbar" aria-valuenow="<?php echo $stats["hp"] ?>" aria-valuemin="0" aria-valuemax="<?php echo $stats["hp"] ?>" style="min-width: 2em; width: 100%">
              HP <span id="playerHP"></span> / <?php echo $stats["hp"] ?>
            </div>
          </div>
        </td>
        <td class="battle-monster">
          <img id="battle-enemy" class="center-block img-responsive" src="images/ani/monster<?php echo $monster_id ?>_idle.gif" alt="Avatar" />
          <div class="progress">
            <div id="monsterHPbar" class="progress-bar progress-bar-danger  progress-bar-striped" role="progressbar" aria-valuenow="<?php echo $stats["hp"] ?>" aria-valuemin="0" aria-valuemax="<?php echo $stats["hp"] ?>" style="min-width: 2em; width: 100%">
              HP <span id="monsterHP"></span> / <span id="monsterHPtotal"></span>
            </div>
          </div>
        </td>
      </tr>
      <tr>
        <td class="battle-control" colspan="2">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-3">
                <p>An elemental move based on magic</p>
                <button id="skill-1" class="btn btn-default btn-block" onclick="attack(1); return false;"><?php echo $skills[$class_num][0] ?> (5 SP)</button>
              </div>
              <div class="col-sm-3">
                <p>Restore half of your Skill Points</p>
                <button id="skill-2" class="btn btn-default btn-block" onclick="recoversp(); return false;">SP Recovery (0 SP)</button>
              </div>
              <div class="col-sm-3">
                <p>An elemental move based on magic</p>
                <button id="skill-3" class="btn btn-default btn-block" onclick="attack(2); return false;"><?php echo $skills[$class_num][1] ?> (10 SP)</button>
              </div>
              <div class="col-sm-3">
                <p>Restore a portion of your Health</p>
                <button id="skill-4" class="btn btn-default btn-block" onclick="recoverhp(); return false;">HP Recovery  (5 SP)</button>
              </div>
            </div>
          </div>
          <div class="progress">
            <div id="playerSPbar" class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="<?php echo $stats["sp"] ?>" aria-valuemin="0" aria-valuemax="<?php echo $stats["sp"] ?>" style="min-width: 2em; width: 100%">
              SP <span id="playerSP"></span> / <?php echo $stats["sp"] ?>
            </div>
          </div>

        </td>
      </tr>
    </tbody>
  </table>
  EXP <?php echo getExp($_SESSION['uid']) . "/" . getMaxExp($profile["level"]); ?>
  <div class="progress">
    <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="<?php echo $stats["exp"] ?>" aria-valuemin="0" aria-valuemax="<?php echo $stats["exp"] ?>" style="min-width: 2em; width:<?php echo round($user_exp / getMaxExp($profile["level"]), 3) * 100; ?>%">
      <?php echo round($user_exp / getMaxExp($profile["level"]), 3) * 100; ?>%
    </div>
  </div>
</div>

<script type="text/javascript">
  var myClass = "<?php echo $chara_class ?>";
  var myLevel = <?php echo $profile["level"] ?>;
  var myAtk = <?php echo $user_atk ?> * myLevel * 0.35;
  var myDef = <?php echo $user_def ?> * myLevel * 0.35;
  var myTotalHP = <?php echo $stats["hp"] ?>;
  var myTotalSP = <?php echo $stats["sp"] ?>;
  var myCurrentHP = myTotalHP;
  var myCurrentSP = myTotalSP;
  document.getElementById('playerHP').innerHTML = myCurrentHP;
  document.getElementById('playerSP').innerHTML = myCurrentSP;

  var enemyId = <?php echo $monster_id ?>;
  var enemyAtk = <?php echo floor($user_atk * rand(7, 11) * 0.1) ?> * myLevel * 0.37;
  var enemyDef = <?php echo floor($user_def * rand(7, 11) * 0.1) ?> * myLevel * 0.33;
  var enemyTotalHP = Math.floor(<?php echo $stats["hp"] ?> * (Math.random() * (1 - 0.75) + 0.75));
  var enemyTotalSP = <?php echo $stats["sp"] ?>;
  var enemyCurrentHP = enemyTotalHP;
  var enemyCurrentSP = enemyTotalSP;
  document.getElementById('monsterHP').innerHTML = enemyCurrentHP;
  document.getElementById('monsterHPtotal').innerHTML = enemyTotalHP;
</script>
<form role="form" action="battle.php" method="post" id="battle-play">
  <input type="hidden" name="token" value="<?php echo $token; ?>">
</form>
<?php } // end isLogged()
else { 
  header('Location: index.php');
  exit();
}
include_once("footer.php"); ?>
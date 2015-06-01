<?php
require_once "utils/functions.php";

if( isLogged() && !isNew($_SESSION['uid']) ) {
  $isBattle = true;
  $profile = getProfile($_SESSION['uid']);
  $stats = getStats($_SESSION['uid']);
  $chara_class = getClass($_SESSION['uid']);

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
  if(($_SERVER["REQUEST_METHOD"] == "POST") && isset($_SESSION['token']) && ($_POST['token'] == $_SESSION['token'])) {
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
        <th><?php echo $profile["character_name"]; ?></th>
        <th width="50%">Monster Name</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="battle-chara">
          <img id="battle-user" class="center-block img-responsive" src="images/ani/ranger_idle.gif" alt="Avatar" />

          <div class="progress">
            <div id="playerHPbar" class="progress-bar progress-bar-danger progress-bar-striped" role="progressbar" aria-valuenow="<?php echo $stats["hp"] ?>" aria-valuemin="0" aria-valuemax="<?php echo $stats["hp"] ?>" style="min-width: 2em; width: 100%">
              HP <span id="playerHP"></span> / <?php echo $stats["hp"] ?>
            </div>
          </div>
        </td>
        <td class="battle-monster">
          <img id="battle-enemy" class="center-block img-responsive" src="images/ani/monster_idle.gif" alt="Avatar" />
          <div class="progress">
            <div id="monsterHPbar" class="progress-bar progress-bar-danger  progress-bar-striped" role="progressbar" aria-valuenow="<?php echo $stats["hp"] ?>" aria-valuemin="0" aria-valuemax="<?php echo $stats["hp"] ?>" style="min-width: 2em; width: 100%">
              HP <span id="monsterHP"></span> / <?php echo $stats["hp"] ?>
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
                <a class="btn btn-default btn-block" href="#" role="button" onclick="attack(1); return false;">Fire Ball</a>
              </div>
              <div class="col-sm-3">
                <p>Restore half of your Skill Points</p>
                <a class="btn btn-default btn-block" href="#" role="button" onclick="recoversp(); return false;">SP Recovery</a>
              </div>
              <div class="col-sm-3">
                <p>An elemental move based on magic</p>
                <a class="btn btn-default btn-block" href="#" role="button" onclick="attack(2); return false;">Flame Blast</a>
              </div>
              <div class="col-sm-3">
                <p>Restore a portion of your Health</p>
                <a class="btn btn-default btn-block" href="#" role="button" onclick="recoverhp(); return false;">HP Recovery</a>
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
</div>

<script type="text/javascript">
  var myLevel=<?php echo $profile["level"] ?>;
  var myTotalHP=<?php echo $stats["hp"] ?>;
  var myTotalSP=<?php echo $stats["sp"] ?>;
  var myCurrentHP = myTotalHP;
  var myCurrentSP = myTotalSP;
  document.getElementById('playerHP').innerHTML = myCurrentHP;
  document.getElementById('playerSP').innerHTML = myCurrentSP;

  var enemyTotalHP=<?php echo $stats["hp"] ?>;
  var enemyTotalSP=<?php echo $stats["sp"] ?>;
  var enemyCurrentHP = enemyTotalHP;
  var enemyCurrentSP = enemyTotalSP;
  document.getElementById('monsterHP').innerHTML = myCurrentHP;
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
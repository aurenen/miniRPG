<?php
require_once "utils/functions.php";

if( isLogged() && !isNew($_SESSION['uid']) ) {
  $isBattle = true;
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

<h1>Battleground</h1>
<div class="table-responsive battle-wrap">
  <div class="battle-info">
    <div id="battle-text">Ready for battle</div>
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
          <img class="center-block img-responsive" src="http://placehold.it/200x300" alt="Avatar" />

          <?php 
          $battle_total_hp = $stats["hp"] * 10;  
          $battle_hp = $stats["hp"]; 
          $battle_percent_hp = $stats["hp"] / $stats["hp"] * 100; 

          $battle_total_sp = $stats["sp"];  
          $battle_sp = $stats["sp"]; 
          $battle_percent_sp = $stats["sp"] / $stats["sp"] * 100; 
          ?>
          <div class="progress">
            <div id="playerHPbar" class="progress-bar progress-bar-danger progress-bar-striped" role="progressbar" aria-valuenow="<?php echo $battle_total_hp ?>" aria-valuemin="0" aria-valuemax="<?php echo $battle_total_hp ?>" style="min-width: 2em; width: 100%">
              HP <span id="playerHP"></span> / <?php echo $battle_total_hp ?>
            </div>
          </div>
        </td>
        <td class="battle-monster">
          <img class="center-block img-responsive" src="http://www.cs.csub.edu/~achen/cs311/week09_final/final/monsters/9.png" alt="Avatar" />
          <div class="progress">
            <div id="monsterHPbar" class="progress-bar progress-bar-danger  progress-bar-striped" role="progressbar" aria-valuenow="<?php echo $battle_total_hp ?>" aria-valuemin="0" aria-valuemax="<?php echo $battle_total_hp ?>" style="min-width: 2em; width: 100%">
              HP <span id="monsterHP"></span> / <?php echo $battle_total_hp ?>
            </div>
          </div>
        </td>
      </tr>
      <tr>
        <td class="battle-control" colspan="2">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-3">
                <p>Attack description</p>
                <a class="btn btn-default btn-block" href="#" role="button" onclick="attack(); return false;">Attack</a>
              </div>
              <div class="col-sm-3">
                <p>Attack description</p>
                <a class="btn btn-default btn-block" href="#" role="button">Attack</a>
              </div>
              <div class="col-sm-3">
                <p>Attack description</p>
                <a class="btn btn-default btn-block" href="#" role="button">Attack</a>
              </div>
              <div class="col-sm-3">
                <p>Attack description</p>
                <a class="btn btn-default btn-block" href="#" role="button">Attack</a>
              </div>
            </div>
          </div>
          <div class="progress">
            <div id="playerSPbar" class="progress-bar progress-bar-info progress-bar-striped" role="progressbar" aria-valuenow="<?php echo $battle_total_sp ?>" aria-valuemin="0" aria-valuemax="<?php echo $battle_total_sp ?>" style="min-width: 2em; width: 100%">
              SP <span id="playerSP"></span> / <?php echo $battle_total_sp ?>
            </div>
          </div>

        </td>
      </tr>
    </tbody>
  </table>
</div>

<script type="text/javascript">
  var myTotalHP=<?php echo $battle_total_hp ?>;
  var myTotalSP=<?php echo $battle_total_sp ?>;
  var myCurrentHP = myTotalHP;
  var myCurrentSP = myTotalSP;
  document.getElementById('playerHP').innerHTML = myCurrentHP;

  var enemyTotalHP=<?php echo $battle_total_hp ?>;
  var enemyTotalSP=<?php echo $battle_total_sp ?>;
  var enemyCurrentHP = enemyTotalHP;
  var enemyCurrentSP = enemyTotalSP;
  document.getElementById('monsterHP').innerHTML = myCurrentHP;
</script>

<?php } // end isLogged()
else { 
  header('Location: index.php');
  exit();
}
include_once("footer.php"); ?>
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
        <h1>Player Rankings</h1>
      </div>

<div class="table-responsive">
  <table class="table table-hover">
    <thead>
      <tr>
        <th>#</th>
        <th>Character Name</th>
        <th>Level</th>
        <th>Exp</th>
        <th>Class</th>
        <th>Money</th>
      </tr>
    </thead>
    <tbody>
  <?php 
  $rank = getRanking();

  $i = 1;
  foreach ($rank as $r) {
    echo "<tr>\n";
    echo "<th scope=\"row\">". $i ."</th><td>" . 
          $r[character_name] . "</td><td>" . 
          $r[level] . "</td><td>" . 
          $r[exp] . "</td><td>" . 
          $r[type] . "</td><td>" . 
          $r[money] . "</td>";
    echo "</tr>\n";
    $i++;
  }


  ?>
    </tbody>
  </table>
</div> <!-- end .container-fluid -->


<?php } // end isLogged()
else { 
  header('Location: index.php');
  exit();
}
include_once("footer.php"); ?>
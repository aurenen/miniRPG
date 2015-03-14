<?php
require_once "utils/functions.php";

if( isLogged() && isNew($_SESSION['uid']) ) {
  if(isset($_POST['submit'])) {
    $q1 = $_POST['q1'];
    $q2 = $_POST['q2'];
    $q3 = $_POST['q3'];
    $q4 = $_POST['q4'];
    $q5 = $_POST['q5'];

    if( !isset($q1) || !isset($q2) || !isset($q3) || !isset($q4) || !isset($q5) ) {
      header('Location: quiz.php?error');
      exit();
    }

    $q1 = cleanPOST($_POST['q1']);
    $q2 = cleanPOST($_POST['q2']);
    $q3 = cleanPOST($_POST['q3']);
    $q4 = cleanPOST($_POST['q4']);
    $q5 = cleanPOST($_POST['q5']);
    
    $score = $q1 + $q2 + $q3 + $q4 + $q5;
    
    $classtype = classQuiz($score);
    
    if ($classtype == "warrior")
      header('Location: quiz.php?warrior');
    elseif ($classtype == "enchanter")
      header('Location: quiz.php?enchanter');
    elseif ($classtype == "ranger")
      header('Location: quiz.php?ranger');
    elseif ($classtype == "templar")
      header('Location: quiz.php?templar');
    elseif ($classtype == "mystic")
      header('Location: quiz.php?mystic');
    elseif ($classtype == "rogue")
      header('Location: quiz.php?rogue');
    exit();
  }
  else { // set $url to page.php?QUERY
    if (isset($_SERVER['QUERY_STRING'])) {
      $url = $_SERVER['QUERY_STRING'];
      parse_str($url, $vars);
    } 
    else {
      $url = $_GET;
    }
  
include_once "header.php";

?>


<div class="container-fluid">
<h1>Character Class Quiz</h1>


  <?php if ($url == "warrior") { ?>
  <div class="jumbotron"><h2>You got Warrior! <a href="selectclass.php">Select your class?</a></h2></div>

  <?php } elseif ($url == "enchanter") { ?>
  <div class="jumbotron"><h2>You got Enchanter! <a href="selectclass.php">Select your class?</a></h2></div>

  <?php } elseif ($url == "ranger") { ?>
  <div class="jumbotron"><h2>You got Ranger! <a href="selectclass.php">Select your class?</a></h2></div>

  <?php } elseif ($url == "templar") { ?>
  <div class="jumbotron"><h2>You got Templar! <a href="selectclass.php">Select your class?</a></h2></div>

  <?php } elseif ($url == "mystic") { ?>
  <div class="jumbotron"><h2>You got Mystic! <a href="selectclass.php">Select your class?</a></h2></div>

  <?php } elseif ($url == "rogue") { ?>
  <div class="jumbotron"><h2>You got Rogue! <a href="selectclass.php">Select your class?</a></h2></div>
  <?php } else { ?>

  <form action="quiz.php" method="post">
     <div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">1) When you run into a problem, how do you go about solving it?</h3>
  </div>
  <div class="panel-body">
    <div class="radio">
      <label>
        <input type="radio" name="q1" id="optionsRadios1" value="2" required>
        Use your physical powers to defeat it. 
      </label>
    </div>
    <div class="radio">
      <label>
        <input type="radio" name="q1" id="optionsRadios2" value="4">
        Cast an appropriate spell to make it go away.
      </label>
    </div>
    <div class="radio">
      <label>
        <input type="radio" name="q1" id="optionsRadios3" value="6">
        Secretly sabotage whatever it is that's causing your problems.
      </label>
    </div>
  </div>
</div>

<!-- question separator -->

<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">2) In a perfect world, where you can do whatever you like, you'd spend your life doing what?</h3>
  </div>
  <div class="panel-body">
    <div class="radio">
      <label>
        <input type="radio" name="q2" id="optionsRadios1" value="5" required>
        Stealing and breaking into restricted places for fun.
      </label>
    </div>
    <div class="radio">
      <label>
        <input type="radio" name="q2" id="optionsRadios2" value="6">
        Explore the wilderness and live off your own hunting abilities.
      </label>
    </div>
    <div class="radio">
      <label>
        <input type="radio" name="q2" id="optionsRadios3" value="3">
        Locking yourself up in the library reading up on all the knowledge.
      </label>
    </div>
    <div class="radio">
      <label>
        <input type="radio" name="q2" id="optionsRadios4" value="4">
        Volunteer your time helping the sick and wounded.
      </label>
    </div>
    <div class="radio">
      <label>
        <input type="radio" name="q2" id="optionsRadios5" value="3">
        Travel the world as a missonary.
      </label>
    </div>
    <div class="radio">
      <label>
        <input type="radio" name="q2" id="optionsRadios6" value="1">
        Become a professional sports player.
      </label>
    </div>
  </div>
</div>

<!-- question separator -->    

<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">3) A stray dog follows you on your way home, what do you do?</h3>
  </div>
  <div class="panel-body">
    <div class="radio">
      <label>
        <input type="radio" name="q3" id="optionsRadios1" value="2" required>
        Try to find its owner, and if that fails, adopt it.
      </label>
    </div>
    <div class="radio">
      <label>
        <input type="radio" name="q3" id="optionsRadios2" value="4">
        Drop it off at a safe shelter.
      </label>
    </div>
    <div class="radio">
      <label>
        <input type="radio" name="q3" id="optionsRadios3" value="6">
        Find a good buyer and sell the dog.
      </label>
    </div>
  </div>
</div>

<!-- question separator -->    

<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">4) What character traits do you consider to be virtuous?</h3>
  </div>
  <div class="panel-body">
    <div class="radio">
      <label>
        <input type="radio" name="q4" id="optionsRadios1" value="4" required>
        Compassion and intelligence.
      </label>
    </div>
    <div class="radio">
      <label>
        <input type="radio" name="q4" id="optionsRadios2" value="6">
        Cunning and a sharp wit.
      </label>
    </div>
    <div class="radio">
      <label>
        <input type="radio" name="q4" id="optionsRadios3" value="2">
        Honor and duty.
      </label>
    </div>
  </div>
</div>

<!-- question separator -->

<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">5) Which class do you think you're most suited to play?</h3>
  </div>
  <div class="panel-body">
    <div class="radio">
      <label>
        <input type="radio" name="q5" id="optionsRadios1" value="1" required>
        Warrior.
      </label>
    </div>
    <div class="radio">
      <label>
        <input type="radio" name="q5" id="optionsRadios2" value="2">
        Templar.
      </label>
    </div>
    <div class="radio">
      <label>
        <input type="radio" name="q5" id="optionsRadios3" value="3">
        Enchanter.
      </label>
    </div>
    <div class="radio">
      <label>
        <input type="radio" name="q5" id="optionsRadios4" value="4">
        Mystic.
      </label>
    </div>
    <div class="radio">
      <label>
        <input type="radio" name="q5" id="optionsRadios5" value="5">
        Rogue.
      </label>
    </div>
    <div class="radio">
      <label>
        <input type="radio" name="q5" id="optionsRadios6" value="6">
        Ranger.
      </label>
    </div>
  </div>
</div>

<button type="submit" name="submit" class="btn btn-success btn-lg center-block">Submit answers</button>

</form>
<?php } /* end else for url query string */ ?>
</div> <!-- end .container-fluid -->


<?php }
} // end isLogged() && isNew()
else { 
  header('Location: index.php');
  exit();
}
include_once("footer.php"); ?>
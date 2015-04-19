<?php
// session_start(); 

require_once "utils/functions.php";

if( isLogged() ) {
  header('Location: index.php');
  exit();
}

if(isset($_POST['submit'])) {
  $login_user = cleanPOST($_POST['login_user']);
  $login_pass = cleanPOST($_POST['login_pass']);

  if( empty($login_user) || empty($login_pass) ) {
    header('Location: login.php?error');
    exit();
  }
  
  login($login_user, $login_pass);
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

      <h2>Account Login</h2>
      <form class="form-horizontal" role="form" action="login.php" method="post">
        <fieldset>
          <div class="row">
            <div class="col-sm-6 col-md-offset-3">

              <?php if ($url == "failed") { ?>
              <div class="alert alert-danger" role="alert">No such account.</div>

              <?php } if ($url == "error") { ?>
              <div class="alert alert-danger" role="alert">Incorrect login username or password</div>

              <?php } if ($url == "regsuccess") { ?>
              <div class="alert alert-info" role="alert">Successful registration. Please login.</div>
              <?php } ?>

            </div>
          </div>

              <div class="form-group">
                <label for="inputEmail" class="col-sm-3 control-label">Email</label>
                <div class="col-sm-6">
                  <input name="login_user" type="text" class="form-control" id="inputEmail" placeholder="Email">
                </div>
              </div>
              <div class="form-group">
                <label for="inputPassword" class="col-sm-3 control-label">Password</label>
                <div class="col-sm-6">
                  <input name="login_pass" type="password" class="form-control" id="inputPassword" placeholder="Password">
                </div>
              </div>


              <div class="form-group">
                <div class="col-sm-9 col-sm-offset-3">
                  <button type="submit" name="submit" class="btn btn-success">Login</button>
                  <button type="reset" class="btn btn-default">Cancel</button>
                </div>
              </div>

        </fieldset>
      </form>

<?php } include_once "footer.php"; ?>
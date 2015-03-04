<?php
require_once 'utils/functions.php';

if( isLogged() ) {
  header('Location: index.php');
  exit();
}

if(isset($_POST['submit'])) {
  $login_user = cleanPOST($_POST['login_user']);
  $login_pass = cleanPOST($_POST['login_pass']);
  $login_pass2 = cleanPOST($_POST['login_pass2']);
  $login_chara = cleanPOST($_POST['login_chara']);
  $login_gender = cleanPOST($_POST['login_gender']);
  $login_agree = cleanPOST($_POST['login_agree']);

  $_SESSION['login_user'] = $login_user;
  $_SESSION['login_chara'] = $login_chara;

  if( empty($login_user) || empty($login_pass) || empty($login_pass2) || empty($login_chara) || empty($login_gender) || empty($login_agree) ) {
    header('Location: register.php?required');
    exit();
  }
  
  elseif( $login_pass != $login_pass2 ) {
    header('Location: register.php?password');
    exit();
  }
  
  else
    registerAccount($login_user, $login_pass, $login_chara, $login_gender);
}

else { // set $url to page.php?QUERY
  if (isset($_SERVER['QUERY_STRING'])) {
    $url = $_SERVER['QUERY_STRING'];
    parse_str($url, $vars);
  } 
  else {
    $url = $_GET;
  }
  
include_once 'header.php';
?>

      <form class="form-horizontal" role="form" action="register.php" method="post">
        <fieldset>
          <h2>Register for an account</h2>
            <?php if ($url == "error") { ?>
            <div class="alert alert-danger" role="alert">Email already registered.</div>

            <?php } if ($url == "invalid_email") { ?>
            <div class="alert alert-danger" role="alert">Email is invalid.</div>

            <?php } if ($url == "invalid_password") { ?>
            <div class="alert alert-danger" role="alert">Your password is too long.</div>

            <?php } if ($url == "required") { ?>
            <div class="alert alert-danger" role="alert">Please fill in all fields.</div>

            <?php } if ($url == "password") { ?>
            <div class="alert alert-danger" role="alert">Passwords do not match</div>
            <?php } ?>
          
            <div class="row">

            <div class="col-sm-5 col-sm-push-7">
              <h3>Guidelines</h3>
              <ol>
                <li><p>Sample</p></li>
                <li><p>Sample</p></li>
                <li><p>Sample</p></li>
                <li><p>Sample</p></li>
              </ol>
            </div>

            <div class="col-sm-7 col-sm-pull-5">
              <div class="form-group<?php if ($url == "error" || $url== "invalid_email") echo " has-error"; ?>">
                <label for="inputEmail" class="col-sm-3 control-label">Email</label>
                <div class="col-sm-9">
                  <input name="login_user" type="text" class="form-control" id="inputEmail" placeholder="Email" value="<?php echo isset($_SESSION['login_user']) ? $_SESSION['login_user'] : '' ?>">
                </div>
              </div>
              <div class="form-group<?php if ($url == "password" || $url == "invalid_password") echo " has-error"; ?>">
                <label for="inputPassword" class="col-sm-3 control-label">Password</label>
                <div class="col-sm-9">
                  <input name="login_pass" type="password" class="form-control" id="inputPassword" placeholder="Password">
                </div>
              </div>
              <div class="form-group<?php if ($url == "password" || $url == "invalid_password") echo " has-error"; ?>">
                <div class="col-sm-9 col-sm-offset-3">
                  <input name="login_pass2" type="password" class="form-control" id="inputPassword" placeholder="Password again">
                </div>
              </div>
              <div class="form-group">
                <label for="characterName" class="col-sm-3 control-label">Character Name</label>
                <div class="col-sm-9">
                  <input name="login_chara" type="text" class="form-control" id="characterName" placeholder="Character Name" value="<?php echo isset($_SESSION['login_chara']) ? $_SESSION['login_chara'] : '' ?>">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-3 control-label">Gender</label>
                <div class="col-sm-9">
                  <div class="radio">
                    <label>
                      <input type="radio" name="login_gender" id="gender_female" value="female">
                      Female Character
                    </label>
                  </div>
                  <div class="radio">
                    <label>
                      <input type="radio" name="login_gender" id="gender_male" value="male">
                      Male Character
                    </label>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-9 col-sm-offset-3">
                  <div class="checkbox">
                    <label>
                      <input name="login_agree" type="checkbox"> I have read and understood the rules and terms of use.
                    </label>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-9 col-sm-offset-3">
                  <button type="submit" name="submit" class="btn btn-success">Submit</button>
                  <button type="reset" class="btn btn-default">Cancel</button>
                </div>
              </div>
            </div>

        </div>
        </fieldset>
      </form>

<?php } include_once 'footer.php';
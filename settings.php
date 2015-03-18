<?php
require_once "utils/functions.php";

if( isLogged() && !isNew($_SESSION['uid']) ) {
  $profile = getProfile($_SESSION['uid']);
  $chara_class = getClass($_SESSION['uid']);
  if (isset($_POST['submit'])) {

    $setting_oldpass = cleanPOST($_POST['setting_oldpass']);
    $setting_name = cleanPOST($_POST['setting_name']);
    $setting_newpass = cleanPOST($_POST['setting_newpass']);
    $setting_newpass2 = cleanPOST($_POST['setting_newpass2']);
    $setting_class = cleanPOST($_POST['setting_class']);

    if ( empty($setting_oldpass) ) {
      header('Location: settings.php?incorrectpass');
      exit();
    }
    if ( !empty($setting_name) ) {
      updateName($_SESSION['uid'], $setting_oldpass, $setting_name);
    }
    if ( !empty($setting_class) ) {
      updateClass($_SESSION['uid'], $setting_oldpass, intval($setting_class));
    }
    if ( !empty($setting_newpass) ) {
      if( $setting_newpass != $setting_newpass2 ) {
        header('Location: settings.php?no_match');
        exit();
      }
      else
        updatePassword($_SESSION['uid'], $setting_oldpass, $setting_newpass);
    }

    header('Location: profile.php');
    exit();
  }
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

<h1>Settings</h1>

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
    <form class="form-horizontal" role="form" action="settings.php" method="post">
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
              <th>Character Name<?php if ($url == "name_error") echo "<br><span class=\"label label-danger\">Already taken</span>"; ?></th>
              <td>
                <input name="setting_name" type="text" class="form-control" id="inputName" value="<?php echo $profile['character_name'] ?>">
              </td>
            </tr>
            <tr>
              <th>Class</th>
              <td>
                <?php $class_list = getClassList(); ?>
                <select name="setting_class" class="form-control">
                  <?php foreach ($class_list as $c): ?>
                  <option <?php if ($chara_class == $c[type]) echo "selected "; ?>value="<?php echo $c[cid]; ?>"><?php echo $c[type]; ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
            </tr>
            <tr>
              <th>Current Password<?php if ($url == "incorrectpass") echo "<br><span class=\"label label-danger\">Cannot be empty</span>"; ?></th>
              <td>
                <input name="setting_oldpass" type="password" class="form-control" id="inputOldPass" placeholder="Required to make any changes">
              </td>
            </tr>
            <tr>
              <th>New Password<?php if ($url == "no_match") echo "<br><span class=\"label label-danger\">Passwords must match</span>"; ?></th>
              <td>
                <p><input name="setting_newpass" type="password" class="form-control" id="inputNewPass" placeholder="Must be at least 8 characters">
                </p><p>
                <input name="setting_newpass2" type="password" class="form-control" id="inputNewPass" placeholder="New Password again">
                </p>
              </td>
            </tr>
            <tr>
              <th></th>
              <td>
                <button type="submit" name="submit" class="btn btn-success">Update Profile</button> 
                <button type="reset" class="btn btn-default">Cancel</button> 
              </td>
            </tr>
          </tbody>
        </table>
    </div>
    </form>
  </div> <!-- end .row -->
</div> <!-- end .container-fluid -->


<?php } // end isLogged()
else { 
  header('Location: index.php');
  exit();
}
include_once("footer.php"); ?>
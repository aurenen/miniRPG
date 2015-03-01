<?php
session_start();
session_regenerate_id(true);

require_once 'config.php';
require_once 'PasswordHash.php';

$db; // global mysqli connection
function db_connect() {
    global $db, $db_host, $db_user, $db_pass, $db_name;
    $db = new mysqli($db_host, $db_user, $db_pass, $db_name, 3306);

    if($db->connect_errno > 0) {
        die('Unable to connect to database [' . $db->connect_error . ']');
    }
}

function fail($pub, $pvt = '') {
    $debug = true;
    $msg = $pub;
    if ($debug && $pvt !== '')
        $msg .= ": $pvt";
/* The $pvt debugging messages may contain characters that would need to be
 * quoted if we were producing HTML output, like we would be in a real app,
 * but we're using text/plain here.  Also, $debug is meant to be disabled on
 * a "production install" to avoid leaking server setup details. */
    exit("An error occurred ($msg).\n");
}

function get_post_var($var) {
    $val = $_POST[$var];
    if (get_magic_quotes_gpc())
        $val = stripslashes($val);
    return $val;
}

function cleanPOST($form) {
    return htmlspecialchars($form);
}
function cleanSQL($val) {
    global $db;
    return $db->real_escape_string($val);
}

/*****************************************
    User and Database Functions
*****************************************/

function registerAccount($email, $pass, $chara, $gender) {

    global $db;
    db_connect();

    // clean up input
    $email = cleanSQL($email);
    $pass = cleanSQL($pass);
    $chara = cleanSQL($chara);
    $gender = cleanSQL($gender);

    // check if email is real
    if (!preg_match('/^[a-zA-Z0-9_.@-]{1,60}$/', $email)) { 
        header('Location: register.php?invalid_email');
        exit();
    }
    // check password length, bcrypt only uses the first 72 characters
    if (strlen($pass) > 72) { 
        header('Location: register.php?invalid_password');
        exit();
    }

    // check if email already registered
    $sql = "SELECT * FROM Users WHERE email=?";

    $stmt = $db->prepare($sql);
    $stmt->bind_param('s', $email);
    if ($stmt->execute()) { 
        header('Location: register.php?error');
        exit();
    }

    // hash password before inserting into db
    $hasher = new PasswordHash(8, FALSE);
    $hash = $hasher->HashPassword($pass);
    if (strlen($hash) < 20)
        fail('Failed to hash new password');
    unset($hasher);

    $one = 1;
    $zero = 0;
    $null = NULL;

    $stmt = $db->prepare('
        INSERT INTO Users (email, password, level, character_name, character_class, gender, stat_id, money, location)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');

    if (!$stmt) 
        fail('MySQL prepare', $db->error);
    if (!$stmt->bind_param('ssisisiii', $email, $hash, $one, $chara, $zero, $gender, $null, $zero, $zero))
        fail('MySQL bind_param', $db->error);
    if (!$stmt->execute()) {
    /* Figure out why this failed - maybe the username is already taken?
     * It could be more reliable/portable to issue a SELECT query here.  We would
     * definitely need to do that (or at least include code to do it) if we were
     * supporting multiple kinds of database backends, not just MySQL.  However,
     * the prepared statements interface we're using is MySQL-specific anyway. */
        if ($db->errno === 1062 /* ER_DUP_ENTRY */) {
            header('Location: register.php?error');
            exit();
        }
        else
            fail('MySQL execute', $db->error);
    }

    // $sql = "INSERT INTO Users (email, password, level, character_name, character_class, gender, stat_id, money, location)
    //   VALUES ('$email','$pass','1','$chara','novice','$gender',NULL,'0','0')";

    // $go = $db->query($sql);
    // if ($go === false) { 
    //     die('Error: ' . $db->connect_error); 
    // }
    else {
        header('Location: login.php?regsuccess');
        exit();
    }

    $stmt->close();

    db_disconnect();
}


function login($user, $pass) {
    global $db;
    db_connect();

    $user = cleanSQL($user);
    $pass = cleanSQL($pass);

    // check if email is real
    if (!preg_match('/^[a-zA-Z0-9_.@-]{1,60}$/', $user)) { 
        header('Location: register.php?invalid_email');
        exit();
    }
    // check password length, bcrypt only uses the first 72 characters
    if (strlen($pass) > 72) { 
        header('Location: register.php?invalid_password');
        exit();
    }

    // check if email exists
    $sql = "SELECT * FROM Users WHERE email=?";

    $stmt = $db->prepare($sql);
    $stmt->bind_param('s', $email);
    if (!$stmt->execute()) { 
        header('Location: login.php?error');
        exit();
    }

    /*
    if($rs->num_rows == 0) {  // User not found   
        header('Location: login.php?failed');
        exit();
    }
     */
     
    //$result = mysql_fetch_array($rs, MYSQL_ASSOC);
    $result = mysql_fetch_array($rs);
     
    if($pass != $result['u_password']) { // Incorrect password. So, redirect to login_form again.
        header('Location: login.php?error');
        exit();
    }
    else { // Redirect to home page after successful login.
        $_SESSION['active'] = true;
        $_SESSION['user_id'] = $result['u_id'];
        $_SESSION['user_name'] = $result['u_charactername'];
        $_SESSION['user_email'] = $result['u_email'];
    $_SESSION['user_class'] = $result['u_class'];

    //DEBUG
    //print_r($_SESSION);

        // setCookieInfo();
        // redirectHome();
    }
}


function db_disconnect() {
    global $db;
	$db->close();
}
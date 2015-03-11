<?php
session_start();
session_regenerate_id(true);

require_once 'config.php';
require_once 'PasswordHash.php';

/*****************************************
    Database Related Functions
*****************************************/

$db; // global mysqli connection object
$profile = array(); // global profile array
function db_connect() {
    global $db, $db_host, $db_user, $db_pass, $db_name;
    $db = new mysqli($db_host, $db_user, $db_pass, $db_name, 3306);

    if($db->connect_errno > 0) {
        die('Unable to connect to database [' . $db->connect_error . ']');
    }
}

function db_disconnect() {
    global $db;
    $db->close();
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

/*****************************************
    Misc Functions
*****************************************/

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
    User Account Functions
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

    // mysqli_report(MYSQLI_REPORT_ALL);
    
    // check if email already registered
    $sql = "SELECT * FROM users WHERE email=?";

    $stmt = $db->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    if ($stmt->num_rows != 0) { 
        header('Location: register.php?error');
        exit();
    }
    $stmt->close();

    // hash password before inserting into db
    $hasher = new PasswordHash(8, FALSE);
    $hash = $hasher->HashPassword($pass);
    if (strlen($hash) < 20)
        fail('Failed to hash new password');
    unset($hasher);

    $one = 1;
    $zero = 0;

    $sql = "INSERT INTO users (email, password, level, character_name, character_class, gender, money, location, new)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $db->prepare($sql);

    if (!$stmt) 
        fail('MySQL registration prepare', $db->error);
    if (!$stmt->bind_param('ssisisiii', $email, $hash, $one, $chara, $zero, $gender, $zero, $zero, $one))
        fail('MySQL registration bind_param', $db->error);
    if (!$stmt->execute()) {
    /* Figure out why this failed - maybe the username is already taken?
     * It could be more reliable/portable to issue a SELECT query here.  We would
     * definitely need to do that (or at least include code to do it) if we were
     * supporting multiple kinds of database backends, not just MySQL.  However,
     * the prepared statements interface we're using is MySQL-specific anyway. */
        $stmt->close();
        db_disconnect();

        if ($db->errno === 1062 /* ER_DUP_ENTRY */) {
            header('Location: register.php?error');
            exit();
        }
        else
            fail('MySQL registration execute', $db->error);
    }

    // $sql = "INSERT INTO Users (email, password, level, character_name, character_class, gender, stat_id, money, location)
    //   VALUES ('$email','$pass','1','$chara','novice','$gender',NULL,'0','0')";

    // $go = $db->query($sql);
    // if ($go === false) { 
    //     die('Error: ' . $db->connect_error); 
    // }
    else {
        $stmt->close();
        db_disconnect();
        header('Location: login.php?regsuccess');
        exit();
    }
}


function login($user, $pass) {
    global $db, $profile;
    db_connect();

    $user = cleanSQL($user);
    $pass = cleanSQL($pass);

    // check if email is real
    if (!preg_match('/^[a-zA-Z0-9_.@-]{1,60}$/', $user)) { 
        header('Location: login.php?error');
        exit();
    }
    // check password length, bcrypt only uses the first 72 characters
    if (strlen($pass) > 72) { 
        header('Location: login.php?error');
        exit();
    }

    // check if email exists
    $sql = "SELECT * FROM users WHERE email=?";

    $stmt = $db->prepare($sql);
    $stmt->bind_param('s', $user);

    $stmt->execute();
    $result = $stmt->get_result();
    $result->fetch_row();

    if ($result->num_rows === 0) { 
        header('Location: login.php?failed');
        exit();
    }
    $stmt->close();

    // hash password before inserting into db
    $hasher = new PasswordHash(8, FALSE);

    $sql = "SELECT password, uid FROM users WHERE email=?";

    // mysqli_report(MYSQLI_REPORT_ALL);

    $hash = '*'; // In case the user is not found

    $stmt = $db->prepare($sql);
    if (!$stmt) 
        fail('MySQL login prepare', $stmt->error);
    if (!$stmt->bind_param('s', $user))
        fail('MySQL login bind_param', $stmt->error);
    if (!$stmt->execute())
        fail('MySQL login execute', $stmt->error);
    if (!$stmt->bind_result($hash, $uid))
        fail('MySQL login bind_result', $stmt->error);
    if (!$stmt->fetch() && $stmt->errno)
        fail('MySQL login fetch', $stmt->error);

    if ($hasher->CheckPassword($pass, $hash)) { // Redirect to home page after successful login.
        $_SESSION['active'] = true;
        $_SESSION['uid'] = $uid;
        setcookie('uid', $_SESSION['uid']);
        // $_SESSION['user_name'] = $result['u_charactername'];
        // $_SESSION['user_email'] = $result['u_email'];
        // $_SESSION['user_class'] = $result['u_class'];

        unset($hasher);
        $stmt->close();
        db_disconnect();
        header('Location: index.php');
        // call a function to query for all user info based on $uid
        exit();
    } 
    else { // Incorrect password. So, redirect to login_form again.
        unset($hasher);
        $stmt->close();
        db_disconnect();
        header('Location: login.php?error');
        exit();
    }

    //DEBUG
    //print_r($_SESSION);

        // setCookieInfo();
        // redirectHome();
}

function getCharacterName($uid) {
    global $db;
    db_connect();

    // check if uid exists
    // $sql = "SELECT * FROM users WHERE uid=?";

    // $stmt = $db->prepare($sql);
    // $stmt->bind_param('i', $uid);

    // $stmt->execute();
    // $result = $stmt->get_result();
    // $result->fetch_row();

    // if ($result->num_rows === 0) { 
    //     header('Location: profile.php?uid=failed');
    //     exit();
    // }
    // $stmt->close();

    $sql = "SELECT character_name FROM users WHERE uid=?";

    $stmt = $db->prepare($sql);

    if (!$stmt) 
        fail('MySQL getCharacterName prepare', $stmt->error);
    if (!$stmt->bind_param('i', $uid))
        fail('MySQL getCharacterName bind_param', $stmt->error);
    if (!$stmt->execute())
        fail('MySQL getCharacterName execute', $stmt->error);
    if (!$stmt->bind_result($name))
        fail('MySQL getCharacterName bind_result', $stmt->error);
    if (!$stmt->fetch() && $stmt->errno)
        fail('MySQL getCharacterName fetch', $stmt->error);

    $result = $stmt->get_result();
    if ($result->num_rows === 0) { 
        db_disconnect();
        header('Location: login.php?failed');
        exit();
    }
    db_disconnect();
    return $name;
}

function getProfile($uid) {
    global $db;
    db_connect();

    // check if uid exists
    // $sql = "SELECT * FROM users WHERE uid=?";

    // $stmt = $db->prepare($sql);
    // $stmt->bind_param('i', $uid);

    // $stmt->execute();
    // $result = $stmt->get_result();
    // $result->fetch_row();

    // if ($result->num_rows === 0) { 
    //     header('Location: profile.php?uid=failed');
    //     exit();
    // }
    // $stmt->close();

    $sql = "SELECT * FROM users WHERE uid=?";

    $stmt = $db->prepare($sql);

    if (!$stmt) 
        fail('MySQL getProfile prepare', $stmt->error);
    if (!$stmt->bind_param('i', $uid))
        fail('MySQL getProfile bind_param', $stmt->error);
    if (!$stmt->execute())
        fail('MySQL getProfile execute', $stmt->error);

    $result = $stmt->get_result();
    // $row = $result->fetch_array(MYSQLI_ASSOC);
    $row = $result->fetch_assoc();
    $result->free();
    db_disconnect();
    return $row;
}

function getStats($uid) {
    global $db;
    db_connect();

    $sql = "SELECT exp, hp, sp, str, vit, dex, agi, cun, wis FROM users, stats WHERE id=?";

    $stmt = $db->prepare($sql);

    if (!$stmt) 
        fail('MySQL getStats prepare', $stmt->error);
    if (!$stmt->bind_param('i', $uid))
        fail('MySQL getStats bind_param', $stmt->error);
    if (!$stmt->execute())
        fail('MySQL getStats execute', $stmt->error);

    $result = $stmt->get_result();
    // $row = $result->fetch_array(MYSQLI_ASSOC);
    $row = $result->fetch_assoc();
    $result->free();
    db_disconnect();
    return $row;
}

function isNew($uid) {
    global $db;
    db_connect();
    // if new = true, make them select a class
    // else they can't select class
    $sql = "SELECT new FROM users WHERE uid=?";

    $stmt = $db->prepare($sql);

    if (!$stmt) 
        fail('MySQL isNew prepare', $stmt->error);
    if (!$stmt->bind_param('i', $uid))
        fail('MySQL isNew bind_param', $stmt->error);
    if (!$stmt->execute())
        fail('MySQL isNew execute', $stmt->error);
    if (!$stmt->bind_result($flag))
        fail('MySQL isNew bind_result', $stmt->error);
    if (!$stmt->fetch() && $stmt->errno)
        fail('MySQL isNew fetch', $stmt->error);

    $result = $stmt->get_result();
    if ($result->num_rows === 0) { 
        db_disconnect();
        header('Location: login.php?failed');
        exit();
    }
    db_disconnect();
    return $flag;
}

function setClass($uid, $type) {
    global $db;
    db_connect();

    $sql = "UPDATE users SET character_class=?, new=? WHERE uid=?";

    $stmt = $db->prepare($sql);

    $new = 0;

    if (!$stmt) 
        fail('MySQL setClass prepare', $db->error);
    if (!$stmt->bind_param('iii', $type, $new, $uid))
        fail('MySQL setClass bind_param', $db->error);
    if (!$stmt->execute()) {
    /* Figure out why this failed - maybe the username is already taken?
     * It could be more reliable/portable to issue a SELECT query here.  We would
     * definitely need to do that (or at least include code to do it) if we were
     * supporting multiple kinds of database backends, not just MySQL.  However,
     * the prepared statements interface we're using is MySQL-specific anyway. */
        $stmt->close();

        fail('MySQL registration execute', $db->error);
        db_disconnect();

        header('Location: selectclass.php?error');
        exit();
    }
    else {
        $stmt->close();
        db_disconnect();
        header('Location: profile.php?setclass');
        exit();
    }
}

function getClass($uid) {
    global $db;
    db_connect();

    $sql = "SELECT classes.type FROM classes LEFT JOIN users ON classes.cid = users.character_class WHERE users.uid = ?";

    $stmt = $db->prepare($sql);

    if (!$stmt) 
        fail('MySQL getStats prepare', $stmt->error);
    if (!$stmt->bind_param('i', $uid))
        fail('MySQL getStats bind_param', $stmt->error);
    if (!$stmt->execute())
        fail('MySQL getStats execute', $stmt->error);
    if (!$stmt->bind_result($class_name))
        fail('MySQL getCharacterName bind_result', $stmt->error);
    if (!$stmt->fetch() && $stmt->errno)
        fail('MySQL getCharacterName fetch', $stmt->error);

    $result = $stmt->get_result();
    if ($result->num_rows === 0) { 
        db_disconnect();
        header('Location: login.php?failed');
        exit();
    }
    db_disconnect();
    return $class_name;
}


/*****************************************
    Session Functions
*****************************************/

function isLogged() {
    if (isset($_SESSION['active']) && $_SESSION['active']===true) return true; 
    else return false;
}

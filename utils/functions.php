<?php
session_start();
session_regenerate_id(true);

require_once 'config.php';
require_once 'PasswordHash.php';

ini_set('display_errors',1);  
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_STRICT);

/*****************************************
    Database Related Functions
*****************************************/

$db; // global mysqli connection object
$profile = array(); // global profile array
$isBattle = false;
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
    Monster Functions
*****************************************/

function createMonster() {
    global $db;
    db_connect();

    // read from a file and insert values into monster table
    $fp = fopen('flat-file-data.txt','r');
    if (!$fp) {echo 'ERROR: Unable to open file.'; exit;}
    $loop = 0;
    while (!feof($fp)) {
        $loop++;
        $line = fgets($fp, 1024); //use 2048 if very long lines
        $field[$loop] = explode ('|', $line);

        $fp++;
    }
    fclose($fp);

    $hp; $sp; $str; $vit; $dex; $agi; $cun; $wis; $exp = 50;
    // stats start out from a total of 30 points (avg of 5 pts * 6 areas)

    for ($mid=1; $mid < 11; $mid++) { 
        
        $hp = 45 + rand(1, 15);
        $sp = 20 + rand(1, 10);
        $str = 1 + rand(1, 9);
        $vit = 1 + rand(1, 9);
        $dex = 1 + rand(1, 9);
        $agi = 1 + rand(1, 9);
        $cun = 1 + rand(1, 9);
        $wis = 1 + rand(1, 9);

        $sql = "INSERT INTO monster_stats (id,exp,hp,sp,str,vit,dex,agi,cun,wis) VALUES (?,?,?,?,?,?,?,?,?,?)";

        $stmt = $db->prepare($sql);

        if (!$stmt) 
            fail('MySQL createMonster prepare', $db->error);
        if (!$stmt->bind_param('iiiiiiiiii', $mid, $exp, $hp, $sp, $str, $vit, $dex, $agi, $cun, $wis))
            fail('MySQL createMonster bind_param', $db->error);
        if (!$stmt->execute()) {
        /* Figure out why this failed - maybe the username is already taken?
         * It could be more reliable/portable to issue a SELECT query here.  We would
         * definitely need to do that (or at least include code to do it) if we were
         * supporting multiple kinds of database backends, not just MySQL.  However,
         * the prepared statements interface we're using is MySQL-specific anyway. */
            $stmt->close();

            fail('MySQL createMonster execute', $db->error);
            db_disconnect();

            // header('Location: selectclass.php?error');
            exit();
        }
        else { // success!!
            $stmt->close();
            db_disconnect();
        }
    } // end for
}


/*****************************************
    Battle Functions
*****************************************/

function gainExp($uid) {
    $old = getExp($uid);
    $level = getLevel($uid);

    global $db;
    db_connect();

    $exp = ceil($level * 0.65 * 2.5 + rand(5, 15) * 1.4) + $old;

    if ($exp >= getMaxExp($level)) {
        // level up and recalculate exp
        $nlvl = $level + 1;

        $sql = "UPDATE users SET level=? WHERE uid=?";

        $stmt = $db->prepare($sql);

        if (!$stmt) 
            fail('MySQL levelUp prepare', $db->error);
        if (!$stmt->bind_param('ii', $nlvl, $uid))
            fail('MySQL levelUp bind_param', $db->error);
        if (!$stmt->execute()) {
            $stmt->close();

            fail('MySQL levelUp execute', $db->error);
            db_disconnect();

            header('Location: index.php?error');
            exit();
        }
        else {
            $stmt->close();
            $exp = $exp - getMaxExp($level);
        }
    }

    $sql = "UPDATE stats SET exp=? WHERE id=?";

    $stmt = $db->prepare($sql);

    if (!$stmt) 
        fail('MySQL gainExp prepare', $stmt->error);
    if (!$stmt->bind_param('ii', $exp, $uid))
        fail('MySQL gainExp bind_param', $db->error);
    if (!$stmt->execute()) {
        $stmt->close();

        fail('MySQL gainExp execute', $db->error);
        db_disconnect();

        header('Location: index.php?error');
        exit();
    }
    else {
        $stmt->close();
        db_disconnect();
        header('Location: battle.php?exp');
        exit();
    }
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
    $sql = "SELECT uid FROM users WHERE email=?";

    $stmt = $db->prepare($sql);
    $stmt->bind_param('s', $user);

    $stmt->execute();
    // $result = $stmt->get_result();
    // $result->fetch_row();
    $stmt->bind_result($result);
    $stmt->fetch();

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

    // $result = $stmt->get_result();
    $stmt->bind_result($result);
    $stmt->fetch();

    db_disconnect();
    return $name;
}

function getLevel($uid) {
    global $db;
    db_connect();

    $sql = "SELECT level FROM users WHERE uid=?";

    $stmt = $db->prepare($sql);

    if (!$stmt) 
        fail('MySQL getLevel prepare', $stmt->error);
    if (!$stmt->bind_param('i', $uid))
        fail('MySQL getLevel bind_param', $stmt->error);
    if (!$stmt->execute())
        fail('MySQL getLevel execute', $stmt->error);
    if (!$stmt->bind_result($lvl))
        fail('MySQL getLevel bind_result', $stmt->error);
    if (!$stmt->fetch() && $stmt->errno)
        fail('MySQL getLevel fetch', $stmt->error);

    // $result = $stmt->get_result();
    $stmt->bind_result($result);
    $stmt->fetch();

    db_disconnect();
    return $lvl;
}

function getExp($uid) {
    global $db;
    db_connect();

    $sql = "SELECT exp FROM stats WHERE id=?";

    $stmt = $db->prepare($sql);

    if (!$stmt) 
        fail('MySQL getExp prepare', $stmt->error);
    if (!$stmt->bind_param('i', $uid))
        fail('MySQL getExp bind_param', $stmt->error);
    if (!$stmt->execute())
        fail('MySQL getExp execute', $stmt->error);
    if (!$stmt->bind_result($exp))
        fail('MySQL getExp bind_result', $stmt->error);
    if (!$stmt->fetch() && $stmt->errno)
        fail('MySQL getExp fetch', $stmt->error);

    // $result = $stmt->get_result();
    $stmt->bind_result($result);
    $stmt->fetch();

    db_disconnect();
    return $exp;
}

function getMaxExp($level) {
    return floor(hypot($level * 15,$level * 20) * 2.1);
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

    // $result = $stmt->get_result();
    // $stmt->bind_result($result);
    // $stmt->fetch();
    // $row = $result->fetch_assoc();
    // $result->free();
    $meta = $stmt->result_metadata(); 

    while ($field = $meta->fetch_field()) { 
        $params[] = &$row[$field->name]; 
    } 

    call_user_func_array(array($stmt, 'bind_result'), $params);            
    while ($stmt->fetch()) { 
        foreach($row as $key => $val) { 
            $c[$key] = $val; 
        } 
        $profile_list = $c; 
    } 
    $stmt->close(); 

    db_disconnect();
    return $profile_list;
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

    // $result = $stmt->get_result();
    // $stmt->bind_result($result);
    // $stmt->fetch();
    // $row = $result->fetch_assoc();
    // $result->free();

    $meta = $stmt->result_metadata(); 

    while ($field = $meta->fetch_field()) { 
        $params[] = &$row[$field->name]; 
    } 

    call_user_func_array(array($stmt, 'bind_result'), $params);            
    while ($stmt->fetch()) { 
        foreach($row as $key => $val) { 
            $c[$key] = $val; 
        } 
        $stat_list = $c; 
    } 
    $stmt->close(); 

    db_disconnect();
    return $stat_list;
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

    // $result = $stmt->get_result();
    $stmt->bind_result($result);
    $stmt->fetch();

    db_disconnect();
    return $flag;
}

function setStats($uid, $type) {
    global $db;
    // no need to call db_connect() or db_disconnect() here 
    // because this is called within the setClass(), which 
    // connects and disconnects

    define("WAR", 1);
    define("ENC", 2);
    define("RAN", 3);
    define("TEM", 4);
    define("MYS", 5);
    define("ROG", 6);

    $hp; $sp; $str; $vit; $dex; $agi; $cun; $wis; $exp = 0;
    // stats start out from a total of 30 points (avg of 5 pts * 6 areas)
    switch ($type) {
        case WAR:
            $hp = 45;
            $sp = 15;
            $str = 9;
            $vit = 8;
            $dex = 5;
            $agi = 3;
            $cun = 3;
            $wis = 2;
            break;
        
        case ENC:
            $hp = 25;
            $sp = 55;
            $str = 1;
            $vit = 2;
            $dex = 9;
            $agi = 2;
            $cun = 6;
            $wis = 10;
            break;
        
        case RAN:
            $hp = 35;
            $sp = 25;
            $str = 2;
            $vit = 4;
            $dex = 10;
            $agi = 8;
            $cun = 4;
            $wis = 2;
            break;
        
        case TEM:
            $hp = 55;
            $sp = 30;
            $str = 7;
            $vit = 9;
            $dex = 4;
            $agi = 2;
            $cun = 2;
            $wis = 6;
            break;
        
        case MYS:
            $hp = 50;
            $sp = 50;
            $str = 1;
            $vit = 10;
            $dex = 6;
            $agi = 2;
            $cun = 2;
            $wis = 9;
            break;
        
        case ROG:
            $hp = 30;
            $sp = 25;
            $str = 2;
            $vit = 3;
            $dex = 5;
            $agi = 10;
            $cun = 8;
            $wis = 2;
            break;
        
        default:
            # code...
            break;
    }

    $sql = "INSERT INTO stats (id,exp,hp,sp,str,vit,dex,agi,cun,wis) VALUES (?,?,?,?,?,?,?,?,?,?)";

    $stmt = $db->prepare($sql);

    if (!$stmt) 
        fail('MySQL setStats prepare', $db->error);
    if (!$stmt->bind_param('iiiiiiiiii', $uid, $exp, $hp, $sp, $str, $vit, $dex, $agi, $cun, $wis))
        fail('MySQL setStats bind_param', $db->error);
    if (!$stmt->execute()) {
    /* Figure out why this failed - maybe the username is already taken?
     * It could be more reliable/portable to issue a SELECT query here.  We would
     * definitely need to do that (or at least include code to do it) if we were
     * supporting multiple kinds of database backends, not just MySQL.  However,
     * the prepared statements interface we're using is MySQL-specific anyway. */
        $stmt->close();

        fail('MySQL setStats execute', $db->error);
        db_disconnect();

        header('Location: selectclass.php?error');
        exit();
    }
    else {
        $stmt->close();
    }
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

        fail('MySQL setClass execute', $db->error);
        db_disconnect();

        header('Location: selectclass.php?error');
        exit();
    }
    else {
        $stmt->close();
        // call set stats function
        setStats($uid, $type);
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
        fail('MySQL getClass prepare', $stmt->error);
    if (!$stmt->bind_param('i', $uid))
        fail('MySQL getClass bind_param', $stmt->error);
    if (!$stmt->execute())
        fail('MySQL getClass execute', $stmt->error);
    if (!$stmt->bind_result($class_name))
        fail('MySQL getClass bind_result', $stmt->error);
    if (!$stmt->fetch() && $stmt->errno)
        fail('MySQL getClass fetch', $stmt->error);

    // $result = $stmt->get_result();
    $stmt->bind_result($result);
    $stmt->fetch();

    db_disconnect();
    return $class_name;
}

function bind_array($stmt, &$row) {
    $md = $stmt->result_metadata();
    $params = array();
    while($field = $md->fetch_field()) {
        $params[] = &$row[$field->name];
    }

    call_user_func_array(array($stmt, 'bind_result'), $params);
}
function getClassList() {
    global $db;
    db_connect();

    $sql = "SELECT cid,type FROM classes";

    $stmt = $db->prepare($sql);

    if (!$stmt) 
        fail('MySQL getClassList prepare', $stmt->error);
    if (!$stmt->execute())
        fail('MySQL getClassList execute', $stmt->error);

    // $result = $stmt->get_result();
    // $stmt->bind_result($result);
    // $stmt->fetch();

    $meta = $stmt->result_metadata(); 

    while ($field = $meta->fetch_field()) { 
        $params[] = &$row[$field->name]; 
    } 

    call_user_func_array(array($stmt, 'bind_result'), $params);            
    while ($stmt->fetch()) { 
        foreach($row as $key => $val) { 
            $c[$key] = $val; 
        } 
        $class_list[] = $c; 
    } 
    $stmt->close(); 

    // $class_list = array();
    // $class_list = $hits;
    // while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
    //     array_push($class_list, $row);
    // }

    // $result->free();
    db_disconnect();
    return $class_list;
}

function classQuiz($score) {
    if($score < 10) {
        return "warrior";
    }
    else if($score < 15) {
        return "templar";
    }
    else if($score < 20) {
        return "enchanter";
    }
    else if($score < 25) {
        return "mystic";
    }
    else if($score < 30) {
        return "rogue";
    }
    else {
        return "ranger";
    }
}

function updateName($uid, $pass, $name) {
    global $db;
    db_connect();
    
    $name = cleanSQL($name);
    $pass = cleanSQL($pass);

    // check password length, bcrypt only uses the first 72 characters
    if (strlen($pass) > 72) { 
        header('Location: settings.php?incorrectpass');
        exit();
    }

    // hash password before inserting into db
    $hasher = new PasswordHash(8, FALSE);

    $sql = "SELECT password FROM users WHERE uid=?";

    // mysqli_report(MYSQLI_REPORT_ALL);

    $hash = '*'; // In case the user is not found

    $stmt = $db->prepare($sql);
    if (!$stmt) 
        fail('MySQL updateName prepare', $stmt->error);
    if (!$stmt->bind_param('s', $uid))
        fail('MySQL updateName bind_param', $stmt->error);
    if (!$stmt->execute())
        fail('MySQL updateName execute', $stmt->error);
    if (!$stmt->bind_result($hash))
        fail('MySQL updateName bind_result', $stmt->error);
    if (!$stmt->fetch() && $stmt->errno)
        fail('MySQL updateName fetch', $stmt->error);

    if ($hasher->CheckPassword($pass, $hash)) { // Redirect to home page after successful login.
        unset($hasher);
        $stmt->close();

        $sql = "UPDATE users SET character_name=? WHERE uid=?";
        $stmt = $db->prepare($sql);

        if (!$stmt) 
            fail('MySQL updateName prepare', $db->error);
        if (!$stmt->bind_param('si', $name, $uid))
            fail('MySQL updateName bind_param', $db->error);
        if (!$stmt->execute()) {
        /* Figure out why this failed - maybe the username is already taken?
         * It could be more reliable/portable to issue a SELECT query here.  We would
         * definitely need to do that (or at least include code to do it) if we were
         * supporting multiple kinds of database backends, not just MySQL.  However,
         * the prepared statements interface we're using is MySQL-specific anyway. */
            $stmt->close();

            fail('MySQL setClass execute', $db->error);
            db_disconnect();

            header('Location: settings.php?name_error');
            exit();
        }
        else { // success
            $stmt->close();
            db_disconnect();
        }
    } 
    else { // Incorrect password. So, redirect to login_form again.
        unset($hasher);
        $stmt->close();
        db_disconnect();
        header('Location: settings.php?incorrectpass');
        exit();
    }
}

function updateClass($uid, $pass, $class) {
    global $db;
    db_connect();
    
    $class = cleanSQL($class);
    $pass = cleanSQL($pass);

    // check password length, bcrypt only uses the first 72 characters
    if (strlen($pass) > 72) { 
        header('Location: settings.php?incorrectpass');
        exit();
    }

    // hash password before inserting into db
    $hasher = new PasswordHash(8, FALSE);

    $sql = "SELECT password FROM users WHERE uid=?";

    // mysqli_report(MYSQLI_REPORT_ALL);

    $hash = '*'; // In case the user is not found

    $stmt = $db->prepare($sql);
    if (!$stmt) 
        fail('MySQL updateName prepare', $stmt->error);
    if (!$stmt->bind_param('s', $uid))
        fail('MySQL updateName bind_param', $stmt->error);
    if (!$stmt->execute())
        fail('MySQL updateName execute', $stmt->error);
    if (!$stmt->bind_result($hash))
        fail('MySQL updateName bind_result', $stmt->error);
    if (!$stmt->fetch() && $stmt->errno)
        fail('MySQL updateName fetch', $stmt->error);

    if ($hasher->CheckPassword($pass, $hash)) { // Redirect to home page after successful login.
        unset($hasher);
        $stmt->close();

        $sql = "UPDATE users SET character_class=? WHERE uid=?";
        $stmt = $db->prepare($sql);

        if (!$stmt) 
            fail('MySQL updateName prepare', $db->error);
        if (!$stmt->bind_param('ii', $class, $uid))
            fail('MySQL updateName bind_param', $db->error);
        if (!$stmt->execute()) {
        /* Figure out why this failed - maybe the username is already taken?
         * It could be more reliable/portable to issue a SELECT query here.  We would
         * definitely need to do that (or at least include code to do it) if we were
         * supporting multiple kinds of database backends, not just MySQL.  However,
         * the prepared statements interface we're using is MySQL-specific anyway. */
            $stmt->close();

            fail('MySQL setClass execute', $db->error);
            db_disconnect();

            header('Location: settings.php?class_error');
            exit();
        }
        else { // success
            $stmt->close();
            db_disconnect();
        }
    } 
    else { // Incorrect password. So, redirect to login_form again.
        unset($hasher);
        $stmt->close();
        db_disconnect();
        header('Location: settings.php?incorrectpass');
        exit();
    }
}

function updatePassword($uid, $pass, $newpass) {
    global $db;
    db_connect();
    
    $newpass = cleanSQL($newpass);
    $pass = cleanSQL($pass);

    // check password length, bcrypt only uses the first 72 characters
    if (strlen($pass) > 72) { 
        header('Location: settings.php?incorrectpass');
        exit();
    }

    // hash password before inserting into db
    $hasher = new PasswordHash(8, FALSE);

    $sql = "SELECT password FROM users WHERE uid=?";

    // mysqli_report(MYSQLI_REPORT_ALL);

    $hash = '*'; // In case the user is not found

    $stmt = $db->prepare($sql);
    if (!$stmt) 
        fail('MySQL updateName prepare', $stmt->error);
    if (!$stmt->bind_param('s', $uid))
        fail('MySQL updateName bind_param', $stmt->error);
    if (!$stmt->execute())
        fail('MySQL updateName execute', $stmt->error);
    if (!$stmt->bind_result($hash))
        fail('MySQL updateName bind_result', $stmt->error);
    if (!$stmt->fetch() && $stmt->errno)
        fail('MySQL updateName fetch', $stmt->error);

    if ($hasher->CheckPassword($pass, $hash)) { // Redirect to home page after successful login.
        unset($hasher);
        $stmt->close();

        // hash password before inserting into db
        $hasher = new PasswordHash(8, FALSE);
        $hash = $hasher->HashPassword($newpass);
        if (strlen($hash) < 20)
            fail('Failed to hash new password');
        unset($hasher);


        $sql = "UPDATE users SET password=? WHERE uid=?";
        $stmt = $db->prepare($sql);

        if (!$stmt) 
            fail('MySQL updateName prepare', $db->error);
        if (!$stmt->bind_param('si', $hash, $uid))
            fail('MySQL updateName bind_param', $db->error);
        if (!$stmt->execute()) {
        /* Figure out why this failed - maybe the username is already taken?
         * It could be more reliable/portable to issue a SELECT query here.  We would
         * definitely need to do that (or at least include code to do it) if we were
         * supporting multiple kinds of database backends, not just MySQL.  However,
         * the prepared statements interface we're using is MySQL-specific anyway. */
            $stmt->close();

            fail('MySQL setClass execute', $db->error);
            db_disconnect();

            header('Location: settings.php?class_error');
            exit();
        }
        else { // success
            $stmt->close();
            db_disconnect();
        }
    } 
    else { // Incorrect password. So, redirect to login_form again.
        unset($hasher);
        $stmt->close();
        db_disconnect();
        header('Location: settings.php?incorrectpass');
        exit();
    }
}


/*****************************************
    Session Functions
*****************************************/

function isLogged() {
    if (isset($_SESSION['active']) && $_SESSION['active']===true) return true; 
    else return false;
}

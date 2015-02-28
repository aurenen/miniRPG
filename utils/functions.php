<?php
require_once 'config.php';

function cleanPOST($form) {
    return htmlspecialchars($form);
}
function cleanSQL($val) {
    return mysql_real_escape_string($val);
}
function db_connect() {
	$db = new mysqli($db_host, $db_user, $db_pass, $db_name);

	if($db->connect_errno > 0){
	    die('Unable to connect to database [' . $db->connect_error . ']');
	}
}

/*****************************************
    User and Database Functions
*****************************************/

function registerAccount($email, $pass, $chara, $gender) {

    global $link;
    db_connect();

    // clean up input
    $email = cleanSQL($email);
    $pass = cleanSQL($pass);
    $chara = cleanSQL($chara);
    $gender = cleanSQL($gender);

    // check if email already registered
    $sql = "SELECT * FROM Users WHERE u_email='$email'";

    $go = $db->query($sql);
    if ($go === true) { 
        header('Location: register.php?error');
        exit();
    }
    else { // if no matching email in db
        $sql = "INSERT INTO Users (u_email, u_password, u_charactername, u_class)
          VALUES ('$email','$pass','$chara','default')";

        $go = $db->query($sql);
        if ($go === false) { 
            die('Error: ' . $db->connect_error); 
        }
        else {
            header('Location: login.php?regsuccess');
            exit();
        }
    }

    db_disconnect();
}

function db_disconnect() {
	$db_connect->close();
}
<?php
session_start();// starts the session, needed at top of each script that uses session data

//set session to an empty array
$_SESSION=array();

//delete the cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
};
//destroy  the session
session_destroy();
header("Location: index.php");
exit();
?>
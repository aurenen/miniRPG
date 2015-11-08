<?php
/* ============================================== *
*   Edit the fields below and rename this file    *
*   to "config.php" before using.                 *
*  ============================================== */

// Database username.
$db_user = 'your_username';
// Database password.
$db_pass = 'your_password';
// Database name.
$db_name = 'database_name';
// Database host.
$db_host = 'localhost';

// URL to site, include trailing slash. ('http://example.com/')
$root_url = 'http://example.com/miniRPG/';
// Absolute path to site on server, include trailing slash. (Add trailing slash!)
$proj_path = '/home/account_name/public_html/miniRPG/';
// Domain where cookie will work. ('example.com')
$cookie_domain = 'example.com/miniRPG';
// Path on domain where cookie will work. ('/folder/')
$cookie_path = '/miniRPG/';

session_set_cookie_params(3600, $cookie_path, $cookie_domain, false, true);
date_default_timezone_set('America/Los_Angeles');

$site_title = 'MiniRPG';
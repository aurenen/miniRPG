<?php
// Database username.
$db_user = 'masao';
// Database password.
$db_pass = 'FenriS';
// Database name.
$db_name = 'cs490';
// Database host.
$db_host = 'localhost';

// URL to site. ('http://example.com/')
$root_url = 'http://localhost/~tsukete/cs490/miniRPG/';
// Absolute path to site on server. (Add trailing slash!)
$abso_path = '/Users/tsukete/Sites/cs490/miniRPG/';
// Domain where cookie will work. ('example.com')
$cookie_domain = 'localhost/~tsukete';
// Path on domain where cookie will work. ('/folder/')
$cookie_path = '/cs490/';

$site_title = 'MiniRPG';

// Set timezone
session_set_cookie_params(3600, $cookie_path, $cookie_domain, false, true);
date_default_timezone_set('America/Los_Angeles');

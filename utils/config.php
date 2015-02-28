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
$root_url = '';
// Absolute path to site on server. (Add trailing slash!)
$proj_path = '';
// Domain where cookie will work. ('example.com')
$cookie_domain = '';
// Path on domain where cookie will work. ('/folder/')
$cookie_path = '';

session_set_cookie_params(3600, $cookie_path, $cookie_domain, false, true);
date_default_timezone_set('America/Los_Angeles');

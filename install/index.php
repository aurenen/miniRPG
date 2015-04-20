<?php
require_once '../utils/config.php';

$db = new mysqli($db_host, $db_user, $db_pass, $db_name);

if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
}
else {
	echo "Connection Success!<br />";
}

if (isset($_SERVER['QUERY_STRING'])) {
	$url = $_SERVER['QUERY_STRING'];
	parse_str($url, $vars);
} 
else {
	$url = $_GET;
}

echo "<h2>Installer</h2>\n";
echo "<p><a href='index.php?create'>Create tables</a></p>\n";
echo "<p><a href='index.php?monster'>Create monster tables</a></p>\n";

if ($url == "create") { 
	$create = file_get_contents("create.sql");
	$stmts = explode("--", $create);

	foreach ($stmts as $s) {
	    if (!$db->query($s)) {
	        die('There was an error running the query [' . $db->error . ']');
	    }
	}

	echo "<p>Successfully created tables!</p>";
}if ($url == "monster") { 
	$sql = "CREATE TABLE monster_stats (
	    id INTEGER,
	    exp INTEGER,
	    hp INTEGER,
	    sp INTEGER,
	    str INTEGER,
	    vit INTEGER,
	    dex INTEGER,
	    agi INTEGER,
	    cun INTEGER,
	    wis INTEGER,
	    UNIQUE (id)
	)";

	$go = $db->query($sql);

	    if ($go === false) { 
	        die('Error: ' . $db->connect_error); 
	    }
	    else {
	    	echo "<p>Successfully created monster tables!</p>";
	    }
}


$db->close();
?>
<?php
$gamedb="No Match";
if (preg_match(":gamelib/:", $_SERVER['PHP_SELF']) )
{
	$gamedb = "gamedb"; 
}
else
{
	$path_array = explode("/", $_SERVER['PHP_SELF']);
	foreach($path_array as $sub => $path_piece)
	{
		if (preg_match("/gamelib_[a-zA-Z_]*/", $path_piece))
		{
		       $gamedb = substr($path_piece, strpos($path_piece, "_")+1) . "_games";
		       break;
		}
	}
}
$hold_db = $gamedb;
$gamedb = getenv('DB_ENV') ?: $hold_db;

$ro_login = getenv('DB_RO_USER') ?: "game_query";
$ro_pw = getenv('DB_RO_PASS') ?: "gamero";
$rw_login = getenv('DB_RW_USER') ?: "game_admin";
$rw_pw = getenv('DB_RW_PASS') ?: "gamerw";
$db_host = getenv('DB_ADDRESS') ?: "localhost";
$db_port = getenv('DB_PORT') ?: 3306;
?>

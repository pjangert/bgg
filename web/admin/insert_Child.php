<?php
require_once ("includes/verify.php");
require_once ("includes/dbinfo.php");
$link = new mysqli($db_host, $ro_login, $ro_pw, $gamedb, $db_port);
if ($link->connect_errno)
{
	$_SESSION['ins_error'] = "Could not connect to database";
	$_SESSION['ins_message'] = $link->connect_error;
	header("Location: admin.php");
}
if (!isset($_REQUEST['parent']))
{
	$_SESSION['ins_error'] = "Invalid request sent";
	header("Location: admin.php");
}
foreach($_REQUEST as $rfield => $rvalue) {$$rfield = $rvalue; }
$game_name = urldecode($_REQUEST['game_name']);

$query_parent = "select min(game_id) as parent_id, min_players, max_players from game_info where bgg_id in ({$parent})";
$success = $link->query($query_parent);
$parent_info = $success->fetch_assoc();
if (!$parent_info || $parent_info['parent_id'] == null)
{
	$_SESSION['ins_error'] = "Unable to retrieve parent with ID: ". $parent;
	goto end;
}

//echo "<pre>{${var_dump($parent_info)}} - {$parent_info['bgg_id']}</pre>";
if ($min_players >= $parent_info['min_players']) { $min_players = "NULL"; }
if ($max_players <= $parent_info['max_players']) { $max_players = "NULL"; }
$parent_id = $parent_info['parent_id'];
$success->free();
$query = "insert into expansion_info (parent_id, bgg_id, exp_name, min_over, max_over) values ({$parent_id}, {$bgg_id}, '{$link->escape_string($game_name)}', {$min_players}, {$max_players})";

if (!$link->query($query))
{
	$_SESSION['ins_error'] = "Unable to create record for {$game_name}";
	$_SESSION['ins_message'] = $link->error;
	goto end;
}
else
	$_SESSION['ins_message'] = "Successfully added entry for {$game_name}";
end:
$link->close();
header ("Location: admin.php");
?>

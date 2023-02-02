<?php
require_once ("includes/verify.php");
require_once ("includes/dbinfo.php");
$link = new mysqli($db_host, $ro_login, $ro_pw, $gamedb, $db_port);
unset($this_err);
if ($link->connect_errno)
{
	$this_err = "Could not connect to database<br />" . $link->connect_error;
//	header("Location: admin.php");
}
else if (!isset($_REQUEST['parent']))
{
	$this_err = "Invalid request sent";
}
if (isset($this_err)) { goto end; }
foreach($_REQUEST as $rfield => $rvalue) {$$rfield = $rvalue; }
$game_name = urldecode($_REQUEST['game_name']);

$query_parent = "select min(game_id) as parent_id, min_players, max_players from game_info where bgg_id in ($parent)";
$success = $link->query($query_parent);
if (!$success)
{
	$this_err = "Error processing {$query_parent} <br /> " . $link->error;
}
else
{
	$parent_info = $success->fetch_assoc();
	if ($parent_info['parent_id'] == null)
	{
		$this_err = "Unable to retrieve parent with ID: ". $parent;
		goto end;
	}
	if ($min_players >= $parent_info['min_players']) { $min_players = "NULL"; }
	if ($max_players <= $parent_info['max_players']) { $max_players = "NULL"; }
	$parent_id = $parent_info['parent_id'];
	$success->free();
	$query = "insert into expansion_info (parent_id, bgg_id, exp_name, min_over, max_over) values ({$parent_id}, {$bgg_id}, '{$link->escape_string($game_name)}', {$min_players}, {$max_players})";

	if (!$link->query($query))
	{
		$this_err = "Unable to create record for {$game_name} <br /> " . $link->error;
	}
}
end:
if (isset($this_err)) {echo "Error processing {$game_name}: {$this_err}";}
$link->close();
?>

<?php
require_once ("includes/verify.php");
require_once ("includes/dbinfo.php");
$link = new mysqli($db_host, $ro_login, $ro_pw, $gamedb, $db_port);
if ($link->connect_errno)
{
  $_SESSION['ins_error'] = "Could not connect to database";
  $_SESSION['ins_message'] = $link->connect_error;
  header ("Location: admin.php");
}
foreach($_REQUEST as $rfield => $rvalue) {$$rfield = $rvalue; }

$query = "insert into game_info (bgg_id, game_name, min_players, max_players) values ({$bgg_id}, '{$link->real_escape_string($game_name)}', {$min_players}, {$max_players})";

//$$$$$$$$$
//echo "<pre>". $query ."</pre>";

if (!$link->query($query))
{
  $_SESSION['ins_error'] = "Unable to create record for {$game_name}";
  $_SESSION['ins_message'] = $link->error;
  header ("Location: admin.php");
}
else
  $_SESSION['ins_message'] = "Successfully added entry for {$game_name}";
$link->close();
header ("Location: admin.php");
?>

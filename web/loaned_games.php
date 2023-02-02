<?php
//$DEBUG="Y";
session_start();
require_once("includes/common.php");
require_once("includes/dbinfo.php");
$title = new header_item("title","Games Curently on Loan");
//$query_hdr = new header_item("function", $query_func);
start_page(array($title, $main_style, $nav_style, $query_hdr));
?>
<div class='container'>
<?php include("includes/nav.php"); ?>
<div class='content'>
<h1>Games Currently on Loan</h1>
<div class='result'>
<!--<span class='buffer'>&nbsp;</span><span class='result' id='gameResult'></span> -->
<span class='buffer'>&nbsp;</span><span class='result' id='gameResult'>
<?php
$link = new mysqli($db_host, $ro_login, $ro_pw, $gamedb, $db_port);
if (!$link)
	echo "<p class='error'>Error retriving information: {$link->error}</p>\n";
else
{
	$game_list = $link->query("select game_name, lent_to, 'game' as type from game_info where lent_to is not null or lent_to != '' union select exp_name as game_name, lent_to, 'exp' as type from expansion_info where lent_to is not null and lent_to != ''");
	if (!$game_list)
		echo "<p class='error'>Error retrieving information: {$link->error}</p>\n";
	else
	{
		if ($game_list->num_rows == 0)
			echo "<p class='indented'>No games currently on loan</p>\n";
		else
		{
			echo "<table class='normal'>\n";
			echo "<th>Name</th><th>&nbsp;</th><th>Loaned To</th>\n";
			while($curr_game = $game_list->fetch_assoc())
			echo "<tr>\n<td>{$curr_game['game_name']}</td><td>&nbsp;</td><td>{$curr_game['lent_to']}</td>\n</tr>\n";
			echo "</table>\n";
		}
	}
}
?>
</span>
</div>
</div>
<?php
end_page();
?>

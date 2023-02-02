<?php
session_start();
require_once('includes/common.php');
require_once('includes/dbinfo.php');
$query_func='<script>
function submitPlayers()
{
	workArea=document.getElementById("gameResult");
	players=document.getElementById("player_count").value;
	if (players=="") {workArea.innerHTML="Invalid player count"; return;}
	workArea.innerHTML="Please wait, retrieving results...";
	if (window.XMLHttpRequest){ xmlhttp=new XMLHttpRequest();}
	else { xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); }
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState == 3) {
			workArea.innerHTML="Loading results...";
		}
		else if (xmlhttp.readyState == 4) {
			//document.getElementById("gameResult").innerHTML=xmlhttp.responseText;
			workArea.innerHTML=xmlhttp.responseText;
		}
	}
	xmlhttp.open("GET","game_query.php?players="+players,true);
	xmlhttp.send();
}
</script>';
$title = new header_item("title","Search by Number of Players");
$query_hdr = new header_item("function", $query_func);
start_page(array($title, $main_style, $nav_style, $query_hdr));
$link = new mysqli($db_host, $ro_login, $ro_pw, $gamedb, $db_port);
if (!$link)
{
        $db_games = "could not be retrieved: {$link->error}";
}
else
{
	$game_count = $link->query("select (select count(*) from game_info) + (select count(*) from expansion_info) as total_games");
	if (!$game_count)
		$db_games = "could not be retrieved: {$link->error}";
	else
	{
		$info = $game_count->fetch_assoc();
		$db_games = ": {$info['total_games']}";
		$game_count->close();
	}
}
?>
<div class='container'>
<?php include("includes/nav.php"); ?>
<div class='content'>
<h1>Games by Player Count</h1>
<h6 class='center'>Games in library <?=$db_games?></h6>
<p class='center'>Number of players: <input type='number' min=0 max=12 id='player_count' value=2 /> <input type='button' value='Get Games' onClick='submitPlayers();' /></p>
<div class='result'>
<!--<span class='buffer'>&nbsp;</span><span class='result' id='gameResult'></span> -->
<span class='buffer'>&nbsp;</span><span class='result' id='gameResult'></span>
</div>
</div>
<?php
end_page();
?>

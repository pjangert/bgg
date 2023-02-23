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
	workArea.innerHTML="<h3>Please wait, retrieving results...</h3>";
	start=false;
	if (window.XMLHttpRequest){ xmlhttp=new XMLHttpRequest();}
	else { xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); }
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState == 3) {
		  workArea.innerHTML="<h3>Loading results...</h3>" + xmlhttp.response;
		}
		else if (xmlhttp.readyState == 4) {
		  if (xmlhttp.status == 200) { workArea.innerHTML="<h3>Results Complete</h3>" + xmlhttp.responseText; }
			else { workArea.innerHTML="<p class=\"center error\">Error occurred: " + xmlhttp.statusText + "</p>"; }
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
	$game_count = $link->query("select (select count(*) from game_info) as total_games, (select count(*) from expansion_info) as total_exp");
	if (!$game_count)
		$db_games = "could not be retrieved: {$link->error}";
	else
	{
		$info = $game_count->fetch_assoc();
		$db_games = ": {$info['total_games']} with {$info['total_exp']} expansions";
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
<script>
  document.getElementById('player_count').addEventListener("keyup", function(e) {
    if (e.key === "Enter" ) { e.preventDefault; submitPlayers(); }
  });
</script>
<div class='result'>
<!--<span class='buffer'>&nbsp;</span><span class='result' id='gameResult'></span> -->
<span class='buffer'>&nbsp;</span><span class='result' id='gameResult'></span>
</div>
</div>
<?php
end_page();
?>

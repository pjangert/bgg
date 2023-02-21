<?php
require_once "includes/verify.php";
require_once "includes/common.php";
require_once "includes/dbinfo.php";
$title = new header_item("title","Export to BGG");
$base_site="http://www.boardgamegeek.com";
$push_script='<script>
function pushGames()
{
	username=document.login.username.value;
	password=document.login.password.value;
	document.getElementById("generate").innerHTML="<h3>Processing, please wait</h3>";
	if (window.XMLHttpRequest){ xmlhttp=new XMLHttpRequest();}
	else { xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); }
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) { result_location.innerHTML=xmlhttp.responseText; }
		else if (xmlhttp.readyState==4 && xmlhttp.status!=200) { result_location.innerHTML="<p class=\"center\">Error occurred:"+xmlhttp.statusText+"</p>"; }
		else if (xmlhttp.readyState==1) { result_location.innerHTML="<p class=\"center\">Request Sent - this will take a moment</p>";}
		else if (xmlhttp.readyState==3) { result_location.innerHTML="<p class=\"center\">Processing Data</p>";}
		else { result_location.innerHTML="<p class=\"center\">State: "+xmlhttp.readyState+" received</p>"; }
	}
	xmlhttp.open("GET","push_bgg_games.php?username="+username+"&password="+password,true);
	xmlhttp.send();
}
</script>';
$push_hdr = new header_item("function", $push_script);
start_page(array($title, $main_style, $nav_style, $push_hdr));
$link = new mysqli($db_host, $ro_login, $ro_pw, $gamedb, $db_port);
if (!$link)
{
	echo "<h1>Error connecting to database:</h1>\n<p>{$link->error}</p>\n";
	end_page(1);
}
echo "<div class='container'>\n";
include_once "includes/nav.php";
?>
  <div class='content'>
    <h1>Game Library BGG Export</h1>
    <hr />
    <p>Logged in as <?=$_SESSION['username']?></p>
<!--    <p>Please log into <a href='<?=$base_site?>' target=_new>Board Game Geek</a> before continuing</p> -->
    <span id='generate'><p>Leave password blank if not changed</p><form name='login' method='post' action='javascript:();'>
    <p>BGG Username <input type='text' length=20 name='username' /></p><p>BGG Password <input type='password' name='password' /></p><input type=button value='Export Games' onClick='pushGames();' /></form></span>
    <span id='result'></span>
  </div>
<?php
end_page();
?>

<?php
$DEBUG="";
require_once("includes/verify.php");
require_once("includes/common.php");
$query_func='function submitName()
{
	game_name=document.getElementById("game_name").value;
	result_location=document.getElementById("gameResult");
	if (game_name=="") {result_location.innerHTML=""; return;}
	if (document.getElementById("exact_name").checked) { game_name+="&exact=1"; }
	if (window.XMLHttpRequest){ xmlhttp=new XMLHttpRequest(); }
	else { xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); }
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) { result_location.innerHTML=xmlhttp.responseText; }
		else if (xmlhttp.readyState==4 && xmlhttp.status!=200) { result_location.innerHTML="<p class=\"center\">Error occurred:"+xmlhttp.statusText+"</p>"; }
		else if (xmlhttp.readyState==1) { result_location.innerHTML="<p class=\"center\">Query Sent - this will take a moment</p>";}
		else if (xmlhttp.readyState==3) { result_location.innerHTML="<p class=\"center\">Retrieving Data</p>";}
		else { result_location.innerHTML="<p class=\"center\">State: "+xmlhttp.readyState+" received</p>"; }
	}
	xmlhttp.open("GET","lookup.php?game_name="+game_name,true);
	xmlhttp.send();
}
';
$title = new header_item("title", "Add Game from BGG");
$query_function = new header_item("script", $query_func);
start_page(array($title, $main_style, $nav_style, $query_function));
echo "<div class='container'>\n";
include_once("includes/nav.php");
?>
  <div class='content'>
    <h1>Search for Games to Add from BGG</h1>
    <hr />
    <div class='center'>
    <p class='center'>Game Name: <input type='text' id='game_name' /> Exact Name? <input type='checkbox' id='exact_name' value='Y'><input type='button' value='Search Games' onClick='submitName();' /></p>
    <script>
    document.getElementById("game_name").addEventListener("keyup", function(e) {
      if (e.key === "Enter" ) { e.preventDefault(); submitName(); }
    });
    </script>
    <div class='result'>
<!--<span class='buffer'>&nbsp;</span><span class='result' id='gameResult'></span> -->
      <span class='buffer'>&nbsp;</span>
      <span class='result' id='gameResult'></span>
    </div>
  </div>
</div>
<?php
end_page();
?>

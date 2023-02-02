<?php
$DEBUG = getenv("DEBUG") ?: 0;
require_once("includes/verify.php");
require_once("includes/common.php");
$query_func='function submitName()
{
	user_name=document.getElementById("user_name").value;
	result_location=document.getElementById("gameResult");
	if (user_name=="") {result_location.innerHTML=""; return;}
	if (window.XMLHttpRequest){ xmlhttp=new XMLHttpRequest(); }
	else { xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); }
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) { result_location.innerHTML="<p>Import complete</p>"+xmlhttp.responseText; }
		else if (xmlhttp.readyState==4 && xmlhttp.status==202) { result_location.innerHTML="<p class=\"center\">Still building - try again later:"+xmlhttp.statusText+"</p>"; }
		else if (xmlhttp.readyState==4 && xmlhttp.status!=200) { result_location.innerHTML="<p class=\"center\">Error occurred:"+xmlhttp.statusText+"</p>"; }
		else if (xmlhttp.readyState==1) { result_location.innerHTML="<p class=\"center\">Query Sent - this will take a moment</p>";}
		else if (xmlhttp.readyState==3) { result_location.innerHTML="<p class=\"center\">Retrieving Data - this shouldn\'t take too long</p>";}
		else { result_location.innerHTML="<p class=\"center\">State: "+xmlhttp.readyState+" received</p>"; }
	}
	xmlhttp.open("GET","getlib.php?username="+user_name,true);
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
    <h1>Add BGG collection</h1>
    <hr />
    <div class='center'>
    <p class='center'>BGG User Name: <input type='text' id='user_name' /><input type='button' value='Import Games' onClick='submitName();' /></p>
    <script>
    document.getElementById("user_name").addEventListener("keyup", function(e) {
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

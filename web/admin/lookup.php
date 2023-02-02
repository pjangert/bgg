<?php
  $DEBUG = intval(getenv('DEBUG')) ?: 0;
  require_once "includes/debug.php";
  require_once "includes/common.php";
  require_once "includes/dbinfo.php";
  start_page();
  $bgg_url = "http://www.boardgamegeek.com/xmlapi2/";
  $query = $_REQUEST['game_name'];
  $exact = (isset($_REQUEST['exact'])) ? "&exact=1" : "";
  $load_fail = 0;
  $query_string=$bgg_url."search?query=".$query."&type=boardgame{$exact}";
  date_default_timezone_set("America/New_York");
  $xml_start_time = date("H:i:s");
  $game_response = simplexml_load_file($query_string) or $load_fail = 1;
  $xml_end_time = date("H:i:s");
  $link = new mysqli($db_host, $ro_login, $ro_pw, $gamedb, $db_port);
  if ($DEBUG >= 1) 
  {
    echo "<pre>query_string=".$query_string."</pre>";
    echo "<pre>"; var_dump($game_response); echo "</pre>";
  }
  if ($load_fail != 0)
  {
?>
    <h2>Error Occurred</h2>
    <p>No results were found for <?=$query?></p>
<?
  }
  else
  {
    $results = $game_response['total'];
?>
    <h2>Results</h2>
    <p>Total found: <?=$results?></p>
<?
    if ($DEBUG >= 1) { echo "    <p>Main query: <?=$xml_start_time?> - <?=$xml_end_time?></p>\n";}
    for ($i=0;$i<$results;$i++)
    {
      $bgg_id = $game_response->item[$i]['id'];
      $game_year = $game_response->item[$i]->yearpublished['value'];
      //echo "<pre>".$game_name."</pre>";
      $specific_query = "{$bgg_url}thing?id=$bgg_id";
      $xml_start_time= microtime(true);
      $info_response = simplexml_load_file($specific_query) or $load_fail = 1;
      $xml_end_time= microtime(true);
       //echo "<pre>min_play=".var_dump($info_response)."</pre>";
      /*
if (function_exists('mb_strlen')) { $len = mb_strlen($game_name, '8bit'); }
else { $len = strlen($game_name); }
$ret = '';
for ($c=0;$c<$len;$c++) { $ret .= $c . $game_name[$c] . ' ' . dechex(ord($game_name[$c])).' '; }
echo "<pre>Decode name (" . $len ."): ". $ret ."</pre>";
       */
      if ($load_fail != 0)
      {
?>
    <h3>Error getting info</h3>
    <p>No results retrieved for <?=$bgg_id?></p>
<?
      }
      else
      {
	if (!$link) {echo "<p>Unable to connect to database - some results may show the ability to add existing entries</p>\n";}
        $proc_start_time= microtime(true);
        $name_search = $info_response->xpath('item/name');
        foreach ($name_search as $name_node)
        {
	      //echo "<pre>{${var_dump($name_node)}}</pre>";
	      if ($name_node['type'] == "primary") { $game_name = str_replace(html_entity_decode('&ndash;',ENT_COMPAT, 'UTF-8'), '-', $name_node['value']); }
        }
        $min_players = $info_response->item->minplayers['value'];
        $max_players = $info_response->item->maxplayers['value'];
        if ($info_response->item['type'] == 'boardgame')
        {
          $parent = "Parent";
	  $insert_parent="";
	  $game_table="game_info";
        }
        else
        {
          $parent_id = "";
	  $insert_parent = "";
          $parent = "Child";
	  $game_table="expansion_info";
	  $parent_search = $info_response->xpath('item/link');
//echo "<pre>".var_dump($parent_search)."</pre>";
	  while(list(,$node) = each($parent_search))
	  {
	    if ($node['inbound'] == "true")
	    {
		    //my_show(var_dump($node));
              $parent_id = $node['id'];
	      if ($insert_parent == "") { $insert_parent="&parent=$parent_id"; }
	      else { $insert_parent .=",$parent_id"; }
	    }
	  }
        }
        $proc_end_time= microtime(true);
	echo "<h3>$game_name ($game_year)</h3>\n<p><img src=\"{$info_response->item->thumbnail}\"></p>\n";
	unset($game_check);
	if ($link)
	{
        	$result = $link->query("select bgg_id from {$game_table} where bgg_id = {$bgg_id}");
		if (!$result) { echo "<p>Failed to check record: {$link->error}</p>"; }
	}
	if (isset($result)) 
		$game_check = $result->fetch_assoc();
	if (isset($result) && $game_check['bgg_id'] != null) { echo "<p>Already in database</p>\n"; $result->close(); }
	else { echo "<p><a href=\"insert_{$parent}.php?bgg_id={$bgg_id}&game_name=".urlencode($game_name)."&min_players={$min_players}&max_players={$max_players}{$insert_parent}\">Insert Record</a></p>\n"; }
	if ($DEBUG >= 2) {echo "<p>Times: ". sprintf("get XML: %f, search XML: %f",$xml_end_time - $xml_start_time, $proc_end_time - $proc_start_time) ."</p>\n";}
	echo "<hr />\n";
      //echo "<p>BGG ID: ".$bgg_id." Name: ".$name."</p>";
        //echo "<p>Player Info</p>\n<p>Min: $min_players Max: $max_players</p>";
      }
      //my_show(date("H:i:s"));
    }
  }
  end_page();
?>

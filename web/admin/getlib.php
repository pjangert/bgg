<?php
  require_once "includes/common.php";
  require_once "includes/dbinfo.php";
  require_once "includes/verify.php";
  ob_end_clean();
  //start_page();
  $bgg_url = "http://www.boardgamegeek.com/xmlapi2/";
  $query = $_REQUEST['username'];
  $load_fail = 0;
  $query_string=$bgg_url."collection?username=".$query."&type=boardgame&own=1&brief=1";
  date_default_timezone_set("America/New_York");
  $xml_start_time = date("H:i:s");
  //echo "<pre>query_string=".$query_string."</pre>";
  $game_response = simplexml_load_file($query_string) or $load_fail = 1;
  $xml_end_time = date("H:i:s");
  $link = new mysqli($db_host, $ro_login, $ro_pw, $gamedb, $db_port);
  if ($load_fail != 0 )
  {
?>
    <h2>Error Occurred</h2>
    <p>No results were found for <?=$query?></p>
<?
  }
  else
  {
    if (!$link)
    {
        if ($DEBUG >= 1) 
            echo "<p>Failed to connect to DB ${gamedb} on {$db_host}:{$db_port} using {$db_user}</p>";
        else
            echo "<p>Failed to connect to database</p>";
        die;
    }
    if (!isset($game_response['totalitems']))
    {
      http_response_code(202);
      die;
    }
    $results = $game_response['totalitems'];
    $insert_count = 0;
    $processed = 0;
    debug_show(1, "    <p>Main query: {$xml_start_time} - {$xml_end_time}</p>\n");
    debug_show(3, $game_response['totalitems']);
    for ($i=0;$i<$results;$i++)
    {
      $bgg_id = $game_response->item[$i]['objectid'];
      unset($game_check);
      $result = $link->query("select bgg_id from game_info where bgg_id = {$bgg_id} union select bgg_id from expansion_info where bgg_id = {$bgg_id}");
      if (!$result) { echo "<p>Failed to check record: {$link->error}</p>"; }
    }
  	if (isset($result))
  	{
	    $game_check = $result->fetch_assoc();

    	if (isset($result) && $game_check['bgg_id'] != null) 
    	{
    		$processed++;
    		///echo "<p>{$game_name} is already in database</p>\n"; $result->close(); echo "<hr />\n"; 
    	}
    	else 
      { 
        $specific_query = "{$bgg_url}thing?id=$bgg_id";
        $xml_start_time= microtime(true);
        $info_response = simplexml_load_file($specific_query) or $load_fail = 1;
        $xml_end_time= microtime(true);
        if ($load_fail != 0)
        {
?>
    <h3>Error getting info</h3>
    <p>No results retrieved for <?=$bgg_id?></p>
<?
        }
        else
        {
          $proc_start_time= microtime(true);
          $name_search = $info_response->xpath('item/name');
          foreach ($name_search as $name_node)
          {
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
        	  while(list(,$node) = each($parent_search))
        	  {
        	    if ($node['inbound'] == "true")
        	    {
                $parent_id = $node['id'];
        	      if ($insert_parent == "") { $insert_parent="$parent_id"; }
        	      else { $insert_parent .=",$parent_id"; }
      	      }
        	  }
          }
          $proc_end_time= microtime(true);
          $fields = array('bgg_id' => (string)$bgg_id, 'game_name' => $game_name, 'min_players' => (string)$min_players, 'max_players' => (string)$max_players);
          if ($insert_parent != "") { $fields['parent'] = $insert_parent; }
          $post_fields = http_build_query($fields);
          $url_conn = curl_init();
          $final_url = "http://localhost" . dirname($_SERVER['REQUEST_URI']) . "/mass_{$parent}.php?{$post_fields}";
      	  //my_show($final_url);
          curl_setopt($url_conn, CURLOPT_URL, $final_url);
          curl_setopt($url_conn, CURLOPT_FRESH_CONNECT, true);
  	      curl_setopt($url_conn, CURLOPT_RETURNTRANSFER, true);
          $url_res = curl_exec($url_conn);
  	      $error = curl_error($url_conn);
          curl_close($url_conn);
          if ($error != "" || $url_res != "" ) 
          {
     		    if ($error != "" ) 
     			    echo "<p class='error'>Error: {$error} </p>\n"; 
       		  else
       			  echo "<p class='error'>{$url_res}</p>\n";
       	  }
          else { echo "<p>{$game_name} Added to database</p>\n"; $insert_count++; $processed++;}
        }
      	debug_show(2, "<p>Times: ". sprintf("get XML: %f, search XML: %f",$xml_end_time - $xml_start_time, $proc_end_time - $proc_start_time) ."</p>");
      }
    }
  }
  echo "<p>{$results} Records found on BGG<br />{$processed} records successfully processed<br />{$insert_count} records imported</p>\n";
?>

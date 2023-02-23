<?php
  require_once "includes/common.php";
  require_once "includes/dbinfo.php";

  ob_implicit_flush(true);
  start_page();
  $bgg_url = "http://www.boardgamegeek.com/xmlapi2/";
  $query = $_REQUEST['game_name'];
  $exact = (!empty($_REQUEST['exact'])) ? "&exact=1" : "";
  $show_pics = $_REQUEST['show_pics'];
  $load_fail = 0;
  debug_show(1, "Request vars: game_name: '{$_REQUEST['game_name']}' exact: '{$_REQUEST['exact']}' show_pics: '${_REQUEST['show_pics']}'");
  $query_string=urlencode($bgg_url."search?query=".$query."&type=boardgame{$exact}");
  debug_show(1, "query_string={$query_string}");
  date_default_timezone_set("America/New_York");
  $xml_start_time = new DateTime();
  $game_response = simplexml_load_file($query_string) or $load_fail = 1;
  $xml_elapsed = $xml_start_time->diff(new DateTime());
  $link = new mysqli($db_host, $ro_login, $ro_pw, $gamedb, $db_port);
  debug_show(3, var_export($game_response, true));
  if ($load_fail != 0)
  {
    echo "    <h2>Error Occurred</h2>\n";
    echo "    <p>No results were found for {$query}</p>\n";
  }
  else
  {
    // Ensure unset to determine proper path below
    unset($info_response);
    
    $results = $game_response['total'];
    if (!$link) {echo "<p>Unable to connect to database - some results may show the ability to add existing entries</p>\n";}
    echo "    <h2>Results</h2>\n";
    echo "    <p>Total found: {$results}</p>\n";
    debug_show(2, "Main query: {$xml_elapsed->format('%s.%F s')}");
    debug_show(1, "show_pics={$show_pics} -- number of results: {$results}");
    for ($i=0;$i<$results;$i++)
    {
      // Get info from main response
      $bgg_id = $game_response->item[$i]['id'];
      $game_year = $game_response->item[$i]->yearpublished['value'];
      $gname_search = $game_response->item[$i]->xpath('name');
      foreach ($gname_search as $name_node)
      {
        if ($name_node['type'] == "primary") { $game_name = str_replace(html_entity_decode('&ndash;',ENT_COMPAT, 'UTF-8'), '-', $name_node['value']); }
        $game_name_submit = urlencode($game_name);
      }
      
      // Query individual item for details
      if ($show_pics == "Y" && $results <= $max_games_return)
      {
        $specific_query = "{$bgg_url}thing?id=$bgg_id";
        $xml_start_time= microtime(true);
        $info_response = simplexml_load_file($specific_query) or $load_fail = 1;
        $xml_end_time= microtime(true);
        if ($load_fail != 0)
        {
          echo "    <h3>Error getting info</h3>\n";
          echo "    <p>No results retrieved for {$bgg_id}</p>\n";
        }
        else
        {
          $proc_start_time= microtime(true);
          $name_search = $info_response->xpath('item/name');
          foreach ($name_search as $name_node)
          {
            debug_show(3, var_export($name_node, true));
            if ($name_node['type'] == "primary") { $game_name = str_replace(html_entity_decode('&ndash;',ENT_COMPAT, 'UTF-8'), '-', $name_node['value']); $game_name_submit = urlencode($game_name); }
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
            debug_show(3, var_export($parent_search, true));
            while(list(,$node) = each($parent_search))
            {
              if ($node['inbound'] == "true")
              {
                $parent_id = $node['id'];
                if ($insert_parent == "") { $insert_parent="&parent=$parent_id"; }
                else { $insert_parent .=",$parent_id"; }
              }
            }
          }
          $proc_end_time = microtime(true);
        }
        $params = "bgg_id={$bgg_id}&game_name={$game_name_submit}&min_players={$min_players}&max_players={$max_players}{$insert_parent}";
      }
      else
      {
        // Set generic page since parent/child is unknown
        $parent = "Generic";
        $params = "bgg_id={$bgg_id}&game_name={$game_name_submit}";
      }
      echo "<hr />\n";
      echo "<h3>$game_name ($game_year)</h3>\n";

      // Show image if available
      if (isset($info_response)) { echo "<p><img src=\"{$info_response->item->thumbnail}\"></p>\n"; }
      unset($game_check);
 
      // Check DB to see if game exists
      if ($link)
      {
        $result = $link->query("select bgg_id from game_info where bgg_id = {$bgg_id} union select bgg_id from expansion_info where bgg_id = {$bgg_id}");
        if (!$result) { echo "<p>Failed to check record: {$link->error}</p>"; }
      }
      if (isset($result)) 
        $game_check = $result->fetch_assoc();
      if (isset($result) && $game_check['bgg_id'] != null) { echo "<p>Already in database</p>\n"; $result->close(); }
      else { echo "<p><a href=\"insert_{$parent}.php?{$params}\">Insert Record</a></p>\n"; }
      $xml_elapsed = $xml_end_time - $xml_start_time;
      $proc_elapsed = $proc_end_time - $proc_start_time;
      debug_show(2,"<p>Times: ". "get XML: {$xml_elapsed}s, search XML: ${proc_elapsed}s");
    }
  }
  end_page();
?>

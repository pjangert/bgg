<?php
require_once ("includes/verify.php");

// Set variables to pass along
foreach($_REQUEST as $rfield => $rvalue) {$$rfield = $rvalue; }

// Get item details
$game_fail = 0;
$bgg_url = "http://www.boardgamegeek.com/xmlapi2/";
$game_query = "${bgg_url}thing?id={$bgg_id}";
$item_response = simplexml_load_file($game_query) or $game_fail = 1;
if ($load_fail != 0)
{
  $_SESSION['ins_error'] = "Failed to get info from BGG for game ID = ${bgg_id}";
  header("Location: ' . urlencode({$_SERVER['HTTP_REFERER']}) . '");
}

$min_players = $item_response->item->minplayers['value'];
$max_players = $item_response->item->maxplayers['value'];

if ($item_response->item['type'] == 'boardgame')
{
  $proc_page = "Parent";
}
else
{
  $proc_page = "Child";
  $parent_search = $item_response->xpath('item/link');
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
header("Location: insert_{$proc_page}.php?bgg_id={$bgg_id}&game_name={$game_name}&min_players={$min_players}&max_players={$max_players}{$insert_parent}");
// Redirect to specific processing page

?>

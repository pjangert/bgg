<?php
include_once('includes/debug.php');
require_once('includes/dbinfo.php');


ob_implicit_flush(true);
$link = new mysqli($db_host, $ro_login, $ro_pw, $gamedb, $db_port);
if (!$link) { echo "<p>Error connecting to database - {$link->error}</p>\n"; die; }
if (isset($_REQUEST['players']))
{
  $start_time = new DateTime();
  $overall_start = $start_time;
  $num_players = $_REQUEST['players'];
  $query = "select distinct a.game_id, a.game_name
    from game_info a left outer join expansion_info b on a.game_id = b.parent_id
    where ((a.min_players <= {$num_players} and a.max_players >= {$num_players})
    or (b.min_over <= {$num_players} and (a.max_players >={$num_players} or b.max_over >={$num_players})) 
    or (b.max_over >= {$num_players} and (a.min_players <={$num_players} or b.min_over <={$num_players})))
    order by a.game_name";

  $result = $link->query($query);
  $elapsed = $start_time->diff(new DateTime());
  debug_show(2, "Main query: {$elapsed->format('%s.%F s')}");

  if (!$result) { echo "<p>Error querying database: {$link->error}</p>"; die; }
  $main_count = $result->num_rows;
  if ($main_count > 0) { echo "<h3>{$main_count} games for {$num_players} player". (($num_players > 1) ? "s" : "") .":</h3>\n"; }
  else { echo "<h3>No games found for  {$num_players} player". (($num_players > 1) ? "s" : "") ."</h3>\n"; }
  while ($row = $result->fetch_assoc())
  {
    $detail_query = "select a.game_name, b.exp_name, a.min_players, a.max_players, b.min_over, b.max_over, a.lent_to as game_lend, b.lent_to as exp_lend
    from game_info a left outer join expansion_info b on a.game_id = b.parent_id
    where a.game_id = {$row['game_id']}
    and ((a.min_players <= {$num_players} and a.max_players >= {$num_players})
    or (b.min_over <= {$num_players} and (a.max_players >={$num_players} or b.max_over >={$num_players})) 
    or (b.max_over >= {$num_players} and (a.min_players <={$num_players} or b.min_over <={$num_players}))) ";
    $start_time = new DateTime();
    $detail_result = $link->query($detail_query);
    $elapsed = $start_time->diff(new DateTime());

    if (!$detail_result) { echo "<p>Error in query: {$link->error}</p>\n"; die; }
    $game_play = "";
    $final_result = $detail_result->fetch_assoc();
    if (!$final_result) { echo "<p>Error in query: {$link->error}</p>\n"; die; }
    $expansion="expansion". $detail_result->num_rows > 1 ? "s" : "";
    if ($final_result['min_players'] <= $num_players && $final_result['max_players'] >= $num_players) { 
      if ($final_result['exp_name'] != null) { $game_play = "also with {$expansion}"; }
    }
    else { if ($final_result['exp_name'] != null) { $game_play = "only with {$expansion}"; } }
    $game_play .= ($final_result['game_lend'] != null ? " (on loan to {$final_result['game_lend']})" : "");
    echo "<p>{$final_result['game_name']} {$game_play}</p>";
    $exp_results = $final_result['exp_name'];
    debug_show(2, "Detail query: {$elapsed->format('%s.%F s')}");

    if ($exp_results != null) {echo "<ul>\n<li>{$final_result['exp_name']} " . ($final_result['exp_lend'] != null ? " (on loan to {$final_result['exp_lend']})" : "") . "</li>\n";}

    $start_time = new DateTime();
    while ($final_result = $detail_result->fetch_assoc())
    {
      $elapsed = $start_time->diff(new DateTime());
      echo "<li>{$final_result['exp_name']} " . ($final_result['exp_lend'] != null ? " (on loan to {$final_result['exp_lend']})" : "") . "</li>\n";
      debug_show(2, "Detail fetch: {$elapsed->format('%s.%F s')}");
      $start_time = new DateTime();
    }
    if ($exp_results != null) {echo "</ul>\n";}
    echo "<hr />\n";
//    ob_flush();
//    flush();
    
    $detail_result->close();
  }
  $result->close();
  $total_elapsed = $overall_start->diff(new DateTime());
  debug_show(2, "Overall time: {$total_elapsed->format('%s.%F s')}");
}
$link->close();
?>

<?php
if (!isset($_SESSION['username'])) { header("Location: ../"); }
if (!$link) { header("Location: {${urlencode($_SERVER['HTTP_REFERER'])}}"); }
foreach($_REQUEST as $rfield => $rvalue) {$$rfield = $link->escape_string($rvalue);}  
if ($_SESSION['edit_game'] )
{
  $check_query = "select * from game_info where game_id = {$game_id}";
  $exist_check = $link->query($check_query);
  if (!$exist_check)
  {
    $ins_error = $link->error;
    $ins_message = $check_query;
  }
  elseif ($exist_check->num_rows == 0) { $ins_error = "Game {$_REQUEST['game_name']} (id: {$_REQUEST['game_id']}) does not exist"; $exist_check->close(); }
  else
  {
    $query = "update game_info set";
    $all_fields = $exist_check->fetch_fields();
    $field_count = $exist_check->field_count;
    $counter = 0;
    $game = $exist_check->fetch_assoc();
    foreach($all_fields as $field_info)
    {
      $ffield = $field_info->name;
      if ($ffield != "game_id")
      {
        if ($$ffield != $game[$ffield])
        {
          $counter++;
          //my_show($$ffield . " - " . $game[$ffield]);
          if ($$ffield == null)
            $query .= " {$ffield} = NULL";
          else
            $query .= " {$ffield} = '{$$ffield}'";
          $query .= ",";
        }
      }
    }
    if ($counter > 0)
    {
      $query = substr($query, 0, strlen($query)-1);
      $query .= " where game_id = {$game_id}";
      if (!$link->query($query))
      //if (!my_show($query))
      {
        $ins_error = $link->error;
        $ins_message = "Error updating {$game_id} <br /> {$query}";
      }
    }
    if (!isset($ins_error) || $ins_error = '')
    {
      unset($ins_error);
      if ($counter > 0)
        $ins_message = "Successfully updated game {$game_id} ({$_REQUEST['game_name']})";
      else
        $ins_message = "No updates needed for game {$game_id}";
    }
    $exist_check->close();
  }
}
elseif ($_SESSION['edit_loan'])
{
  $check_query = "select * from game_info where game_id = {$game_id}";
  $exist_check = $link->query($check_query);
  if (!$exist_check)
  {
    $ins_error = $link->error;
    $ins_message = $check_query . "  " . var_export($_REQUEST, true);
  }
  elseif ($exist_check->num_rows == 0) { $game = $exist_check->fetch_assoc(); $ins_error = "Game {$game['game_name']} (id: {$_REQUEST['game_id']}) does not exist"; $exist_check->close(); }
  else
  {
    $game = $exist_check->fetch_assoc();
    $query = "update game_info set lent_to = " . ($loan_to == null ? "NULL" : '{$loan_to}') . " where game_id = {$game_id}";
    //$query = "update game_info set lent_to = '{$loan_to}' where game_id = {$game_id}";
    if (!$link->query($query))
    {
      $ins_error = $link->error;
      $ins_message = "Error updating {$game_id} <br /> {$query}";
    }
    if (!isset($ins_error) || $ins_error = '')
    {
      unset($ins_error);
      $ins_message = "Successfully updated game {$game_id} ({$game['game_name']})";
    }
    $exist_check->close();
  }
}
else
  $ins_error = 'Not authorized to update games';
?>
<!-- HTML in game_upd.php -->

<?php
$DEBUG = 0;
require_once ("includes/verify.php");
require_once ("includes/debug.php");
require_once ("includes/common.php");
require_once ("includes/dbinfo.php");
$title = new header_item("title","Parent Game Selection");
//$script_head = new header_item("script", $scripts);
start_page(array($title, $main_style, $nav_style)); //, $script_head));
$link = new mysqli($db_host, $ro_login, $ro_pw, $gamedb, $db_port);
if (!$link)
{
  echo "<h1>Error connecting to database:</h1>\n<p>{$link->error}</p>\n";
  end_page(1);
}
foreach($_REQUEST as $rfield => $rvalue) {$$rfield = $link->escape_string($rvalue); if ($DEBUG>2) { my_show($rfield . ":\n" . ($$rfield == null ? "NULL" : $$rfield)) ;}}
if ($act == "add")
{
  $exp_id = null;
  $parent_id = null;
}
echo "<div class='container'>\n";
include_once "includes/nav.php";
?>
  <div class='content'>
    <h1>Game Library Parent Selection</h1>
    <hr />
    <p>Logged in as <?=$_SESSION['username']?></p>
<?php
if (isset($ins_error) && $ins_error != "")
  echo ("    <p class='error'>{$ins_error}</p>\n");
if (isset($ins_message) && $ins_message != "")
  echo ("    <p class='error'>{$ins_message}</p>\n");
?>
    <br />
    <table class='update'>
<?php
$game_query = "select a.* from expansion_info a where exp_id = ";
if (isset($exp_id))
  $game_query .= $exp_id;
else
  $game_query .= "(select min exp_id from expansion_info)";

$item_list = $link->query($game_query);
if (!$item_list)
{
        echo "<p class='error'>Query failed {$link->error}</p>";
  echo "<p class='indented'>{$game_query}</p>";
        end_page(1);
}
$curr_array = $item_list->fetch_fields();
$curr_rec_edit = false;
echo "      <tr>\n";
//include "includes/set_fields.php";
echo "      <form name='change' method='post' action='exp_maint.php'>\n";
echo "      <input type='hidden' name='exp_id' value='{$_REQUEST['exp_id']}' />\n";
echo "      <input type='hidden' name='act' value='{$act}' />\n";
debug_show(1, var_export($curr_array, true); }
foreach ($curr_array as $tfield)
{
  debug_show(2, var_export($tfield, true); }
  $thisfield = $tfield->name;
  debug_show(2, var_export($thisfield, true); }
  if ($thisfield != 'exp_id')
  {
    echo "      <input type='hidden' name=";
    echo "'{$thisfield}'";
    if (isset($$thisfield))
      echo "value='{$$thisfield}'";
    echo " />\n";
  }
}
echo "      </tr>\n";
echo "      <tr>\n";
echo "      <tr>\n";
echo "      </tr>\n";
echo "        <td class='sans'>Select Parent for expansion {$exp_name}</td>\n";
echo "      </tr>\n";
echo "      <tr>\n";
echo "        <td class='sans'><a href='exp_maint.php'>Cancel " . ($parent_id == null ? "Add" : "Update") . "</a></td>\n";
echo "      </tr>\n";
$game_query = "select * from game_info order by game_name";
$item_list = $link->query($game_query);
while ($curr_array = $item_list->fetch_assoc())
{
  echo "      <tr>\n";
  echo "        <td class='sans'><a href='javascript:document.change.parent_id.value=\"{$curr_array['game_id']}\";document.change.submit();'>{$curr_array['game_name']}</a></td>\n";
  echo "      </tr>\n";
}
$item_list->close();
echo "    </table>\n";
echo "  <br />\n";
echo "  </div>\n";
echo "</div>\n";
end_page();
?>

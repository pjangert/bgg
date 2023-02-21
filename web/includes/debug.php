<?php
function get_dbg_lvl()
{
  $FDB = (file_exists("debug_level")) ? file_get_contents("debug_level") : false;
  $FDB = ($FDB === false) ? $FDB = 0 : intval($FDB) ;
  $DEBUG = intval(getenv("DEBUG")) ?: $FDB;
  return $DEBUG;
}
$DEBUG = get_dbg_lvl();
function my_show($output_var) { $dump = $output_var; echo "<pre>$dump</pre>\n"; }
function debug_show($level, $response) { if (get_dbg_lvl() >= $level) {echo "<pre>DEBUG{$level}: {$response}</pre>\n";}; }
?>

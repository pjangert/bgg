<?php
$DEBUG = intval(getenv('DEBUG')) ?: 0;
function my_show($output_var) { $dump = $output_var; echo "<pre>$dump</pre>\n"; }
?>

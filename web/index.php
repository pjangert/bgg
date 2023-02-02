<?php
require_once("includes/debug.php");
require_once("includes/common.php");
$title = new header_item("title","Game Library");
start_page(array($title,$main_style, $nav_style));
include_once("includes/nav.php");
?>

<div class='container'>
<h1>Game Library</h1>
<div class='content'>
<p class='indented'><a href='players.php'>Games by Number of Players</a></p>
<p class='indented'><a href='loaned_games.php'>Games Currently on Loan</a></p>
</div>
</div>
<?php
end_page();
?>

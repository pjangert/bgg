<?php
$ENV_SEC_PATH = getenv('ENV_SEC_PATH') ?: "/run/secrets";
$ENV_SEC = $ENV_SEC_PATH . "/db_env_php";
if (is_readable($ENV_SEC))
{
  foreach(file($ENV_SEC, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $dummy => $ENV_LINE)
  {
    $ENV_ARR1=explode("#",$ENV_LINE);
    $ENV_ARR=explode("=",$ENV_ARR1[0]);
    if (! empty($ENV_ARR[0]))
    {
      ${$ENV_ARR[0]}=$ENV_ARR[1];
    }
  }
}
$gamedb = $DB_ENV ?: getenv('DB_ENV') ?: "gamedb";

$ro_login = $DB_RO_USER ?: getenv('DB_RO_USER') ?: "game_query";
$ro_pw = $DB_RO_PASS ?: getenv('DB_RO_PASS') ?: "";
$rw_login = $DB_RW_USER ?: getenv('DB_RW_USER') ?: "game_admin";
$rw_pw = $DB_RW_PASS ?: getenv('DB_RW_PASS') ?: "";
$db_host = $DB_ADDRESS ?: getenv('DB_ADDRESS') ?: "localhost";
$db_port = $DB_PORT ?: getenv('DB_PORT') ?: 3306;
?>

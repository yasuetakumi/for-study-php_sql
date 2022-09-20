<?php
// htmlspecialcharsを短くする
function h($value) {
  return htmlspecialchars($value, ENT_QUOTES);
}


// dbに接続する
function dbconnect() {
  // $db = new mysqli('us-cdbr-east-06.cleardb.net', 'bff80db3a1e66b', '143d2e74', 'heroku_db42ef48675b9bd');
  $db = new mysqli('localhost:3306', 'root', 'root', 'study_app');
  if (!$db) {
    die($db->error);
  }
  return $db;
}
?>
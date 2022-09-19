<?php
// htmlspecialcharsを短くする
function h($value) {
  return htmlspecialchars($value, ENT_QUOTES);
}


// dbに接続する
function dbconnect() {
  $db = new mysqli('localhost:3306', 'root', 'root', 'study_app');
  if (!$db) {
    die($db->error);
  }
  return $db;
}
?>
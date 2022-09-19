<?php
require('./library.php');
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$db = dbconnect();

$stmt = $db->prepare('delete from amount_histories where id = ?');
if (!$stmt) {
  die($db->error);
}

$stmt->bind_param('i', $id);
$success = $stmt->execute();
if (!$success) {
  die($db->error);
}

header('Location: index.php');
exit();
?>
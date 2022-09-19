<?php
require('../library.php');
session_start();

if (isset($_SESSION['form'])) {
  $form = $_SESSION['form'];
} else {
  header('Location: index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $db = dbconnect();
  $stmt = $db->prepare('insert into users (name, email, password) VALUES (?, ?, ?)');
  if (!$stmt) {
    die($db->error);
  }

  $password = password_hash($form['password'], PASSWORD_DEFAULT);
  $stmt->bind_param('sss', $form['name'], $form['email'], $password);
  $success = $stmt->execute();
  if (!$success) {
    die($db->error);
  }
  unset($_SESSION['form']);
	header('Location: complete.php');
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/bootstrap.css">
  <link rel="stylesheet" href="../css/style.css">
  <title>会員登録確認</title>
</head>
<body>
  <div class="container">
    <div class="text-center">
      <div class="h2 text-center">
        <p>Check</p>
        <div class="text-center">
          <img src="../img/icon.jpeg" alt="icon" width="60px">
        </div>
      </div>
      <form action="" method="post" class="form-bg mb-3">
        <div class="text-left">
          <label class="text-left">ニックネーム</label>
          <p class="text-left input-bg"><?php echo $form['name']; ?></p>
        </div>
        <div class="text-left">
          <label class="text-left">メールアドレス</label>
          <p class="text-left input-bg"><?php echo $form['email']; ?></p>
        </div>
        <div class="text-left">
          <label class="text-left">パスワード</label>
          <p class="text-left input-bg">【表示されません】</p>
        </div>
        <div>
        <a href="index.php?action=rewrite" class="mx-1">書き直す</a>
        <input type="submit" class="mx-1 btn btn-primary" value="登録する">
      </div>
      </form>
    </div>
  </div>
</body>
</html>
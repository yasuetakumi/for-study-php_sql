<?php
require('./library.php');
session_start();

$form = [
  'name' => '',
  'email' => '',
  'password' => '',
  'month'
];
$error = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $form['email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
  if ($form['email'] === '') {
    $error['email'] = 'blank';
  }

  $form['password'] = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
  if ($form['password'] === '') {
    $error['password_empty'] = 'blank';
  }
  $password_correct = (!preg_match('/(?=.*[a-zA-Z])(?=.*[0-9])[a-zA-z0-9]{8,}/', $form['password']));
  if ($password_correct) {
    $error['password_format'] = 'correct';
  }

  if (!isset($error['email']) && !isset($error['password'])) {
    $db = dbconnect();
    $stmt = $db->prepare('select id, name, email, password from users where email=? limit 1');
    if (!$stmt) {
      die($db->error);
    }

    $stmt->bind_param('s', $form['email']);
    $success = $stmt->execute();
    if (!$success) {
      die($db->error);
    }

    $stmt->bind_result($id, $name, $email, $hash);
    $stmt->fetch();

    if (password_verify($form['password'], $hash)) {
      $form['name'] = $name;
      $form['id'] = $id;
      $_SESSION['form'] = $form;
      header('Location: index.php');
    } else {
      $error['login'] = 'failed';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./css/bootstrap.css">
  <link rel="stylesheet" href="./css/style.css">
  <title>ログイン画面</title>
</head>

<body>
  <?php include 'header.php' ?>
  <div class="container">
    <div class="text-center">
      <div class="h2 text-center">
        <p>ログイン</p>
      </div>
      <form action="" method="post" class="form-bg mb-3">
        <div class="text-left">
          <label class="text-left">メールアドレス</label>
          <input type="text" class="form-control text-left" name="email" value="<?php echo h($form['email']); ?>">
          <?php if (isset($error['email']) && $error['email'] === 'blank') : ?>
            <p class="error-text">*メールアドレスを入力してください。</p>
          <?php endif; ?>
          <?php if (isset($error['login']) && $error['login'] === 'failed') : ?>
            <p class="error-text">*このメールアドレスはすでに登録されています。</p>
          <?php endif; ?>
        </div>
        <div class="text-left mb-3">
          <label class="text-left">パスワード</label>
          <input type="password" class="form-control text-left" name="password" value="<?php echo h($form['password']); ?>">
          <?php if (isset($error['password_empty']) && $error['password_empty'] === 'blank') : ?>
            <p class="error-text">*パスワードを入力してください。</p>
          <?php endif; ?>
          <?php if (isset($error['password_format']) && $error['password_format'] === 'correct') : ?>
            <p class="error-text">*パスワードは英数字8文字以上で入力して下さい。</p>
          <?php endif ?>
        </div>
        <div>
          <input type="submit" value="ログインする" class="btn btn-primary">
        </div>
      </form>
      <div>
        <p><a href="join/" class="btn btn-link">アカウント登録はこちらから</a></p>
      </div>
    </div>
  </div>
</body>

</html>
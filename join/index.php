<?php
require('../library.php');
session_start();

// sessionの値を確認し、$formを操作
if (isset($_GET['action']) && $_GET['action'] === 'rewrite' && isset($_SESSION['form'])) {
  $form = $_SESSION['form'];
} else {
  $form = [
    'name' => '',
    'email' => '',
    'password' => '',
  ];
}
$error = [];

// sessionに格納するため、入力項目を受け取り$form変数に格納
// バリデーションを表示するため、$error変数に情報を格納
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $form['name'] = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
  if ($form['name'] === '') {
    $error['name'] = 'blank';
  }

  $form['email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
  if ($form['email'] === '') {
    $error['email'] = 'blank';
  }

  $form['password'] = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
  if ($form['password'] === '') {
    $error['password'] = 'blank';
  }
  $password_correct = (!preg_match('/(?=.*[a-zA-Z])(?=.*[0-9])[a-zA-z0-9]{8,}/', $form['password']));
  if ($password_correct) {
    $error['password_format'] = 'correct';
  }

  if (empty($error)) {
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
    if ($form['email'] === $email) {
      $error['register'] = 'sameEmail';
    } else {
      $_SESSION['form'] = $form;
      header('Location: check.php');
      exit();
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
  <link rel="stylesheet" href="../css/bootstrap.css">
  <link rel="stylesheet" href="../css/style.css">
  <title>会員登録</title>
</head>
<body>
  <nav class="navbar mx-3 text-center" style="justify-content: left;">
    <img src="../img/icon.jpeg" alt="icon" width="50px" >
    <div class="h3" style="font-weight: bold; margin: 20px">家計簿システム</div>
  </nav>
  <hr>
  <div class="container"> 
    <div class="text-center">
      <div class="h2 text-center">
        <p>アカウント登録</p>
      </div>
      <form action="" method="post" class="form-bg mb-3">
        <div class="text-left">
          <label class="text-left"><span class="require-text">*必須</span> : ニックネーム</label>
          <input type="text" class="form-control text-left" name="name" value="<?php echo h($form['name']); ?>">
          <?php if (isset($error['name']) && $error['name'] === 'blank'): ?>
            <p class="error-text">*ニックネームを入力してください</p>
          <?php endif; ?>
        </div>
        <div class="text-left">
          <label class="text-left"><span class="require-text">*必須</span> : メールアドレス</label>
          <input type="text" class="form-control text-left" name="email" value="<?php echo h($form['email']); ?>">
          <?php if (isset($error['email']) && $error['email'] === 'blank'): ?>
            <p class="error-text">*メールアドレスを入力してください</p>
          <?php endif; ?>
          <?php if (isset($error['register']) && $error['register'] === 'sameEmail'): ?>
            <p class="error-text">*同じメールアドレスは登録できません</p>
          <?php endif; ?>
        </div>
        <div class="text-left">
          <label class="text-left"><span class="require-text">*必須</span> : パスワード</label>
          <input type="password" class="form-control text-left" name="password" value="<?php echo h($form['password']); ?>">
          <?php if (isset($error['password']) && $error['password'] === 'blank'): ?>
            <p class="error-text">*パスワードを入力してください</p>
          <?php endif; ?>
          <?php if (isset($error['password_format']) && $error['password_format'] === 'correct'): ?>
            <p class="error-text">*パスワードは英数字8文字以上で入力して下さい。</p>
          <?php endif; ?>
        </div>
        <div><input type="submit" class="btn btn-primary" value="次へ"/></div>
      </form>
    </div>
  </div>
  <div>
</body>
</html>
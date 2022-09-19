<?php
require('./library.php');
session_start();


if (isset($_SESSION['form']) && $_SESSION['form'] !== '') {
  $loginData = $_SESSION['form'];
} else {
  header('Location: login.php');
  exit();
}

$db = dbconnect();
$stmt = $db->prepare('select id, category_id, amount_history_type, date, title, amount from amount_histories where user_id=?');
if (!$stmt) :
  die($db->error);
endif;

$stmt->bind_param('i', $loginData['id']);
$success = $stmt->execute();
if (!$success) {
  die($db->error);
}
$stmt->bind_result($id, $category_id, $amount_history_type, $date, $title, $amount);

$amountTotal = 0;
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./css/bootstrap.css">
  <link rel="stylesheet" href="./css/style.css">
  <title>一覧画面</title>
</head>

<body>
  <div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center;">
      <div style="display: flex;">
        <p class="h2" style="margin-top: 10px">List</p>
        <img src="./img/icon.jpeg" alt="icon" width="50px" class="mx-1">
      </div>
      <a href="./logout.php" class="btn btn-link">ログアウト</a>
    </div>
    <p class="mt-1"><a href="./register.php" class="btn btn-primary">登録</a></p>
    <table border="1"  class="table">
      <thead>
        <tr>
          <th>日付</th>
          <th>タイトル</th>
          <th>金額</th>
          <th>カテゴリー</th>
          <th>操作</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($stmt->fetch()) : ?>
          <tr>
            <td>
              <?php echo date('Y/m/d', strtotime($date)); ?>
            </td>
            <td>
              <?php echo $title ?>
            </td>
            <td>
              <?php echo $amount ?>
              <?php $amountTotal = $amountTotal + $amount ?>
            </td>
            <td>
              <?php if ($category_id === 1) echo '食費'; ?>
              <?php if ($category_id === 2) echo '外食費'; ?>
              <?php if ($category_id === 3) echo '日用品'; ?>
              <?php if ($category_id === 4) echo '交通費'; ?>
              <?php if ($category_id === 5) echo '交際費'; ?>
              <?php if ($category_id === 6) echo '趣味'; ?>
              <?php if ($category_id === 7) echo '給料'; ?>
              <?php if ($category_id === 8) echo 'その他'; ?>
            </td>
            <td>
              <a href="./update.php?id=<?php echo h($id) ?>" class="btn btn-success">編集</a>
              <a href="./delete.php?id=<?php echo h($id) ?>" class="btn btn-danger">削除</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    <div>
      <p class="h3">合計 : <?php echo $amountTotal ?>円</p>
    </div>
  </div>
</body>

</html>
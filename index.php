<?php
require('./library.php');
session_start();

$category_kind_expense = array(
  '1' => '食費',
  '2' => '外食費',
  '3' => '日用品',
  '4' => '交通費',
  '5' => '交際費',
  '6' => '趣味',
  '8' => 'その他'
);
$category_kind_income = array(
  '7' => '給料',
  '8' => 'その他',
);

if (isset($_SESSION['form']) && $_SESSION['form'] !== '') {
  $loginData = $_SESSION['form'];
} else {
  header('Location: login.php');
  exit();
}

$db = dbconnect();
$stmt = $db->prepare('select id, category_id, amount_history_type, date, title, amount from amount_histories where user_id=? and month(date) = ? and year(date) = year(current_date()) order by date asc');
if (!$stmt) :
  die($db->error);
endif;

if ($_SESSION['month'] === '') {
  $_SESSION['month'] = date('n');
}

// $_SESSION['month'] = '';

$get = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT);
$m = (int)$_SESSION['month'] + (int)$get;
if ($m >= 12) {
  $m = 12;
}
if ($m <= 1) {
  $m = 1;
}

$_SESSION['month'] = $m;

$stmt->bind_param('ii', $loginData['id'], $m);
$success = $stmt->execute();
if (!$success) {
  die($db->error);
}
$id_array = [];
$category_id_array = [];
$amount_history_type_array = [];
$date_array = [];
$title_array = [];
$amount_array = [];

$stmt->bind_result($id, $category_id, $amount_history_type, $date, $title, $amount);
while ($stmt->fetch()) {
  $id_array[] = $id;
  $category_id_array[] = $category_id;
  $amount_history_type_array[] = $amount_history_type;
  $date_array[] = $date;
  $title_array[] = $title;
  $amount_array[] = $amount;
}
if (is_array($title_array) && empty($title_array)) {
  $table = 0;
} else {
  $table = 1;
}


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
  <?php include 'header.php' ?> 
  <div class="container">
    <div class="flex" style="justify-content: space-between;">
      <p class="h2">一覧</p>
      <p><a href="./register.php" class="btn btn-primary">登録</a></p>
    </div>
    <div class="flex" style="justify-content: space-between;">
      <p class="h3" style="margin-top: 5px;">【<?php echo $m ;?>月のデータ】</p>
      <div class="mb-3">
        <a href="index.php?page=-1" class="btn btn-link">&lt; 先月</a>
        <a href="index.php?page=1" class="btn btn-link">翌月 &gt;</a>
      </div>
    </div>
    <?php if ($table) : ?>
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
          <tr>
            <td>
              <?php foreach ($date_array as $key => $value) : ?>
                <p style="margin-bottom: 1px;"><?php echo date('Y/m/d', strtotime($value)); ?></p><br>
              <?php endforeach; ?>
            </td>
            <td>
              <?php foreach ($title_array as $key => $value) : ?>
                <p style="margin-bottom: 1px;"><?php echo $value ?></p><br>
              <?php endforeach; ?>
            </td>
            <td>
              <?php foreach ($amount_array as $key => $value) : ?>
                <p style="margin-bottom: 1px;"><?php echo $value ?></p><br>
              <?php endforeach; ?>
            </td>
            <td>
              <?php foreach ($category_id_array as $key => $value) : ?>
                <p style="margin-bottom: 25px;"><?php if ($value === 1) echo '食費'; ?></p>
                <p style="margin-bottom: 25px;"><?php if ($value === 2) echo '外食費'; ?></p>
                <p style="margin-bottom: 25px;"><?php if ($value === 3) echo '日用品'; ?></p>
                <p style="margin-bottom: 25px;"><?php if ($value === 4) echo '交通費'; ?></p>
                <p style="margin-bottom: 25px;"><?php if ($value === 5) echo '交際費'; ?></p>
                <p style="margin-bottom: 25px;"><?php if ($value === 6) echo '趣味'; ?></p>
                <p style="margin-bottom: 25px;"><?php if ($value === 7) echo '給料'; ?></p>
                <p style="margin-bottom: 25px;"><?php if ($value === 8) echo 'その他'; ?></p>
              <?php endforeach; ?>
            </td>
            <td>
              <?php foreach ($id_array as $key => $value) : ?>
                <div class="flex">
                  <p><a href="./update.php?id=<?php echo h($value) ?>" class="btn btn-success" style="font-size: 12px; margin: 3px; padding: 3px; ">編集</a></p>
                  <p><a href="./delete.php?id=<?php echo h($value) ?>" class="btn btn-danger" style="font-size: 12px; margin: 3px; padding: 3px;">削除</a></p><br>
                </div>
              <?php endforeach; ?>
            </td>
          </tr>
      </tbody>
    </table>
    <p class="h3">合計 : <?php echo array_sum($amount_array) ?>円</p>
    <?php else : ?>
      <p class="h3 flex" style="justify-content: center; background-color: #EEEEEE; padding: 10px; border: solid 0.3px;">データなし</p>
    <?php endif; ?>
    <div>
    </div>
  </div>
</body>

</html>
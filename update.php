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

$loginData = $_SESSION['form'];

$form = [
  'amount_type' => '',
  'date' => '',
  'title' => '',
  'amount' => '',
  'category' => '',
];
$error = [];
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$db = dbconnect();

$stmt = $db->prepare('select category_id, amount_history_type, date, title, amount from amount_histories where id=?');
if (!$stmt) {
  die($db->error);
}

$stmt->bind_param('i', $id);
$success = $stmt->execute();
if (!$success) {
  die($db->error);
}

$stmt->bind_result($category_id, $amount_type, $date, $title, $amount);
$result = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $form['amount_type'] = filter_input(INPUT_POST, 'amount_type', FILTER_SANITIZE_NUMBER_INT);

  $form['date'] = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_NUMBER_INT);
  if ($form['date'] === '') {
    $error['date'] = 'blank';
  }

  $form['title'] = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
  if ($form['title'] === '') {
    $error['title'] = 'blank';
  }

  $form['amount'] = filter_input(INPUT_POST, 'amount', FILTER_SANITIZE_NUMBER_INT);
  if ($form['amount'] === '') {
    $error['amount'] = 'blank';
  }

  $form['category'] = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);

  if (isset($form['category']) && isset($form['amount_type'])) {
    if ($form['category'] === '7' && $form['amount_type'] === '0') {
      $form['category'] = '8';
    } else if ($form['category'] === '1' && $form['amount_type'] === '1') {
      $form['category'] = '7';
    } else if ($form['category'] === '2' && $form['amount_type'] === '1') {
      $form['category'] = '8';
    }
  }

  if ($form['amount_type'] !== '' && $form['date'] !== '' && $form['title'] !== '' && $form['amount'] !== '' && $form['category'] !== '') {
    $db = dbconnect();

    $stmt = $db->prepare('update amount_histories set category_id = ?, amount_history_type = ?, date = ?, title = ?, amount = ? where id = ?');
    if (!$stmt) {
      die($db->error);
    }
    $stmt->bind_param('iissii', $form['category'], $form['amount_type'], $form['date'], $form['title'], $form['amount'], $id);
    $success = $stmt->execute();
    if (!$success) {
      die($db->error);
    }

    header('Location: index.php');
    exit();
  }
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>更新画面</title>
  <link rel="stylesheet" href="./css/bootstrap.css">
  <link rel="stylesheet" href="./css/style.css">
  <script type="text/javascript" src="js/jquery.min.js"></script>
  <script type="text/javascript" src="js/script.js"></script>
</head>

<body>
  <?php include 'header.php' ?>
  <div class="container" style="margin-top: 50px; margin-bottom: 100px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
      <p class="h2" style="margin-top: 10px">更新</p>
      <p><a href="./index.php" class="btn btn-secondary">一覧へ戻る</a></p>
    </div>
    <form action="" method="post">
      <div style="margin-bottom: 50px;">
        <div class="radio">
          <input type="radio" id="expense" name="amount_type" value="0" class="form-control text-left" <?php if ($form['amount_type'] === '' && $amount_type === 0 || $form['amount_type'] === '0') : ?> <?php echo 'checked'; ?> <?php endif; ?>>
          <label for="expense" class="label">支出</label>
          <input type="radio" id="income" name="amount_type" value="1" <?php if ($form['amount_type'] === '1' || $form['amount_type'] === '' && $amount_type === 1) : ?> <?php echo 'checked'; ?> <?php endif; ?>>
          <label for="income" class="label">収入</label>
        </div>
        <div class="form">
          <div class="row mb-3">
            <div class="col-form-label">
              <label for="date" class="flex"><span class="require-text">*必須</span> 日付</label>
            </div>
            <div class="col">
              <?php if (!isset($error['date']) && $form['date'] === '') : ?>
                <input type="date" name="date" value="<?php echo h(date('Y-m-d', strtotime($date))); ?>" class="form-control">
              <?php elseif ($form['date'] === '') : ?>
                <input type="date" name="date" value="" class="form-control">
              <?php else : ?>
                <input type="date" name="date" value="<?php echo h(date('Y-m-d', strtotime($form['date']))); ?>" class="form-control">
              <?php endif; ?>
              <?php if (isset($error['date']) && $error['date'] === 'blank') : ?>
                <p class="error-text">*日付を入力してください</p>
              <?php endif; ?>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-form-label">
              <label for="title" class="flex"><span class="require-text">*必須</span> タイトル</label>
            </div>
            <div class="col">
              <?php if (!isset($error['title']) && $form['title'] === '') : ?>
                <input type="text" name="title" value="<?php echo h($title); ?>" class="form-control">
              <?php else : ?>
                <input type="text" name="title" value="<?php echo h($form['title']); ?>" class="form-control">
              <?php endif; ?>
              <?php if (isset($error['title']) && $error['title'] === 'blank') : ?>
                <p class="error-text">*タイトルを入力してください</p>
              <?php endif; ?>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-form-label">
              <label for="title" class="flex"><span class="require-text">*必須</span> 金額</label>
            </div>
            <div class="col">
              <?php if (!isset($error['amount']) && $form['amount'] === '') : ?>
                <input type="number" name="amount" value="<?php echo h($amount); ?>" class="form-control">
              <?php else : ?>
                <input type="number" name="amount" value="<?php echo h($form['amount']); ?>" class="form-control">
              <?php endif; ?>
              <?php if (isset($error['amount']) && $error['amount'] === 'blank') : ?>
                <p class="error-text">*金額を入力してください</p>
              <?php endif; ?>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-form-label">
              <label for="title" class="flex"><span class="require-text">*必須</span> カテゴリー</label>
            </div>
            <div class="col">
              <select id="category" name="category" class="form-control">
                <?php if ($amount_type === 0) : ?>
                  <?php foreach ($category_kind_expense as $key => $category) {
                    if ($key === $category_id) {
                      echo '<option value=' . $key . ' selected>' . $category . '</option>';
                    } else {
                      echo '<option value=' . $key . '>' . $category . '</option>';
                    }
                  }
                  ?>
                <?php else : ?>
                  <?php foreach ($category_kind_income as $key => $category) {
                    if ($key === $category_id) {
                      echo '<option value=' . $key . ' selected>' . $category . '</option>';
                    } else {
                      echo '<option value=' . $key . '>' . $category . '</option>';
                    }
                  }
                  ?>
                <?php endif; ?>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <input type="submit" value="登録する" class="btn btn-primary form">
      </div>
    </form>
</body>

</html>
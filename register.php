<?php
require('./library.php');
session_start();

$loginData = $_SESSION['form'];

$form = [
  'amount_type' => '',
  'date' => '',
  'title' => '',
  'amount' => '',
  'category' => '',
];
$error = [];

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
  if ($form['category'] === '') {
    $error['category'] = 'blank';
  }
  if (isset($form['category']) && isset($form['amount_type'])) {
    if ($form['category'] === '7' && $form['amount_type'] === '0') {
      $form['category'] = '8';
    } else if ($form['category'] === '1' && $form['amount_type'] === '1') {
      $form['category'] = '7';
    } else if ($form['category'] === '2' && $form['amount_type'] === '1') {
      $form['category'] = '8';
    }
  }


  if ($form['amount_type'] !== '' && $form['date'] !== '' && $form['title'] !== '' && $form['amount'] !== '' && $form['category'] !== '' ) {
    $db = dbconnect();

    $stmt = $db->prepare('insert into amount_histories (user_id, category_id, amount_history_type, date, title, amount) VALUES (?, ?, ?, ?, ?, ?)');
    if (!$stmt) {
      die($db->error);
    }
    $stmt->bind_param('iiissi', $loginData['id'], $form['category'], $form['amount_type'], $form['date'], $form['title'], $form['amount']);
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
  <title>????????????</title>
  <link rel="stylesheet" href="./css/bootstrap.css">
  <link rel="stylesheet" href="./css/style.css">
  <script type="text/javascript" src="js/jquery.min.js"></script>
  <script type="text/javascript" src="js/script.js"></script>
</head>

<body>
  <?php include 'header.php' ?>
  <div class="container" style="margin-top: 50px; margin-bottom: px;">

      <div style="display: flex; justify-content: space-between;">
        <p class="h2">??????</p>
        <p><a href="./index.php" class="btn btn-secondary">???????????????</a></p>
      </div>

    <form action="" method="post">
      <div style="margin-bottom: 50px;">
        <div class="radio">
          <input type="radio" id="expense" name="amount_type" value="0" class="form-control text-left"
            <?php if (isset($form['amount_type']) && $form['amount_type'] === '0') echo 'checked' ;?> 
            <?php if (empty($form['amount_type'])) echo 'checked' ?> 
          >
          <label for="expense" class="label">??????</label>
          <input type="radio" id="income" name="amount_type" value="1"
            <?php if (isset($form['amount_type']) && $form['amount_type'] === '1') echo 'checked' ;?> 
          >
          <label for="income" class="label">??????</label>
        </div>
        <div class="form">
          <div class="row mb-3">
            <div class="col-form-label">
              <label for="date" class="flex"><span class="require-text">*??????</span> ??????</label>
            </div>
            <div class="col">
              <?php if ($form['date'] === '') : ?>
                <input type="date" name="date" value="" class="form-control">
              <?php else : ?>
                <input type="date" name="date" value="<?php echo h(date('Y-m-d', strtotime($form['date']))); ?>" class="form-control">
              <?php endif; ?>
              <?php if (isset($error['date']) && $error['date'] === 'blank'): ?>
                <p class="error-text">*?????????????????????????????????</p>
              <?php endif; ?>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-form-label">
              <label for="title" class="flex"><span class="require-text">*??????</span> ????????????</label>
            </div>
            <div class="col">
              <input type="text" id="title" name="title" value="<?php echo h($form['title']); ?>" class="form-control">
              <?php if (isset($error['title']) && $error['title'] === 'blank'): ?>
                <p class="error-text">*???????????????????????????????????????</p>
              <?php endif; ?>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-form-label">
              <label for="amount" class="flex"><span class="require-text">*??????</span> ??????</label>
            </div>
            <div class="col">
              <input type="text" id="amount" name="amount" value="<?php echo h($form['amount']); ?>" class="form-control">
              <?php if (isset($error['amount']) && $error['amount'] === 'blank'): ?>
                <p class="error-text">*?????????????????????????????????</p>
              <?php endif; ?>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-form-label">
              <label for="category" class="flex"><span class="require-text">*??????</span> ???????????????</label>
            </div>
            <div class="col">
              
            <select id="category" name="category" class="form-control">
              <option value="1">??????</option>
              <option value="2">?????????</option>
              <option value="3">?????????</option>
              <option value="4">?????????</option>
              <option value="5">?????????</option>
              <option value="6">??????</option>
              <option value="7">?????????</option>
            </select>
              <?php if (isset($error['category']) && $error['category'] === 'blank'): ?>
                <p class="error-text">*??????????????????????????????????????????</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <input type="submit" value="????????????" class="btn btn-primary form">
      </div>
    </form>
  </div>
</body>

</html>
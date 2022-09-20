<nav class="navbar mx-3 text-center">
  <div style="display: flex; justify-content: left;">
    <img src="./img/icon.jpeg" alt="icon" width="50px" height="50px">
    <div class="h3" style="font-weight: bold; margin: 10px;">家計簿システム</div>
  </div>
  <?php if (isset($_SESSION['form'])): ?>
    <div class="flex mx-1" style="align-items: baseline; justify-content: right;">
      <p class="h4" style="font-weight: bold;"><?php echo $loginData['name'] ?></p>
      <a href="./logout.php" class="btn btn-link">ログアウト</a>
    </div>
  <?php endif; ?>
</nav>
<hr>
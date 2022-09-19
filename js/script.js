// 支出
let expense = {
  1: '食費', 
  2: '外食費', 
  3: '日用品', 
  4: '交通費', 
  5: '交際費', 
  6: '趣味', 
  7: 'その他'
};
// 収入
let income = {
  1: '給料', 
  2: 'その他'
};

$(function () {
  $('input[name="amount_type"]:radio').change(function() {  //nameが'amount_type'のラジオボタンの値が変更される度に実行
    let amount = $(this).val();  //選択中のラジオボタンのvalueを変数amountに保存
    if(amount == '0') {  //animalが'expense'の時に
      courseOption(expense);  //関数courseOption()に引数expenseを渡し実行
    } else {
      courseOption(income);  //関数courseOption()に引数incomeを渡し実行
    }
  });
});

function courseOption(n) {
  $('#category option').remove();  //表示されているセレクトボックスのoptionを全て削除
  $.each(n, function(index, val) {
    $('#category').append('<option value=' + index + '>' + val + '</option>');
  });
};
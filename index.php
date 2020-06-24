<?php
session_start();
$session_name = htmlspecialchars($_SESSION['name']);
$session_email = htmlspecialchars($_SESSION['email']);
$session_age = htmlspecialchars($_SESSION['age']);
$session_gender = htmlspecialchars($_SESSION['gender']);
$session_message = htmlspecialchars($_SESSION['message']);

$error_columns = [];
if (isset($_SESSION['errors'])) {
    $error_columns = $_SESSION['errors'];
}
?>

<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SEEDS-INTERNSHIP</title>
    <meta name="description" content=""/>
    <meta name="keywords" content=""/>
    <link rel="stylesheet" media="all" href="assets/css/style.css"/>
</head>
<body>
<main>
    <h1>インターンお問い合わせ</h1>

    <!-- 下記でエラーを表示している。!emptyで$error_columnsの全体の存在を確認し、エラー文章をforeachで表示していく-->
    <?php if (!empty($error_columns)): ?>
        <div class="errors">
            <p>エラーが発生しました。</p>
            <?php foreach ($error_columns as $e) { ?>

                <?= $e ?> <br/>
            <?php } ?>
        </div>
    <?php endif ?>
    <form action="confirm.php" method="post">
        <div class="mail-item">
            <label>
                <span class="d-block">■名前</span>
                <input type="text" name="name" value="<?= $session_name ?? '' ?>"/>
            </label>
        </div>
        <div class="mail-item">
            <label>
                <span class="d-block">■メールアドレス</span>
                <input type="text" name="email" value="<?= $session_email ?? '' ?>"/>
            </label>
        </div>
        <div class="mail-item">
            <label>
                <span class="d-block">■年齢</span>
                <input type="text" name="age" value="<?= $session_age ?? '' ?>"/>
            </label>
        </div>
        <p class="mail-item">
            <span class="d-block">■性別</span>
        <p>
            <label><input type="radio" name="gender"
                          value="男"<?= $session_gender === '男' ? 'checked' : '' ?> />男</label>
            <label><input type="radio" name="gender"
                          value="女"<?= $session_gender === '女' ? 'checked' : '' ?> />女</label>
            <label><input type="radio" name="gender" value="その他"<?= $session_gender === 'その他' ? 'checked' : '' ?> />その他</label>
            <label><input type="radio" name="gender"
                          value="未回答"<?= $session_gender === '未回答' ? 'checked' : '' || empty($session_age) ? 'checked' : '' ?>/>未回答</label>
        </p>
        </p>
        <div class="mail-item">
            <label>
                <span class="d-block">■メッセージ</span>
                <textarea name="message"><?= $session_message ?? '' ?></textarea>
            </label>
        </div>
        <div class="mail-btn">
            <button type="submit" name="send">送信する</button>
        </div>
    </form>
</main>
</body>
</html>

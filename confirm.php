<?php
session_start();
$_SESSION["name"] = $_POST["name"];
$_SESSION["email"] = $_POST["email"];
$_SESSION["gender"] = $_POST["gender"];
$_SESSION["age"] = $_POST["age"];
$_SESSION["message"] = htmlspecialchars($_POST["message"]);
//エラーのカラム情報を格納する変数
$error_columns = [];
//必須入力にするカラム一覧
$column = array(
    'name' => '名前',
    'email' => 'メールアドレス',
    'gender' => '性別',
    'age' => '年齢',
    'message' => 'メッセージ',
);

//カラムすべてをforeachで回す '$key' => '$name' の形
foreach ($column as $key => $name) {
    switch ($key) {
        case 'name':
            if (empty($_POST[$key])) {
                $error_columns[] = $name . "に入力がありません";
            }
            break;
        case 'email':
            if (empty($_POST[$key])) {
                $error_columns[] = $name . "に入力がありません";
            }
            if (!empty($_POST[$key]) && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\?\*\[|\]%'=~^\{\}\/\+!#&\$\._-])*@([a-zA-Z0-9_-])+\.([a-zA-Z0-9\._-]+)+$/", $_POST['email'])) {
                $error_columns[] = $name . "が不正です";
            }
            break;
        case 'gender':
            if (empty($_POST[$key])) {
                $error_columns[] = $name . "に入力がありません";
            }
            break;
        case 'age':
            if (empty($_POST[$key])) {
                $error_columns[] = $name . "に入力がありません";
            }
            break;
        case 'message':
            if (empty($_POST[$key])) {
                $error_columns[] = $name . "に入力がありません";
            }
            break;
        default:
            break;
    }
}
// エラーが起きた時
if (count($error_columns)) {
    // セッションで入力画面にエラー内容を渡す
    $_SESSION['errors'] = $error_columns;        // エラー内容
    header('Location: index.php'); // 入力画面へのリダイレクト
    exit;
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
    <h2>この内容でお間違い無いでしょうか？</h2>
    <div class="mail-item">
        <span class="d-block">■名前</span>
        <p><?= $_SESSION["name"] ?></p>
    </div>
    <div class="mail-item">
        <label>
            <span class="d-block">■メールアドレス</span>
            <p><?= $_SESSION["email"] ?></p>
        </label>
    </div>
    <div class="mail-item">
        <label>
            <span class="d-block">■性別</span>
            <p><?= $_SESSION["gender"] ?></p>
        </label>
    </div>
    <div class="mail-item">
        <label>
            <span class="d-block">■年齢</span>
            <p><?= $_SESSION["age"] ?></p
        </label>
    </div>
    <div class="mail-item">
        <label>
            <span class="d-block">■メッセージ</span>
            <p><?= $_SESSION["message"] ?></p>
        </label>
    </div>
    <form action="complete.php" method="post">
        <div class="mail-btn">
            <button type="submit" name="send">送信する</button>
        </div>
    </form>
    <form action="index.php" method="post">
        <div class="back-btn">
            <button type="submit" name="back">前の画面に戻る</button>
        </div>
    </form>
</main>
</html>

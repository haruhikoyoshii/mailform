<?php
session_start();
$name = htmlspecialchars($_SESSION['name']);
$email = htmlspecialchars($_SESSION['email']);
$age = htmlspecialchars($_SESSION['age']);
$gender = htmlspecialchars($_SESSION['gender']);
$message = htmlspecialchars($_SESSION['message']);

$_POST[] = [$name, $email, $age, $gender, $message];
// お問い合わせフローを通らずに、直接アクセスされた時の対策
if (!isset($_POST['send']) || !$_POST) {
    session_destroy();
    session_start();
    $_SESSION['errors'] = [
        'complete_error' => [
            'error_name' => '不正アクセス',
            'error_message' => '直接完了画面にアクセスしないでください',
        ]
    ];
    header('Location: index.php');
    exit;
}
// 「メール送信設定」
$to = 'st081960@m03.kyoto-kcg.ac.jp'; // メール送信先（自分のメールアドレスを設定する）
$from = 'From: ' . $_POST['email'];       // 差出人メールアドレス
$subject = 'お問い合わせフォームからのメール';    // 件名
// メール本文設定 --------------------------------------------
$messages = <<<EOF
フォームからお問い合わせがありました。
■名前
$name

■メールアドレス
$email

■年齢
$age

■性別
$gender

■メッセージ
$message

EOF;
// ---------------------------------------------------------
mb_send_mail($to, $subject, $messages, $from);// メール送信処理
$success = 'メールを送信しました';
// 送信完了メッセージ
// データベース接続
$db_host = 'db';          // サーバーのホスト名
$db_name = 'intern-form'; // データベース名
$db_user = 'intern';      // データベースのユーザー名
$db_pass = 'intern';      // データベースのパスワード
try {
    $dbh = new PDO(
        'mysql:host=' . $db_host .
        ';dbname=' . $db_name .
        ';charset=utf8mb4',
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    // プリペアドステートメントを使い、安全にデータベースに登録されるようにしている
    $sql = 'INSERT INTO contact (name, email, age, gender, message, created_at, updated_at) values(:name, :email, :age, :gender, :message, :created_at, :updated_at )';
    $query = $dbh->prepare($sql);
    $query->bindValue(':name', $name, PDO::PARAM_STR);
    $query->bindValue(':email', $email, PDO::PARAM_STR);
    $query->bindValue(':age', $age, PDO::PARAM_INT);
    $query->bindValue(':gender', $gender, PDO::PARAM_STR);
    $query->bindValue(':message', $message, PDO::PARAM_STR);
    $query->bindValue(':created_at', date('Y-m-d H:i:s'), PDO::PARAM_STR);
    $query->bindValue(':updated_at', date('Y-m-d H:i:s'), PDO::PARAM_STR);
    $query->execute(); // データベースに保存される
} catch (PDOException $e) {
    exit('データベース接続失敗 ' . $e->getMessage());
}
session_destroy();
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
    <?php if (isset($success)): // メール送信が完了した時に表示 ?>
        <div class="success-message"><?= $success ?></div>
    <?php endif ?>

    <form action="index.php" method="post">
        <div class="back-btn">
            <button type="submit" name="back">最初の画面に戻る</button>
        </div>
    </form>
</main>
</body>
</html>

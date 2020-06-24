<?php
session_start(); // セッション開始

$delete_id = (int)$_GET['id']; // 削除するお問い合わせのID

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
} catch (PDOException $e) {
    exit('データベース接続失敗 ' . $e->getMessage());
}

// ■【お問い合わせの指定1件データ取得】　※削除画面を閲覧した時の内容確認として使用
$sql = 'SELECT * FROM contact WHERE id = :id;';
$query = $dbh->prepare($sql);
$query->bindValue(':id', $delete_id,PDO::PARAM_INT);
$query->execute();
$post_values = $query->fetch(PDO::FETCH_ASSOC); // データベースのカラムをキーとして、値を格納した配列


// ■【削除ボタンを押した時】
if (isset($_POST['delete'])) {
    $query = $dbh->prepare('DELETE FROM contact WHERE id = :id;');
    $query->bindValue(':id', $delete_id, PDO::PARAM_INT);
    $result = $query->execute(); // 実行した結果、削除できたが入ってくる　「true」 or 「false」
    // お問い合わせのデータ削除
    if( $result ) {
        $_SESSION['manage_message'] = ['success' => 'ID: ' . $delete_id . 'の削除が完了しました。'];
    } else {
        $_SESSION['manage_message'] = ['errors' => 'ID: ' . $delete_id . 'の削除に失敗しました。'];
    }
    header('Location: list.php');
    exit;
}

// 画面表示の為のエスケープ処理
foreach ($post_values as $key => $value) {
    $post_values[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); // 確認画面表示用・エスケープ処理も行う
}
?>

<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SEEDS-INTERNSHIP</title>
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <link rel="stylesheet" media="all" href="assets/css/style.css" />
</head>
<body>
<main>
    <h1>インターンお問い合わせ削除</h1>

    <div class="mail-item mail-item--name">
        <span class="d-block">■名前</span>
        <?= $post_values['name'] ?>
    </div>
    <div class="mail-item mail-item--email">
        <span class="d-block">■メールアドレス</span>
        <?= $post_values['email'] ?>
    </div>
    <div class="mail-item mail-item--age">
        <span class="d-block">■年齢</span>
        <?= $post_values['age'] ?>
    </div>
    <div class="mail-item mail-item--gender">
        <span class="d-block">■性別</span>
        <?= $post_values['gender'] ?>
    </div>
    <div class="mail-item mail-item--message">
        <span class="d-block">■メッセージ</span>
        <?= nl2br($post_values['message']); // 入力画面で入力した改行も反映されるようにする
        var_dump($post_values['message']);?>
    </div>
    <div class="delete-btn">
        <div class="delete-back">
            <button type="button" onClick="location.href='list.php'">一覧へ戻る</button>
        </div>

        <form class="delete-send" action="delete.php?id=<?= $delete_id ?>" method="post">
            <button type="submit" id="submit" name="delete">削除する</button>
        </form>
    </div>
</main>
</body>
</html>
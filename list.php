<?php
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
$sql = "select * from contact";
$stmt = $dbh->query($sql);
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
$manage_message = []; // 「削除完了などのメッセージが格納される」一覧ページ閲覧時は空の配列で初期設定
// 削除処理の後にリダイレクトされて来た時
if ($_SESSION['manage_message']) {
    $manage_message = $_SESSION['manage_message'];
    session_destroy(); // エラーメッセージ等を今回のページ表示移行に引き継がない為
    session_start();
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
    <table border="1">
        <tr>
            <th>ID</th>
            <th>名前</th>
            <th>Eメール</th>
            <th>年齢</th>
            <th>性別</th>
            <th>言語</th>
            <th>メッセージ</th>
            <th>作成日時</th>
            <th>更新日時</th>
        </tr>
        <?php foreach ($contacts as $contact): ?>
            <tr>
                <td><?= $contact['id'] ?></td>
                <td><?= $contact['name'] ?></td>
                <td><?= $contact['email'] ?></td>
                <td><?= $contact['age'] ?></td>
                <td><?= $contact['gender'] ?></td>
                <td><?= $contact['lang'] ?></td>
                <td><?= $contact['message'] ?></td>
                <td><?= $contact['created_at'] ?></td>
                <td><?= $contact['updated_at'] ?></td>
                <td>
                    <ul class="list-operation">
                        <li class="edit"><a href="edit.php?id=<?= $contact['id'] ?>">編集</a></li>
                        <li class="delete"><a href="delete.php?id=<?= $contact['id'] ?>">削除</a></li>
                    </ul>
                </td>
            </tr>
        <?php endforeach; ?>

    </table>
</main>
</body>
</html>

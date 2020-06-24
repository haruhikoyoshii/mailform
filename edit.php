<?php
session_start(); // セッション開始

$edit_id = (int)$_GET['id']; // 編集するお問い合わせのID
$errors = []; // エラーを格納

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

// ■【お問い合わせの指定1件データ取得】　※編集画面を閲覧した時の内容確認として使用
$sql = 'SELECT * FROM contact WHERE id = :id;';
$query = $dbh->prepare($sql);
$query->bindValue(':id', $edit_id, PDO::PARAM_INT);
$query->execute();
$post_values = $query->fetch(PDO::FETCH_ASSOC); // データベースのカラムをキーとして、値を格納した配列


// ■【編集ボタンを押した時】
if (isset($_POST['edit'])) {
    // フォームの入力項目で処理
    $form_columns = [
        'name' => '名前',
        'email' => 'メール',
        'age' => '年齢',
        'gender' => '性別',
        'message' => 'メッセージ',
    ];
    foreach ($form_columns as $key => $value) {
        /* $post_values　には、「お問い合わせ内容1件（現在のお問い合わせ）の【データベースに登録されている各項目の値】が配列で設定」
         * 「編集する」ボタンを押したことにより、$_POSTでは、「編集画面で入力した内容」が格納されてくる
         *
         * POSTで渡ってきた値が、$post_valuesを使って画面表示・データベース更新に利用できるように代入
         */
        $post_values[$key] = $_POST[$key];
    }


    // ■【編集ボタンを押した時】バリデーションを行い、エラーが存在する場合には配列としてエラー情報を設定
    foreach ($post_values as $key => $value) {
        switch ($key) {
            case 'name':
                if (empty($value)) {
                    $errors[$key] = [
                        'error_name' => $form_columns[$key],
                        'error_message' => '入力必須です',
                    ];
                }
                break;
            case 'email':
                if (empty($value)) {
                    $errors[$key] = [
                        'error_name' => $form_columns[$key],
                        'error_message' => '入力必須です',
                    ];
                }
                if (!empty($value) && !preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\?\*\[|\]%'=~^\{\}\/\+!#&\$\._-])*@([a-zA-Z0-9_-])+\.([a-zA-Z0-9\._-]+)+$/", $value)) {
                    $errors[$key] = [
                        'error_name' => $form_columns[$key],
                        'error_message' => 'メールアドレスの形式が正しくありません。',
                    ];
                }
                break;
            case 'age':
                if (empty($value)) {
                    $errors[$key] = [
                        'error_name' => $form_columns[$key],
                        'error_message' => '入力必須です',
                    ];
                }
                break;
            case 'gender':
                if (empty($value)) {
                    $errors[$key] = [
                        'error_name' => $form_columns[$key],
                        'error_message' => '入力必須です',
                    ];
                }
                break;
            case 'message':
                if (empty($value)) {
                    $errors[$key] = [
                        'error_name' => $form_columns[$key],
                        'error_message' => '入力必須です',
                    ];
                }
                break;
            default:
                break;
        }
    }
    /* データ構成「$errors」
    $errors = [
        【フォームに設定したname（例　name）】 => [
            'error_name'    => 【フォームの項目名（例　名前）】,
            'error_message' => 【エラーメッセージ】,
        ],
        【フォームに設定したname（例　email）】 => [
            'error_name'    => 【フォームの項目名（例　メールアドレス）】,
            'error_message' => 【エラーメッセージ】,
        ],
    ];
    */


    // ■【編集ボタンを押した時】エラーが起きなかった時には更新処理を行う
    if (!count($errors)) {
        // ■【お問い合わせのデータ更新】
        // プリペアドステートメントを使い、安全にデータベースが更新されるようにしている
        $sql = 'UPDATE contact SET
                    name       = :name,
                    email      = :email,
                    age        = :age,
                    gender     = :gender,
                    message    = :message,
                    updated_at = :updated_at
                    WHERE id = :id;';
        $query = $dbh->prepare($sql);
        $query->bindValue(':name', $post_values['name'], PDO::PARAM_STR);
        $query->bindValue(':email', $post_values['email'], PDO::PARAM_STR);
        $query->bindValue(':age', $post_values['age'], PDO::PARAM_INT);
        $query->bindValue(':gender', $post_values['gender'], PDO::PARAM_STR);
        $query->bindValue(':message', $post_values['message'], PDO::PARAM_STR);
        $query->bindValue(':updated_at', date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $query->bindValue(':id', $edit_id, PDO::PARAM_INT);
        $query->execute(); // データベースに保存される
        $result = $query->execute(); // 実行した結果、編集できたが入ってくる　「true」 or 「false」

        if ($result) {
            $_SESSION['manage_message'] = ['success' => 'ID: ' . $edit_id . 'の編集が完了しました。'];
        } else {
            $_SESSION['manage_message'] = ['errors' => 'ID: ' . $edit_id . 'の編集に失敗しました。'];
        }
        header('Location: list.php');
        exit;
    }
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

    <?php if (count($errors)): // エラーが設定されている時 ?>
        <div class="errors">
            <p>エラーが発生しました</p>
            <ul>
                <?php foreach ($errors as $error_key => $error_value): ?>
                    <li>[<?= $error_value['error_name'] ?>] <?= $error_value['error_message'] ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif ?>

    <form action="edit.php?id=<?= $edit_id ?>" method="post">
        <div class="mail-item mail-item--name">
            <label>
                <span class="d-block">■名前</span>
                <input type="text" name="name" value="<?= $post_values['name'] ?? '' ?>"/>
            </label>
        </div>
        <div class="mail-item mail-item--email">
            <label>
                <span class="d-block">■メールアドレス</span>
                <input type="text" name="email" value="<?= $post_values['email'] ?? '' ?>"/>
            </label>
        </div>
        <div class="mail-item mail-item--age">
            <label>
                <span class="d-block">■年齢</span>
                <input type="number" name="age" value="<?= $post_values['age'] ?? '' ?>">
            </label>
        </div>
        <div class="mail-item mail-item--gender">
            <span class="d-block">■性別</span>
            <label><input type="radio" name="gender" value="男性"<?= $post_values['gender'] === '男性' ? ' checked' : '' ?>>男性</label>
            <label><input type="radio" name="gender" value="女性"<?= $post_values['gender'] === '女性' ? ' checked' : '' ?>>女性</label>
            <label><input type="radio" name="gender"
                          value="その他"<?= $post_values['gender'] === 'その他' ? ' checked' : '' ?>>その他</label>
            <label><input type="radio" name="gender"
                          value="未回答"<?= $post_values['gender'] === '回答しない' || empty($post_values['gender']) ? ' checked' : '' ?>>未回答</label>
        </div>
        <div class="mail-item mail-item--message">
            <label>
                <span class="d-block">■メッセージ</span>
                <textarea name="message"><?= $post_values['message'] ?? '' ?></textarea>
            </label>
        </div>
        <div class="edit-btn">
            <div class="edit-back">
                <button type="button" onClick="location.href='list.php'">一覧へ戻る</button>
            </div>
            <div class="edit-send">
                <button type="submit" id="submit" name="edit">編集する</button>
            </div>
        </div>
    </form>
</main>
</body>
</html>

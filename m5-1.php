<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
<body>
<?php
    //DB接続
    $dsn = 'mysql:dbname=データベース名;host=localhost';
    $user = 'ユーザ名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    //テーブルの作成　id name comment dateのカラムを作成
    $sql = "CREATE TABLE IF NOT EXISTS post"
        ."("
        ."id INT AUTO_INCREMENT PRIMARY KEY,"
        ."name char(32),"
        ."comment TEXT,"
        ."dt datetime,"
        ."password TEXT"
        .");";
    //queryを実行して結果を変数に格納
    $stmt = $pdo -> query($sql);
    //INSERTでデータの入力
    //if文でから出ないときのみ取得
    if(!empty($_POST["name"]) && !empty($_POST["comment"]) && empty($_POST["edited_id"]) && !empty($_POST["password"]))
    {
    //フォームからのデータの取得
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $date = date("Y-m-d H:i:s");
    $password = $_POST["password"];
    //テーブルに新しいデータを追加する。
    $sql = "INSERT INTO post (name, comment, dt, password) VALUES (:name, :comment, :datetime, :password)";
    //INSERTでデータの入力
    $stmt = $pdo -> prepare($sql);
    $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
    $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
    $stmt -> bindValue(':datetime', $date, PDO::PARAM_STR);
    $stmt -> bindParam(':password', $password, PDO::PARAM_STR);
    $stmt -> execute();
    }
    
    //削除機能の追加
    if(!empty($_POST["delete_id"]) && !empty($_POST["delete_password"]))
    {
    $id = $_POST["delete_id"];
    $delete_password = $_POST["delete_password"];
    $sql = 'SELECT * FROM post';
    $stmt = $pdo -> query($sql);
    $results = $stmt -> fetchAll();
    foreach($results as $row)
        {
        if($delete_password == $row["password"]);
            {
            $sql = 'delete from post where id = :id';
            $stmt = $pdo -> prepare($sql);
            $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
            $stmt -> execute();
            }
        }
    }
    
    //編集機能の追加
    if(!empty($_POST["edit_id"]) && !empty($_POST["edit_password"]))
    {
    $edit_id = $_POST["edit_id"];
    $edit_password = $_POST["edit_password"];
    //対象テーブルidを選択してSELECTでsqlに格納
    $sql = 'SELECT * FROM post';
    $stmt = $pdo -> query($sql);
    $results = $stmt -> fetchAll();
    foreach($results as $row)
        {
        if(($edit_id == $row["id"]) && ($edit_password == $row["password"]))
           {
            $edited_id = $_POST["edit_id"];
            $edited_name = $row["name"];
            $edited_comment = $row["comment"];
            $edited_password = $row["password"];
           }
        }
    }
    
    //編集内容に変更
    if(!empty($_POST["edited_id"]))
        {
        $id = $_POST["edited_id"];
        $name = $_POST["name"];
        $comment = $_POST["comment"];
        $date=date("Y-m-d H:i:s");
        $sql = 'UPDATE post SET name=:name, comment=:comment, dt=:datetime WHERE id=:id';
        $stmt = $pdo -> prepare($sql);
        $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
        $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt -> bindValue(':datetime', $date, PDO::PARAM_STR);
        $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
        $stmt -> execute();
        }
    
?>
    
    <!--フォームの作成-->
    <form action = "" method = "post">
        <!--名前フォームの作成-->
        <input type = "text" name = "name" placeholder = "名前" value = "<?php if(!empty($_POST["edit_id"]) && !empty($_POST["edit_password"])) echo $edited_name ?>"><br>
        <!--コメントフォームの作成-->
        <input type = "text" name = "comment" placeholder = "コメント" value = "<?php if(!empty($_POST["edit_id"]) && !empty($_POST["edit_password"])) echo $edited_comment ?>"><br>
        <!--パスワードの送信フォームの作成-->
        <input type = "text" name = "password" placeholder = "パスワード入力" value = "<?php if(!empty($_POST["edit_id"]) && !empty($_POST["edit_password"])) echo $edited_password ?>">
        <!--編集対象ID表示フォーム-->
        <input type = "hidden" name = "edited_id" placeholder = "編集対象ID" value = "<?php if(!empty($_POST["edit_id"]) && !empty($_POST["edit_password"])) echo $edited_id ?>">
        <!--送信ボタンの作成-->
        <input type = "submit" name = "submit" value = "投稿"><br>
        <!--削除フォームとボタンの作成-->
        <input type = "number" name = "delete_id" placeholder = "削除ID"><br>
        <input type="text" name="delete_password" placeholder="パスワード入力">
        <input type = "submit" name = "delete" value = "削除"><br>
        <!--編集フォームとボタンの追加-->
        <input type = "number" name = "edit_id" placeholder = "編集ID"><br>
        <input type="text" name="edit_password" placeholder="パスワード入力">
        <input type = "submit" name = "edit" value = "編集"><br>
    </form>
<?php
    //対象テーブルを選択してSELECT文をsqlに格納
    $sql = 'SELECT * FROM post';
    //queryを実行して結果を変数に格納
    $stmt = $pdo -> query($sql);
    //結果にのこっているすべての行を含む配列を返す
    $results = $stmt -> fetchAll();
    //ループ処理
    foreach($results as $row)
    {
        //$rowの中にはテーブルのカラム名が入る
        echo $row['id'].',';
        echo $row['name'].',';
        echo $row['comment'].',';
        echo $row['dt'].'<br>';
    echo "<hr>";
    }
?>
</body>
</html>
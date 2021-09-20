<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>mission_5-01</title>
    </head>
    <body>
        <div><!--まずはタイトルなど-->
            <h1>好きな曲を教えてください！</h1>
            好きなアーティストと、その中で特に好きな楽曲を教えてください！<br>
            <hr>
        </div>

        <div><!--初期値・変数の定義-->
            <?php
                //まずは初期値
                $aimname=null;
                $aimcoment=null;
                $aimnumber=null;
                $pleasepass=null;
                $pleasepass2=null;
                $pleasepass3=null;
                //初期値終わり

                //変数定義開始
                //POST受信
                if(!empty($_POST["name"])){
                    $artist=$_POST["name"];
                }
                if(!empty($_POST["comment"])){
                    $music=$_POST["comment"];
                }
                if(!empty($_POST["editnumber"])){
                    $editnumber=$_POST["editnumber"];
                }
                if(!empty($_POST["deletenumber"])){
                    $deletenumber=$_POST["deletenumber"];
                }
                if(!empty($_POST["editnumber2"])){
                    $editnumber2=$_POST["editnumber2"];
                }
                if(!empty($_POST["pass1"])){
                    $pass1=$_POST["pass1"];
                }
                if(!empty($_POST["pass2"])){
                    $pass2=$_POST["pass2"];
                }
                if(!empty($_POST["pass3"])){
                    $pass3=$_POST["pass3"];
                }
            ?>
        </div><!--初期値・変数の定義-->
        
        <div><!--データベース初期操作-->
            <?php
                //データベースへの接続
                $dsn='データベース名';
                $user='ユーザー名';
                $password='パスワード';
                $pdo=new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
                //データベースへの接続終わり
            ?>

            <?php
                //テーブルの作成
                $sql = "CREATE TABLE IF NOT EXISTS tbtest"
                ." ("
                . "id INT AUTO_INCREMENT PRIMARY KEY,"
                . "name char(32),"
                . "comment TEXT,"
                . "pass char(32)"
                .");";
                $stmt = $pdo->query($sql);    
                //テーブルの作成終わり
            ?>

            <?php
                /*
                //あとでコメントアウトする
                //構成要素の確認
                $sql ='SHOW CREATE TABLE tbtest';
                $result = $pdo -> query($sql);
                foreach ($result as $row){
                    echo $row[1];
                }
                echo "<hr>"
                //構成要素の確認終わり
                */
            ?>

            <?php
                /*
                //後でコメントアウトする
                //テーブルの確認
                $sql ='SHOW TABLES';
                $result = $pdo -> query($sql);
                foreach ($result as $row){
                    echo $row[0];
                    echo '<br>';
                }
                echo "<hr>";
                //テーブルの確認終わり
                */
            ?>

        </div><!--データベース初期操作おわり-->

        <div><!--書き込み関連-->
            <?php
                if(!empty($artist) && !empty($music) && !empty($pass1) && empty($editnumber)){//もし名前、コメント、パスワードの欄が書き込まれていて編集番号が空欄ならば
                    $name = $artist; //名前の設定
                    $comment = $music; //曲名の設定
                    $pass = $pass1; //パスワードの設定
                    $sql = $pdo -> prepare("INSERT INTO tbtest (name, comment, pass) VALUES (:name, :comment, :pass)");//SQL文「テーブル「tbtest」のname,coment,passを追加する」
                    $sql -> bindParam(':name', $name, PDO::PARAM_STR);//SQLに対して変数(ここではname)をセットする。
                    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);//SQLに対して変数(ここではcomment)をセットする。
                    $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);//SQLに対して変数(ここではpass)をセットする。
                    $sql -> execute();//SQL文の実行
                    $pleasepass="投稿完了しました";
                }
                elseif(!empty($artist) && !empty($music) && empty($pass1)){
                    $pleasepass="パスワードを記入してください";
                }
            ?>
        </div>

        <div><!--削除関連-->
            <?php
                if(!empty($deletenumber) && !empty($pass2)){//もし削除番号とパスワード記入欄が記入されていたら
                    $id = $deletenumber;//idを送信された削除番号に設定して
                    $sql = 'SELECT* FROM tbtest';//SQL文「テーブル「tbtest」の中から全てのカラムを対象として」
                    $stmt = $pdo->query($sql);//上のSQL文の実行
                    $results = $stmt->fetchAll();//全ての内容を配列に格納する？(file($filename)みたいなやつ？)
                    foreach ($results as $deleterow){ //各配列を1行ずつ$deleterowに代入
                        if($pass2==$deleterow['pass']){ //もし$pass2がデータベース上のpassと同じであれば
                            $sql = 'delete from tbtest where id=:id';//SQL文「tbtest上で、idが上で設定した$idと等しい行を、削除する」
                            $stmt = $pdo->prepare($sql);//上のSQL文をセットする。
                            $stmt->bindParam(':id', $id, PDO::PARAM_INT);//SQLに対して変数(ここではid)をセットする。
                            $stmt->execute(); //SQL文の実行
                            $pleasepass2="削除完了しました";//削除完了表示
                        }
                        elseif($pass2 !== $deleterow['pass']){ //もし$pass2がデータベース上のpassと異なる場合
                            $pleasepass2="パスワードが異なります";//エラーメッセージ表示場所をこの文言にする
                        }
                    }
                }
                elseif(!empty($deletenumber) && empty($pass2)){
                    $pleasepass2="パスワードを入力してください";
                }
            ?>
        </div>

        <div><!--編集関連-->
            
            <?php
                //まずはフォーム送信
                if(!empty($editnumber2) && !empty($pass3)){//$editnumber2(投稿フォームの方の番号で隠れてるほう)とパスワード欄が共に記入されている場合
                    $id=$editnumber2;//変数の代入
                    $sql='SELECT * FROM tbtest Where id=:id';//SQL文「テーブル「tbtest」の中から全てのカラムを対象として、idが一致するものを選択する」
                    $stmt = $pdo->prepare($sql); //上のSQL文をセットする                 
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT); //SQLに対して変数(ここではid)をセットする。
                    $stmt->execute(); //SQL文の実行
                    $edit_results = $stmt->fetchAll();//全ての内容を配列に格納する？
                    foreach ($edit_results as $editrow){//各配列を1行ずつ$editrowに代入
                        if($pass3==$editrow['pass']){//もし$pass3がデータベース上のpassと一致していたら
                            $aimname = $editrow['name'];//投稿フォームのvalueの編集
                            $aimcoment = $editrow['comment'];//投稿フォームのvalueの編集
                            $aimnumber= $editrow['id'];//投稿フォームのvalueの編集
                            $pleasepass="こちらで編集を行ってください";
                        }
                        elseif($pass3!==$editrow['pass']){//もし$pass3がデータベース上のpassと一致しない場合
                            $pleasepass3="パスワードが異なります";//エラー表示をこれに設定する
                        }
                    }
                }
                elseif(!empty($editnumber2) && empty($pass3)){//$editnumber2(投稿フォームの方の番号で隠れてるほう)は記入されているがパスワード欄が空欄の場合
                    $pleasepass3="パスワードを記入してください";//エラー表示をこれに設定する
                }
                //フォーム送信終わり
            ?>

            <?php
                //編集書き込み
                if(!empty($artist) && !empty($music) && !empty($editnumber) && !empty($pass1)){//全部のフォームが記入されていた場合
                    $id = $editnumber; //変更する投稿番号
                    $name = $artist;//名前の設定
                    $comment = $music; //曲名の設定
                    $pass=$pass1;//パスワードの設定
                    $sql = 'UPDATE tbtest SET name=:name, comment=:comment, pass=:pass WHERE id=:id';//SQL文「tbtest上で、idが上で設定した$idと等しい行のname,coment,passを、更新する」
                    $stmt = $pdo->prepare($sql);//上のSQL文をセットする
                    $stmt->bindParam(':name', $name, PDO::PARAM_STR);//SQLに対して変数(ここではname)をセットする。
                    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);//SQLに対して変数(ここではcomment)をセットする。
                    $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);//SQLに対して変数(ここではpass)をセットする。
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);//SQLに対して変数(ここではid)をセットする。
                    $stmt->execute();//SQL文の実行
                    $pleasepass="編集完了しました";
                }
                elseif(!empty($artist) && !empty($music) && !empty($editnumber) && empty($pass1)){//パスワードが記入されていない場合
                    $pleasepass="パスワードを入力してください";
                }
                //編集書き込み終わり
            ?>
        </div>
    
        <hr>

        <div><!--フォーム欄作成-->

            <div><!--投稿フォーム-->
                <h1>投稿はこちら！</h1>
                <h2>コメント欄</h2>
                <form action="" method="post">
                    <input type="text" name="name" placeholder="好きなアーティスト名をご記入ください" value="<?php echo $aimname ?>">
                    <br>
                    <input type="text" name="comment" placeholder="特に好きな曲をご記入ください" value="<?php echo $aimcoment ?>">
                    <br>
                    <input type="hidden" name="editnumber" value="<?php echo $aimnumber ?>">
                    <input type="password" name="pass1" placeholder="パスワードをご記入ください">
                    <input type="submit" name="送信" value="送信ボタン">
                </form>
                <br>
                <?php
                    echo $pleasepass;//パスワードエラーメッセージ表示欄
                ?>
            </div>

            <br>

            <div><!--削除フォーム -->
                <h2>削除フォーム</h2>
                <form action="" method="post">
                    <input type="number" name="deletenumber" placeholder="削除したい投稿番号をご記入ください"><br><!--これはフォーム-->
                    <input type="password" name="pass2" placeholder="パスワードをご記入ください">
                    <input type="submit" name="削除"><!--これは送信ボタン-->                
                </form>
                <br>
                <?php
                    echo $pleasepass2;//パスワードエラーメッセージ表示欄
                ?>
            </div>

            <br>

            <div>
                <h2>編集フォーム</h2>
                <form action="" method="post">
                    <input type="number" name="editnumber2" placeholder="編集したい投稿番号をご記入ください" value="<?php echo $aimhensyunumber ?>"><br><!--これはフォーム-->
                    <input type="password" name="pass3" placeholder="パスワードをご記入ください">
                    <input type="submit" name="編集"><!--これは送信ボタン-->
                </form>
                <br>
                <?php
                    echo $pleasepass3;//パスワードエラーメッセージ表示欄
                ?>
            </div>

        </div>

        <h1>投稿一覧</h1>

        <div><!--表示関連-->
            <?php
                $sql = 'SELECT* FROM tbtest';//SQL文「tbtest上の全てのカラムを選択する」
                $stmt = $pdo->query($sql);//SQL文の実行
                $results = $stmt->fetchAll();//データを配列に格納する
                foreach ($results as $row){
                    //$rowの中にはテーブルのカラム名が入る
                    echo $row['id']."<br>";
                    echo $row['name'].',';
                    echo $row['comment'];
                    echo "<br>";
                }
            ?>
        </div>

    </body>
</html>

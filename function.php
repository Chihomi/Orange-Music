<?php
//=====================
//ログ
//=====================
//ログを取るか
ini_set('log_errors','on');
//ログの出力ファイルを指定
ini_set('error_log','php.log');

//=====================
//デバッグ
//=====================
//デバッグフラグ
$debug_flg = true;
function debug($str){
    global $debug_flg;
    if(!empty($debug_flg)){
        error_log('デバッグ：'.$str);
    }
}

//=====================
//セッション準備・セッション有効期限を延ばす
//=====================
//セッションファイルの置き場を更新する(/var/tmp/以下に置くと30日は削除されない)
session_save_path("/var/tmp/");
//ガーベージコレクションが削除するセッションの有効期限を設定（30日以上経っているものに対してだけ100分の1100分の1の確率で削除）
ini_set('session.gc_maxlifetime', 60*60*24*30);
//ブラウザを閉じても削除されないようにクッキー自体の有効期限を延ばす
ini_set('session.cookie_lifetime', 60*60*24*30);
//セッションを使う
session_start();
//現在のセッションIDを新しく生成したものと置き換える
session_regenerate_id();

//========================
//画面表示処理開始ログ吐き出し関数
//========================
function debugLogStart(){
    debug('>>>>>>>>>>>画面表示処理開始');
    debug('セッションID：'.session_id());
    debug('セッション変数の中身；'.print_r($_SESSION,true));
    debug('現在日時タイムスタンプ：'.time());
    if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])){
        debug('ログイン期限日時タイムスタンプ：'.($_SESSION['login_date'] + $_SESSION['login_limit']));
        
    } 
}

//==================
//定数
//==================
//エラーメッセージを定数に設定
define('MSG01','入力必須です');
define('MSG02','メールの形式で入力してください');
define('MSG03','パスワード(再入力)が合っていません');
define('MSG04','6文字以上で入力してください');
define('MSG05','255文字以内で入力してください');
define('MSG06','エラーが発生しました。もう一度やり直してください');
define('MSG07','そのメールアドレスは既に登録されています');
define('MSG08','メールアドレスまたはパスワードが違います');
define('MSG09','半角英数字で入力してください');
define('MSG10','現在のパスワードが違います');
define('MSG11','現在のパスワードと同じです');
define('MSG12','そのメールアドレスは登録されていません');
define('MSG13','文字で入力してください');
define('MSG14','認証キーが正しくありません');
define('MSG15','認証キーの有効期限が切れています');
define('MSG16','正しくありません');
define('MSG17','曲名を入力してください');
define('MSG18','アーティスト名を入力してください');
define('MSG19','おすすめポイントを入力してください');
define('SUC01','パスワードを変更しました');
define('SUC02','プロフィールを変更しました');
define('SUC03','メールを送信しました');
define('SUC04','登録しました');
define('SUC05','投稿しました');
define('SUC06','投稿を削除しました');


//===================
//バリデーション関数
//===================
//エラーメッセージ格納用の配列
$err_msg = array();

//バリデーション関数（未入力チェック）
function validRequired($str,$key){
    if(empty($str)){
        global $err_msg;
        $err_msg[$key] = MSG01;
    }
}

//バリデーション関数（EMAIL形式チェック）
function validEmail($str,$key){
    if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)){
        global $err_msg;
        $err_msg[$key] = MSG02;
    }
}

//バリデーション関数（EMAIL重複チェック）
function validEmailDup($email){
    global $err_msg;
    //例外処理
    try{
        //DB接続
        $dbh = dbConnect();
        //SQL文作成
        $sql = 'SELECT count(*) FROM users WHERE email = :email';
        $data = array(':email' => $email);
        //クエリ実行
        $stmt = queryPost($dbh,$sql,$data);
        //クエリ結果の値を取得
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        //debug('クエリ結果：'.print_r($result));
        
        if(!empty(array_shift($result))){
            $err_msg['email'] = MSG07;
        }
    }catch (Exception $e){
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG06;
    }
}
//バリデーション関数（同値チェック）
function validMatch($str1,$str2,$key){
    if($str1 !== $str2){
        global $err_msg;
        $err_msg[$key] = MSG03;
    }
}
//バリデーション関数（最小文字数チェック）
function validMinLen($str,$key,$min=6){
    if(mb_strlen($str) < $min){
        global $err_msg;
        $err_msg[$key] = MSG04;
    }
}
//バリデーション関数（最大文字数チェック）
function validMaxLen($str,$key,$max=255){
    if(mb_strlen($str) > $max){
        global $err_msg;
        $err_msg[$key] = MSG05;
    }
}
//バリデーション関数（半角変換）
function validHalf($str,$key){
    if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
        global $err_msg;
        $err_msg[$key] = MSG09;
    }
}
//固定長チェック
function validLength($str,$key,$len=6){
    if(mb_strlen($str) !== $len){
        global $err_msg;
        $err_msg[$key] = $len.MSG13;
    }
}
//パスワードチェック
function validPass($str,$key){
    validHalf($str,$key);
    validMaxLen($str,$key);
    validMinLen($str,$key);
}
//selectboxチェック
function validSelect($str,$key){
    if(!preg_match("/^[0-9]+$/",$str)){
        global $err_msg;
        $err_msg[$key] = MSG16;
    }
}
//エラーメッセージ表示
function getErrMsg($key){
    global $err_msg;
    if(!empty($err_msg[$key])){
        return $err_msg[$key];
    }
}
//エラースタイル指定
function getErrStyle($key){
    global $err_msg;
    if(!empty($err_msg[$key])){
        return 'err';
    }
}
//===================
//ログイン認証
//===================
function isLogin(){
    //ログインしている場合
    if(!empty($_SESSION['login_date'])){
        debug('ログイン済みユーザーです');
        
        //現在日時が最終ログイン日時＋有効期限を超えていた場合
        if( ($_SESSION['login_date'] + $_SESSION['login_limit']) < time()){
            debug('ログイン有効期限オーバーです');
            
            //セッションを削除
            session_destroy();
            return false;
        }else{
            debug('ログイン有効期限内です');
            return true;
        }
    }else{
        debug('未ログインユーザーです');
        return false;
    }
}
//====================
//データベース
//====================
//DB接続関数
function dbConnect(){
    //DBへの接続準備
    $dsn = 'mysql:dbname=sakuraq_orangemusic;host=mysql8076.xserver.jp;charset=utf8';
    $user = 'sakuraq_aaa';
    $password = 'shokuho5';
    $options = array(
        // SQL実行失敗時にはエラーコードのみ設定
    PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
    // デフォルトフェッチモードを連想配列形式に設定
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // バッファードクエリを使う(一度に結果セットをすべて取得し、サーバー負荷を軽減)
    // SELECTで得た結果に対してもrowCountメソッドを使えるようにする
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    );
    //PDOオブジェクト生成（DB接続）
    $dbh = new PDO($dsn,$user,$password,$options);
    return $dbh;
    
}

//SQL実行関数
function queryPost($dbh,$sql,$data){
    //クエリ作成
    $stmt = $dbh->prepare($sql);
    //プレスホルダに値をセットし、SQL文を実行
    if(!$stmt->execute($data)){
        debug('クエリに失敗しました');
        debug('失敗したSQL：'.print_r($stmt,true));
        $err_msg['common'] = MSG06;
        return 0;
    }
    debug('クエリ成功');
    return $stmt;
}

function getUser($u_id){
    debug('ユーザー情報を取得します');
    
    try{
        $dbh = dbConnect();
        
        $sql = 'SELECT * FROM users WHERE id = :u_id';
        
        $data = array(':u_id' => $u_id);
        
        $stmt = queryPost($dbh,$sql,$data);
        
        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
        
    }catch (Exception $e){
        error_log('エラー発生：'.$e->getMessage());
    }
}
function getPost($u_id,$p_id){
    debug('投稿情報を取得します');
    debug('ユーザーID：'.print_r($u_id,true));
    debug('投稿ID：'.$p_id);
    
    try{
        
        $dbh = dbConnect();
        
        $sql = 'SELECT * FROM post WHERE user_id = :u_id AND id = :p_id AND delete_flg = 0';
        
        $data = array(':u_id' => $u_id,':p_id' => $p_id);
        
        $stmt = queryPost($dbh,$sql,$data);
        
        if($stmt){
            return $stmt -> fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
        
    }catch (Exception $e){
        error_log('エラー発生：'.$e->getMessage());
    }
}
function getPostList($currentMinNum = 1,$genre,$artist,$span=9){
    debug('商品情報を取得します');
    
    try{
        
        $dbh = dbConnect();
        
        $sql = 'SELECT * FROM post';
        
        if(!empty($genre) && empty($artist)){
            $sql .=' WHERE delete_flg = 0 AND genre_id = '.$genre;
        }elseif(!empty($artist) && empty($genre)){
            $sql .=" WHERE delete_flg = 0 AND artist = '$artist'";
            
        }elseif(!empty($genre) && !empty($artist)){
            $sql .=' WHERE delete_flg = 0 AND genre_id ='.$genre." AND artist = '$artist'";
        }else{
            $sql .=' WHERE delete_flg = 0';
        }
        $sql .=' ORDER BY id DESC';
        $data = array();
        debug('SQL0:'.$sql);
        
        $stmt = queryPost($dbh,$sql,$data);
        $rst['total'] = $stmt->rowCount(); //総レコード数
        $rst['total_page'] = ceil($rst['total']/$span); //総ページ数
        if(!$stmt){
            return false;
        }
        
        //ページング用のSQL文作成
        $sql = 'SELECT * FROM post';
        if(!empty($genre) && empty($artist)){
            $sql .=' WHERE delete_flg = 0 AND genre_id = '.$genre;
        }elseif(!empty($artist) && empty($genre)){
            $sql .=" WHERE delete_flg = 0 AND artist = '$artist'";
            
        }elseif(!empty($genre) && !empty($artist)){
            $sql .=' WHERE delete_flg = 0 AND genre_id ='.$genre." AND artist = '$artist'";
        }else{
            $sql .=' WHERE delete_flg = 0';
        }
        $sql .=' ORDER BY id DESC';
        $sql .=' LIMIT '.$span.' OFFSET '.$currentMinNum;
        
        $data = array();
        debug('SQL:'.$sql);
        
        $stmt = queryPost($dbh,$sql,$data);
        
        if($stmt){
            $rst['data'] = $stmt->fetchAll();
            return $rst;
            
        }else{
            return false;
        }
    }catch (Exception $e){
        error_log('エラー発生：'. $e->getMessage());
    }
}
function getPostListAll($u_id){
    debug('ユーザーの投稿情報をすべて取得します');
    
    try{
        
        $dbh = dbConnect();
        
        $sql = 'SELECT * FROM post WHERE user_id = :u_id AND delete_flg = 0 ORDER BY created_date DESC';
        
        $data = array(':u_id' => $u_id);
        
        $stmt = queryPost($dbh,$sql,$data);
        
        if($stmt){
            return $stmt->fetchAll();
        }else{
            return false;
        }
        
    }catch (Exception $e){
        error_log('エラー発生：'.$e->getMessage());
    }
}
function getPostOne($p_id){
    debug('投稿情報を取得します');
    debug('投稿ID：'.$p_id);
    
    try{
        $dbh = dbConnect();
        
        $sql = 'SELECT p.id, p.title, p.artist, p.comment, p.user_id, p.created_date, p.update_date, u.pic,u.username FROM post AS p LEFT JOIN users AS u ON p.user_id = u.id WHERE p.id = :p_id';
        
        $data = array(':p_id' => $p_id);
        
        $stmt = queryPost($dbh,$sql,$data);
        
        
        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
        
    }catch (Exception $e){
        error_log('エラー発生：'.$e->getMessage());
    }
}
function getMsgsAndBord($id){
    debug('msg情報を取得します');
    //debug('bordID:'.$id);
    
    try{
        
        $dbh = dbConnect();
        
        $sql = 'SELECT m.id, m.post_id, m.send_date, m.from_user, m.msg, m.delete_flg, m.create_date, u.id, u.username, u.pic FROM message AS m INNER JOIN users AS u ON m.from_user = u.id WHERE m.post_id = :id AND m.delete_flg = 0 ORDER BY m.send_date ASC' ;
        
        $data = array(':id' => $id);
        
        $stmt = queryPost($dbh,$sql,$data);
        
        if($stmt){
            //debug('データ：'.$stmt);
            return $stmt->fetchAll();
        }else{
            return false;
        }
        
    }catch (Exception $e){
        error_log('エラー発生：' .$e->getMessage());
    }
}
function getMyPost($u_id){
    debug('自分の投稿を取得します');
    
    try{
        
        $dbh = dbConnect();
        
        $sql = 'SELECT id, title, artist, created_date FROM post WHERE user_id = :u_id AND delete_flg = 0 ORDER BY id DESC';
        
        $data = array(':u_id' => $u_id);
        
        $stmt = queryPost($dbh,$sql,$data);
        
        if($stmt){
            return $stmt->fetchALL();
        }else{
            return false;
        }
    }catch (Exceptin $e){
        error_log('エラー発生：'.$e->getMessage());
    }
}
function getGenre(){
    debug('ジャンル情報を取得します');
    
    try{
        
        $dbh = dbConnect();
        
        $sql = 'SELECT * FROM genre';
        
        $data = array();
        
        $stmt = queryPost($dbh,$sql,$data);
        
        if($stmt){
            return $stmt->fetchAll();
        }else{
            return false;
        }
        
    }catch (Exception $e){
        error_log('エラー発生：'.$e->getMessage());
    }
}
function isLike($u_id,$p_id){
    debug('お気に入り情報があるか確認します');
    debug('ユーザーID：'.$u_id);
    debug('投稿ID：'.$p_id);
    
    try{
        
        $dbh = dbConnect();
        
        $sql = 'SELECT count(*) FROM favorite WHERE post_id = :p_id AND user_id = :u_id';
        
        $data = array(':p_id' => $p_id,':u_id' => $u_id);
        
        $stmt = queryPost($dbh,$sql,$data);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!empty(array_shift($result))){
            debug('お気に入りです');
            debug('データ：'.print_r($result));
            return true;
        }else{
            debug('お気に入りではありません');
            debug('データ：'.print_r($result));
            return false;
        }
        
    }catch (Exception $e){
        error_log('エラー発生:' .$e->getMessage());
    }
}
//===================
//メール送信
//===================
function sendMail($from,$to,$subject,$comment){
    if(!empty($to) && !empty($subject) && !empty($comment)){
        //文字化けお決まりパターン
        mb_language("Japanese");
        mb_internal_encoding("UTF-8");
        
        $result = mb_send_mail($to,$subject,$comment,"From:".$from);
        
        if($result){
            debug('メールを送信しました');
        }else{
            debug('メールの送信に失敗しました');
        }
    }
}


//================
//その他
//================
//サニタイズ
function sanitize($str){
    return htmlspecialchars($str,ENT_QUOTES);
}

//フォーム入力保持
function getFormData($str,$flg = false){
    if($flg){
        $method = $_GET;
    }else{
        $method = $_POST;
    }
    global $dbFormData;
    //ユーザーデータがある場合
    if(!empty($dbFormData)){
        //フォームのエラーがある場合
        if(!empty($err_msg[$str])){
            //POSTにデータがある場合
            if(isset($method[$str])){
                return sanitize($method[$str]);
            }else{
                //ない場合（基本的にありえない）
                return sanitize($dbFormData[$str]);
            }
        }else{
            //postにデータがあり、DB情報と違う場合
            if(isset($method[$str]) && $method[$str] !== $dbFormData[$str]){
                return sanitize($method[$str]);
            }else{
                return sanitize($dbFormData[$str]);
            }
        }
        
    }else{
        if(isset($method[$str])){
            return sanitize($method[$str]);
        }
    }
}

//メッセージサクセスの表示のため（sessionを一回だけ取得できる）
function getSessionFlash($key){
    if(!empty($_SESSION[$key])){
        $data = $_SESSION[$key];
        $_SESSION[$key] = '';
        return $data;
    }
}

//認証キー生成
function makeRandKey($length = 6){
    static $chars = '0123456789';
    $str = '';
    for ($i=0; $i<$length; ++$i){
        $str .=$chars[mt_rand(0,9)];
    }
    return $str;
}
//画面処理
function uploadImg($file,$key){
    debug('画面アップロード処理開始');
    debug('file情報：'.print_r($file,true));
    
    if(isset($file['error']) && is_int($file['error'])){
        try{
            switch($file['error']){
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    throw new RuntimeException('ファイルが選択されていません');
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_INI_SIZE:
                    throw new RuntimeException('ファイルサイズが大きすぎます');
                default:
                    throw new RuntimeException('その他のエラーが発生しました');
            }
            $type = @exif_imagetype($file['tmp_name']);
            if(!in_array($type,[IMAGETYPE_GIF,IMAGETYPE_JPEG,IMAGETYPE_PNG],true)){
                throw new RuntimeException('画像形式が未対応です');
            }
            $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
            if(!move_uploaded_file($file['tmp_name'],$path)){
                throw new RangeException('ファイル保存時にエラーが発生しました');
            }
            
            chmod($path,0644);
            
            debug('ファイルは正常にアップロードされました');
            debug('ファイルパス：'.$path);
            return $path;
            
        }catch (RuntimeException $e){
            debug($e->getMessage());
            global $err_msg;
            $err_msg[$key] = $e->getMessage();
        }
        
    }
}
function showImg($path){
    if(empty($path)){
        return 'img/sample.jpg';
    }else{
        return($path);
    }
}
//getパラメータ付与
function appendGetParam($arr_del_key = array()){
    if(!empty($_GET)){
        $str = '?';
        foreach($_GET as $key => $val){
            if(!in_array($key,$arr_del_key,true)){
                $str .=$key.'='.$val.'&';
            }
        }
        $str = mb_substr($str,0,-1,"UTF-8");
        return $str;
    }
}
function appendGetParam2($arr_del_key = array()){
    if(!empty($_GET)){
        $str = '&';
        foreach($_GET as $key => $val){
            if(!in_array($key,$arr_del_key,true)){
                if($key == "p"){
                    continue;
                }
                $str .=$key.'='.$val.'&';
            }
        }
        $str = mb_substr($str,0,-1,"UTF-8");
        return $str;
    }
}
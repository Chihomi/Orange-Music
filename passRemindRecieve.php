<?php
ini_set('display_errors','on');
ini_set('error_reporting',E_ALL);

require('function.php');

debug(' 「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug(' パスワード再発行認証ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();


if(empty($_SESSION['auth_key'])){
    header("Location:passRemindSend.php");
}

//================
//画面処理
//================
//POSTあり
if(!empty($_POST)){
    debug('post送信があります');
    debug('POST情報：'.print_r($_POST,true));
    
    $auth_key = $_POST['token'];
    
    validRequired($auth_key,'token');
    validLength($auth_key,'token');
    validHalf($auth_key,'token');
    
    if(empty($err_msg)){
        debug('パリデーションOK');
        
        if($auth_key !== $_SESSION['auth_key']){
            $err_msg['token'] = MSG14;
        }
        if(time() > $_SESSION['auth_key_limit']){
            $err_msg['token'] = MSG15;
        }
        
        if(empty($err_msg)){
            debug('認証OK');
            
            $pass = makeRandKey();
            debug('新パス：'.print_r($pass,true));
            
            try{
                $dbh = dbConnect();
                
                $sql = 'UPDATE users SET password = :pass WHERE email = :email';
                
                $data = array(':pass' => password_hash($pass,PASSWORD_DEFAULT),':email' => $_SESSION['auth_email']);
                
                $stmt = queryPost($dbh,$sql,$data);
                
                if($stmt){
                    debug('クエリ成功');
                    
                    $from = 'chihomihikaru@gmail.com';
                    $to = $_SESSION['auth_email'];
                    $subject = '【パスワード再発行完了】｜　Orange Music';
                    $comment = <<<EOT
本メールアドレス宛にパスワードの再発行を致しました。
下記のURLにて再発行パスワードをご入力頂き、ログインください。

ログインページ：http://localhost:8888/Orange_Music/login.php
再発行パスワード：{$pass}
※ログイン後、パスワードのご変更をお願い致します

////////////////////////////////////////
Orange Music 
カスタマーセンター
URL http://
E-mail info@sakura.com
////////////////////////////////////////
EOT;
                    sendMail($from,$to,$subject,$comment);
                    
                    session_unset();
                    $_SESSION['msg_success'] = SUC03;
                    debug('セッション変数の中身：'.print_r($_SESSION,true));
                    
                    header("Location:login.php");
                    
                }else{
                    debug('クエリ失敗');
                    $err_msg['common'] = MSG06;
                }
                
            }catch (Exception $e){
                error_log('エラー発生：'.$e->getMessage());
                $err_msg['common'] = MSG07;
            }
        }
    }
}

?>
<?php
$siteTitle = 'パスワード再発行認証';
require('head.php');
?>

<body>
    
    <!-- メニュー　-->
    <?php
    require('header.php');
    ?>
    
    <p id="js-show-msg" style="display:none;" class="msg-slide">
        <?php echo getSessionFlash('msg_success'); ?>
    </p>
    
    <!-- メインコンテンツ　-->
    <div id="contents" class="site-width">
        
        <!-- Main -->
        <div class="form-container">
            
            <form action="" method="post" class="form3" style="height:400px;">
                <p style="margin-top:10px;padding-left:21px;">認証キーを入力してください</p>
                <div class="area-msg">
                    <?php echo getErrMsg('common'); ?>
                </div>
                <label class="<?php echo getErrStyle('token'); ?>">
                    <span>認証キー</span>
                    <input type="text" name="token" value="<?php echo getFormData('token'); ?>">
                </label>
                <div class="area-msg">
                    <?php echo getErrMsg('token'); ?>
                </div>
                <input type="submit" class="btn btn-mid2" value="パスワード再発行">
                <div class="btn">
                <a href="passRemindSend.php">パスワード再発行メールを再度送信</a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- footer -->
    <?php
    require('footer.php');
    ?>


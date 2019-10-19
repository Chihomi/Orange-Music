<?php
ini_set('display_errors','on');
ini_set('error_reporting',E_ALL);

//共通変数・関数ファイル読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「');
debug('「　パスワード再発行送信ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();


//====================
//画面処理
//====================
//post送信されていた場合
if(!empty($_POST)){
    debug('post送信があります');
    debug('POST情報：'.print_r($_POST,true));
    
    //変数にPOST情報を代入
    $email = $_POST['email'];
    
    validEmail($email,'email');
    validRequired($email,'email');
    
    if(empty($err_msg)){
        debug('パリデーションOKです');
        
        try{
            $dbh = dbConnect();
            
            $sql = 'SELECT count(*) FROM users WHERE email = :email';
            
            $data = array(':email' => $email);
            
            $stmt = queryPost($dbh,$sql,$data);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            debug('結果：'.print_r($result,true));
            if($stmt && array_shift($result)){
                debug('クエリ成功。DB登録あり');
                $_SESSION['msg_success'] = SUC03;
                
                $auth_key = makeRandKey();
                
                //メールを送信
                $from = 'chihomihikaru@gmail.com';
                $to = $email;
                $subject = ' 【パスワード再発行認証】 |  Orange Music';
                $comment = <<<EOT
本メールアドレス宛にパスワード再発行のご依頼がありました。
下記のURLにて認証キーをご入力頂くとパスワードが再発行されます。

パスワード再発行認証キー入力ページ：http://localhost:8888/Orange_Music/passRemindRecieve.php
認証キー：{$auth_key}
※認証キーの有効期限は30分となります

認証キーを再発行されたい場合は下記ページより再度再発行をお願い致します。
http://localhost:8888/Orange_Music/passRemindSend.php

////////////////////////////////////////
Orange Music 
カスタマーセンター
URL http://
E-mail info@sakura.com
////////////////////////////////////////
EOT;
                
                sendMail($from,$to,$subject,$comment);
                
                $_SESSION['auth_key'] = $auth_key;
                $_SESSION['auth_email'] = $email;
                $_SESSION['auth_key_limit'] = time()+(60*30);
                debug('セッション変数の中身：'.print_r($_SESSION,true));
                
                header("Location:passRemindRecieve.php");
             
            }elseif($stmt && !array_shift($result)){
                debug('DBに登録されていないアドレスです');
                $err_msg['email'] = MSG12;
            
            }else{
                debug('クエリに失敗しました');
                $err_msg['common'] = MSG06;
            }
                
            }catch (Exception $e){
            error_log('エラー発生：'.$e->getMessege());
            $err_msg['common'] = MSG06;
            }
        }
        
    }   


?>

<?php
$siteTitle='パスワード再発行';
require('head.php');
?>

<body>
    
    <!-- メニュー　-->
    <?php
    require('header.php');
    ?>
    
    <!-- メインコンテンツ　-->
    <div id="contents" class="site-width">
        
        <!-- Main -->
        <div class="form-container">
        <form action="" method="post" class="form3" style="height:400px;">
            <p style="margin-top:10px;">登録しているメールアドレスを入力して下さい。<br>パスワード再発行用のURLと認証キーをお送り致します。
            </p>
            <div class="area-msg">
                <?php echo getErrMsg('common');?>
            </div>
            <label class="<?php echo getErrStyle('email'); ?>">
                <span>E-MAIL</span>
                <input type="text" name="email" value="<?php echo getFormData('email'); ?>">
            </label>
            <div class="area-msg">
                <?php echo getErrMsg('email'); ?>
            </div>
            <div class="btn-container">
                <input type="submit" class="btn btn-mid2" value="送信">
            </div>
        </form>
        </div>
    </div>
    
    <!-- footer -->
    <?php
    require('footer.php');
    ?>


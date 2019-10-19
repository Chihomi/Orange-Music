<?php
//ini_set('display_errors',1);
//ini_set('error_reporting',E_ALL);

//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　パスワード変更ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//===================
//画面処理
//===================
//DBからユーザーデータを取得
$userData = getUser($_SESSION['user_id']);
debug('取得したユーザー情報：'.print_r($userData,true));

//post送信されていた場合
if(!empty($_POST)){
    debug('POST送信があります');
    debug('POST情報：'.print_r($_POST,true));
    
    $pass_old = $_POST['pass_old'];
    $pass_new = $_POST['pass_new'];
    $pass_new_re = $_POST['pass_new_re'];
    
    validRequired($pass_old,'pass_old');
    validRequired($pass_new,'pass_new');
    validRequired($pass_new_re,'pass_new_re');
    
    if(empty($err_msg)){
        debug('未入力チェックOK');
        validPass($pass_old,'pass_old');
        validPass($pass_new,'pass_new');
        
        
        if(!password_verify($pass_old,$userData['password'])){
            $err_msg['pass_old'] = MSG10;
        }
        
        //新しいパスワードと古いパスワードが同じかチェック
        if($pass_old === $pass_new){
            $err_msg['pass_new'] = MSG11;
        }
        
        validMatch($pass_new,$pass_new_re,'pass_new_re');
        
        if(empty($err_msg)){
            debug('パリデーションOK');
            
            try{
                
                $dbh = dbConnect();
                
                $sql = 'UPDATE users SET password = :pass WHERE id = :u_id';
                
                $data = array(':pass' => password_hash($pass_new,PASSWORD_DEFAULT), ':u_id' => $_SESSION['user_id']);
                
                $stmt = queryPost($dbh,$sql,$data);
                
                if($stmt){
                    $_SESSION['msg_success'] = SUC01;
                    
                    //メールを送信
                    $username = ($userData['username']) ? $userData[('username')] : '名無し';
                    $from = 'chihomihikaru@gmail.com';
                    $to = $userData['email'];
                    $subject = 'パスワード変更通知 | Orange Music';
                    $comment = <<<EOT
{$userData['username']} さん
パスワードが変更されました。

/////////////////////////////////////
Orange Music 
カスタマーセンター
URL http://
E-mail info@sakura.com
/////////////////////////////////////
EOT;
                    
                    
                    sendMail($from,$to,$subject,$comment);
                    
                    header('Location:mypage.php');
                }
                
            }catch (Exception $e){
                error_log('エラー発生：' .$e->getMessage());
                $err_msg['common'] = MSG06;
            }
        }
    }
}

?>

<?php
$siteTitle ='パスワード変更';
require('head.php');
?>

 <body>
     <!-- メニュー　-->
     <?php
     require('header.php');
     ?>
     
     <!-- メインコンテンツ　-->
     <div id="contents" class="site-width">
         <h1 class="page-title">パスワード変更</h1>
         
         <!-- Main -->
         <section id="main">
             <div class="form-container">
                 <form action="" method="post" class="form3">
                     <div class="area-msg">
                         <?php
                         echo getErrMsg('common');
                         ?>
                     </div>
                     <label class="<?php echo getErrStyle('pass_old'); ?>">
                         <span>現在のパスワード</span>
                         <input type="password" name="pass_old" value="<?php echo getFormData('pass_old'); ?>">
                     </label>
                     <div class="area-msg">
                         <?php echo getErrMsg('pass_old'); ?>
                     </div>
                     <label class="<?php echo getErrStyle('pass_new'); ?>">
                         <span>新しいパスワード</span>
                         <input type="password" name="pass_new" value="<?php echo getFormData('pass_new'); ?>">
                     </label>
                     <div class="area-msg">
                         <?php echo getErrMsg('pass_new'); ?>
                     </div>
                     <label class="<?php echo getErrStyle('pass_new_re'); ?>">
                         <span>新しいパスワード（再入力）</span>
                         <input type="password" name="pass_new_re" value="<?php echo getFormData('pass_new_re'); ?>">
                     </label>
                     <div class="area-msg">
                         <?php echo getErrMsg('pass_new_re'); ?>
                     </div>
                     <div class="btn-container">
                         <input type="submit" class="btn btn-mid2" value="変更">
                     </div>
                 </form>
             </div>
         </section>
     </div> 
     
     <!-- footer -->
     <?php
     require('footer.php');
     ?>
     


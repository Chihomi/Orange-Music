<?php
ini_set('display_errors',1);
ini_set('error_reporting',E_ALL);

//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ユーザー登録　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//POST送信されていた場合
if(!empty($_POST)){
    
    $name = $_POST['username'];
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_re = $_POST['pass_re'];
    
    //未入力チェック
    validRequired($name,'username');
    validRequired($email,'email');
    validRequired($pass,'pass');
    validRequired($pass_re,'pass_re');
    
    if(empty($err_msg)){
        
        validEmail($email,'email');
        validMaxLen($email,'email');
        validEmailDup($email);
        
        validMaxLen($pass,'pass');
        validMinLen($pass,'pass');
        validHalf($pass,'pass');
        
        if(empty($err_msg)){
            
            validMatch($pass,$pass_re,'pass_re');
            
            if(empty($err_msg)){
                
                try{
                    $dbh = dbConnect();
                    $sql = 'INSERT INTO users (username,email,password,login_time,create_date) VALUES (:username,:email,:pass,:login_time,:create_date)';
                    $data = array(':username' => $name, ':email' => $email, ':pass' => password_hash($pass, PASSWORD_DEFAULT),':login_time' => date('Y-m-d H:i:s'), ':create_date' => date('Y-m-d H:i:s'));
                    
                    $stmt = queryPost($dbh,$sql,$data);
                    
                    //クエリ成功の場合
                    if($stmt){
                        $sesLimit = 60*60;
                        //最終ログイン日時を現在日時に変更
                        $_SESSION['login_date'] = time();
                        $_SESSION['login_limit'] = $sesLimit;
                        //ユーザーIDを格納
                        $_SESSION['user_id'] = $dbh->lastInsertId();
                        
                        debug('セッション変数の中身：'.print_r($_SESSION,true));
                        header("Location:mypage.php");
                    }
                    
                }catch (Exception $e){
                    error_log('エラー発生：'.$e->gerMessege());
                    $err_msg['common'] = MSG06;
                }
                
            }
        }
        
    }
}

?>
<?php
$siteTitle = 'ユーザー登録';
require('head.php');
?>

<body>
    
    <!-- ヘッダー　-->
    <?php
    require('header.php');
    ?>
    
    <!-- メインコンテンツ -->
    <div id="contents" class="site-width">
        
        <!-- Main -->
        <section class="form-container">
            
            <form action="" method="post" class="form3" style="height:800px;">
                <h2 class="title">登録</h2>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['common'])) echo $err_msg['common']; 
                    ?> 
                </div>
                <label class="<?php if(!empty($err_msg['username'])) echo 'err'; ?>">
                    <span>NAME</span>
                    <input type="text" name="username" value="<?php if(!empty($_POST['username'])) echo $_POST['username']; ?>">
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['username'])) echo $err_msg['username'];
                    ?>
                </div>
                <label class="<?php if(!empty($err_msg['email'])) echo 'err';?>">
                    <span>E-MAIL</span>
                    <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['email'])) echo $err_msg['email'];
                    ?>
                </div>
                <label class="<?php if(!empty($err_msg['pass'])) echo 'err';?>">
                    <span>PASSWORD</span>
                    <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['pass'])) echo $err_msg['pass'];
                    ?>
                </div>
                <label class="<?php if(!empty($err_msg['pass_re'])) echo 'err'; ?>">
                    <span>PASSWORD（再入力）</span>
                    <input type="password" name="pass_re" value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re']; ?>">
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['pass_re'])) echo $err_msg['pass_re']; 
                    ?>
                </div>
                <div class="btn-container">
                    <input type="submit" class="btn btn-mid2" value="登録" style="margin-top: 90px;">
                </div>
                
            </form>
        </section>
    </div>
    
    <!-- footer -->
    <?php
    require('footer.php');
    ?>

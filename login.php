<?php


//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ログイン　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//=================
//ログイン画面
//=================
//POST送信されていた場合
if(!empty($_POST)){
    debug('POST送信あります');
    
    
    //変数にユーザー情報を代入
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_save = (!empty($_POST['pass-save'])) ? true : false;
    
    validEmail($email,'email');
    validMaxLen($email,'email');
    
    validMaxLen($pass,'pass');
    validMinLen($pass,'pass');
    validHalf($pass,'pass');
    
    validRequired($email,'email');
    validRequired($pass,'pass');
    
    if(empty($err_msg)){
        debug('バリデーションOKです');
        
        //例外処理
        try{
            $dbh = dbConnect();
            $sql = 'SELECT password, id FROM users WHERE email = :email AND delete_flg = 0';
            $data = array(':email' => $email);
            
            $stmt = queryPost($dbh,$sql,$data);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            debug('クエリの中身：'.print_r($result,true));
            
            //パスワード照合
            if(!empty($result) && password_verify($pass,array_shift($result))){
                debug('パスワードがマッチしました');
                
                //ログイン有効期限（デフォルトを1時間とする）
                $sesLimit = 60*60;
                //最終ログイン日時を現在日時にする
                $_SESSION['login_date'] = time();
                
                //ログイン保持にチェックがある場合
                if($pass_save){
                    debug('ログイン保持にチェックがあります');
                    //ログイン有効期限を30日にしてセット
                    $_SESSION['login_limit'] = $sesLimit*24*30;
                }else{
                    debug('ログイン保持にチェックがありません');
                    $_SESSION['login_limit'] = $sesLimit;
                }
                //ユーザーIDを格納
                $_SESSION['user_id'] = $result['id'];
                
                debug('セッション変数の中身：'.print_r($_SESSION,true));
                debug('マイページへ遷移します');
                header("Location:login.php");
            }else{
                debug('パスワードがアンマッチです');
                $err_msg['common'] = MSG08;
            }
        }catch (Exception $e){
            error_log('エラー発生：'.$e->getMessage());
            $err_msg['common'] = MSG06;
        }
    }
}
debug('画面表示処理終了　<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
$siteTitle = 'ログイン';
require('head.php');
?>
    
    
    <body>
        <!-- ヘッダー -->
        <?php
        require('header.php');
        ?>
        
        <p id="js-show-msg" style="display:none;" class="msg-slide">
            <?php echo getSessionFlash('msg_success'); ?>
        </p>

        <!-- メインコンテンツ -->
        <div id="contents" class="site-width">
            
            <!-- Main -->
            <section id="main">
                <div class="form-container">
                <form action="" method="post" class="form3">
                    <h2 class="title">ログイン</h2>
                    <div class="area-msg">
                    <?php
                    echo getErrMsg('common');
                    ?>
                    </div>
                    <label class="<?php echo getErrStyle('email'); ?>">
                        <span>E-MAIL</span>
                        <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email'] ; ?>" >
                    </label>
                    <div class="area-msg">
                        <?php
                        echo getErrMsg('email'); 
                        ?>
                    </div>
                    <label class="<?php echo getErrStyle('pass'); ?>">
                        <span>PASSWORD</span>
                        <input type="password" name="pass" value="<?php if(!empty($_POST['psaa'])) echo $_POST['pass']; ?>"　>
                    </label>
                    <div class="area-msg">
                        <?php
                        echo getErrMsg('pass'); 
                        ?>
                    </div>
                    <label class="checkbox">
                        <input type="checkbox" name="pass-save">次回ログインを省略する
                    </label>
                    <div class="btn-container">
                        <input type="submit" class="btn btn-mid2" value="ログイン">
                    </div>
                    パスワードを忘れた方は<a href="passRemindSend.php">こちら</a>
                
                </form>
                </div>
            </section>
        </div>
        
        <!-- footer -->
        <?php
        require('footer.php');
        ?>
<?php
ini_set('display_errors',1);
ini_set('error_reporting',E_ALL);
 
//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　プロフィール編集ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//=================
//画面処理
//=================
//DBからユーザーデータを取得
$dbFormData = getUser($_SESSION['user_id']);
debug('取得したユーザー情報:'.print_r($dbFormData,true));

//post送信されていた場合
if(!empty($_POST)){
    debug('POST送信があります');
    debug('POST情報：'.print_r($_POST,true));
    debug('FILE情報：'.print_r($_FILES,true));
    
    //変数にユーザー情報を代入
    $username = $_POST['username'];
    $email = $_POST['email'];
    $preference = $_POST['preference'];
    $twitter = $_POST['twitter'];
    $instagram = $_POST['instagram'];
    $facebook = $_POST['facebook'];
    //画像をアップロードしバスを格納
    $pic = (!empty($_FILES['pic']['name'])) ? uploadImg($_FILES['pic'],'pic') : '';
    $pic = (empty($pic) && !empty($dbFormData['pic'])) ? $dbFormData['pic'] : $pic;
    
    //DBの情報と入力情報が異なる場合にバリデーションを行う
    if($dbFormData['username'] !== $username ){
        validMaxLen($username,'username');
        validRequired($username,'username');
    }
    if($dbFormData['email'] !== $email){
        validRequired($email,'email');
        validEmail($email,'email');
        validMaxLen($email,'email');
        validEmailDup($email);
    }
    if($dbFormData['preference'] !== $preference){
        validMaxLen($preference,'preference');
    }
    
    if(empty($err_msg)){
        debug('バリデーションOKです');
        
        try{
            
            $dbh = dbConnect();
            
            $sql = 'UPDATE users SET username = :u_name, email = :email, preference = :preference, twitter = :twitter, instagram = :instagram, facebook = :facebook, pic = :pic WHERE id = :u_id';
            
            $data = array(':u_name' => $username, ':email' => $email, ':preference' => $preference, ':twitter' => $twitter, ':instagram' => $instagram, ':facebook' => $facebook, ':pic' => $pic, 'u_id' => $dbFormData['id']);
            
            $stmt = queryPost($dbh,$sql,$data);
            
            if($stmt){
                $_SESSION['msg_success'] = SUC02;
                debug('マイページへ遷移します');
                header("Location:mypage.php");
            }
            
        }catch (Exception $e){
            error_log('エラー発生：'.$e->getMessage());
            $err_msg['common'] = MSG06;
        }
    }
}
debug('画面表示処理終了<<<<<<<<<<<<<');
?>

<?php
$siteTitle = 'プロフィール編集';
require('head.php');
?>

<body>
    
    <!-- メニュー -->
    <?php
    require('header.php');
    ?>
    
    <!-- メインコンテンツ -->
    <div id="contents" class="site-width">
        <h1 class="page-title">プロフィール</h1>
        <!-- Main -->
        <section id="main">
            <div class="form-container">
                <form action="" method="post" class="form-pe" enctype="multipart/form-data">
                 <div class="form1">
                     <span>プロフィール画像</span>
                     <label class="area-drop <?php if(!empty($err_msg['pic'])) echo 'err'; ?>" style="width:200px;height:200px;">
                         <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                         <input type="file" name="pic" class="input-file" style="width:200px;height:200px;">
                         <img src="<?php echo getFormData('pic');?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic'))) echo 'display:none;' ?>">
                         アイコンを変更
                     </label>
                    </div>
                 <div class="form2">
                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg['common'])) echo $err_msg['common'];
                        ?>
                    </div>
                    <label class="<?php if(!empty($err_msg['username'])) echo 'err'; ?>">
                        <span style="margin-top:0;">NAME</span>
                        <input type="text" name="username" value="<?php echo getFormData('username'); ?>">
                    </label>
                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg['username'])) echo $err_msg['username'];
                        ?>
                    </div>
                    <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
                        <span>E-MAIL</span>
                        <input type="text" name="email" value="<?php echo getFormData('email'); ?>">
                    </label>
                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg['email'])) echo $err_msg['email'];
                        ?>
                    </div>
                    <label class="<?php echo getErrStyle('twitter'); ?>">
                        <span>Twitter</span>
                        <input type="text" name="twitter" value="<?php echo getFormData('twitter'); ?>">
                    </label>
                    <div class="area-msg">
                        <?php echo getErrMsg('twitter'); ?>
                    </div>
                    <label class="<?php echo getErrStyle('instagram'); ?>">
                        <span>Instagram</span>
                        <input type="text" name="instagram" value="<?php echo getFormData('instagram'); ?>">
                    </label>
                    <div class="area-msg">
                        <?php echo getErrMsg('instagram'); ?>
                    </div>
                    <label class="<?php echo getErrStyle('facebook'); ?>">
                        <span>Facebook</span>
                        <input type="text" name="facebook" value="<?php echo getFormData('facebook'); ?>">
                    </label>
                    <div class="area-msg">
                        <?php echo getErrMsg('twitter'); ?>
                    </div>
                    <label class="<?php if(!empty($err_msg['preference'])) echo 'err' ; ?>">
                        <span>好きな音楽・アーティスト</span>
                        <textarea name="preference" id="js-count" cols="35" rows="10" style="height:150px;"><?php echo getFormData('preference'); ?></textarea>
                    </label>
                        <p class="counter-text"><a id="js-count-view" style="color:#757575;">0</a>/250文字</p>
                        <div class="area-msg">
                            <?php
                            if(!empty($err_msg['preference'])) echo $err_msg['preference'];
                            ?>
                        </div>
                    
                    <div class="btn-container">
                        <input type="submit" class="btn btn-mid" value="更新" style="margin-top:60px;font-size:16px;">
                    </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
    
    <!-- footer -->
    <?php
    require('footer.php');
    ?>
    
</body>
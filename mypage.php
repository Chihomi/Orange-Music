<?php

ini_set('display_errors',1);
ini_set('error_reporting',E_ALL);
//共通変数・関数ファイルを読込み
require('function.php');



debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　マイページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//================================
// 画面処理
//================================
//ログイン認証
require('auth.php');

//画面表示用データ取得
//================================
$u_id = $_SESSION['user_id'];

$dbFormData = getUser($u_id);

$postData = getMyPost($u_id);


if(!empty($_POST)){
    debug('削除します');
    
    try{
        
        $dbh = dbConnect();
        
        $sql = 'UPDATE post SET delete_flg = 1 WHERE id = :p_id';
        
        $data = array(':p_id' => $_POST['id']);
        
        $stmt = queryPost($dbh,$sql,$data);
        
        if($stmt){
            $_SESSION['msg_success'] = SUC06;
            debug('マイページに遷移します');
            header("Location:mypage.php");
        }
    }catch (Exception $e){
        error_log('エラー発生：' .$e->getMessage());
    }
}
?>
<?php
$siteTitle = 'マイページ';
require('head.php'); 
?>

 <body>
     
     <!-- メニュー -->
     <?php
     require('header.php');
     ?>
     
     <p id="js-show-msg" style="display:none;" class="msg-slide">
         <?php echo getSessionFlash('msg_success'); ?>
     </p>
      <!-- Main -->
     <section id="main">
         
         <!-- メインコンテンツ　-->
         <div id="contents" class="site-width">
             <h1 class="page-title">マイページ</h1>
             
             <!-- サイドバー -->
             <section id="sideber2">
                 <div class="avatar">
                    <img src="<?php echo sanitize($dbFormData['pic']); ?>" alt="" class="avatar">
                    <div class="avatar-name"><?php echo sanitize($dbFormData['username']); ?></div>
                    <div class="avatarprof"><?php echo sanitize($dbFormData['preference']); ?></div>
                </div>
                <a href="profEdit.php">プロフィール変更</a>
                <a href="passEdit.php">パスワード変更</a>
                 
             </section>
             
             <!-- マイページメイン　-->
             <section class="list list-table">
                <h2 class="list-title" style="color:#757575;">投稿一覧</h2>
                <table class="table">
                    <tbody>
                        <?php
                        if(!empty($postData)){
                            foreach($postData as $key => $val){
                        ?>
                           <tr>
                               <td><a href="postDetail.php?p_id=<?php echo sanitize($val['id']); ?>"><?php echo sanitize($val['title']); ?></a>&nbsp;&frasl;&nbsp;<?php echo sanitize($val['artist']); ?></td>
                               <td><a href="postMusic.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&p_id='.$val['id'] : '?p_id='.$val['id']; ?>" class="edit">編集</a></td>
                               <td>
                               <form action="" method="post">
                               <input type="submit" value="削除">
                               <input type="hidden" name="id" value="<?php echo sanitize($val['id']); ?>">
                               </form></td>
                           </tr>
                           <?php
                            }                          
                        }
                        ?>
                    </tbody>
                </table>
                
                 
             </section>
             
         </div>
     </section>
</body>

    <!-- footer -->
    <?php
      require('footer.php'); 
    ?>
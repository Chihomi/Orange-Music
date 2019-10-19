<?php

ini_set('dicplay_errors','on');
ini_set('error_reporting',E_ALL);

require('function.php');

//ログイン認証
require('auth.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「');
debug(' 投稿詳細/メッセージページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//=====================
//画面処理
//=====================

//画面表示用データ取得
//=====================
//投稿のGETパラメータを取得
$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';


//DBから投稿データを取得
$viewPostData = getPostOne($p_id);
$viewMsgData = (!empty($p_id)) ? getMsgsAndBord($p_id) : '';
debug('データv：'.print_r($viewMsgData,true));
//$gestUserInfo = getUser($viewMsgData[1]['from_user']);

//パラメータに不正な値が入っていないかチェック
if(empty($viewPostData)){
    error_log('エラーが発生：指定ページに不正な値が入りました。');
    //header("Location:title.php");
}
debug('取得したDBデータ：'.print_r($viewPostData,true));

//if(empty($viewMsgData)){
    //error_log('エラーが発生：指定ページに不正な値が入りました!');
    //header("Location:title.php");
//}
//debug('取得したDBデータ：'.print_r($viewMsgData,true));


//post送信されていた場合
if(!empty($_POST)){
    debug('POST送信があります');
    require('auth.php');
    
    $msg = (isset($_POST['msg'])) ? $_POST['msg'] : '';
    
    validMaxLen($msg,'msg');
    
    validRequired($msg,'msg');
    
    if(empty($err_msg)){
        debug('バリデーションOKです');
        
            try{
                
                $dbh = dbConnect();
                
                $sql = 'INSERT INTO message (post_id,send_date,from_user,msg,create_date) VALUES (:p_id,:send_date, :from_user, :msg, :date)';
                
                $data = array(':p_id' => $p_id, ':send_date' => date('Y-m-d h:i:s'), 'from_user' => $_SESSION['user_id'], ':msg' => $_POST['msg'], ':date' => date('Y-m-d h:i:s'));
                
                $stmt = queryPost($dbh,$sql,$data);
                
                if($stmt){
                    $_POST = array();
                    debug('コメントを投稿しました');
                    header('Location:' .$_SERVER['PHP_SELF'] .'?p_id='.$p_id);
                    
                }
                
            }catch (Exception $e){
                error_log('エラー発生：'.$e->getMessage());
            }
        
    }
}

debug('画面表示処理終了 <<<<<<<<<<<<<');

?>

<?php
$siteTitle = '投稿詳細';
require('head.php');
?>


<body>
   
   <!-- メニュー　 -->
    <?php
    require('header.php');
    ?>
    
    <!-- メインコンテンツ　-->
    <div id="contents" class="site-width">
        
        <!-- Main -->
        <section id="main">
           <div class="panel-main">
            <div class="panel-img">
               <div class="panel-head">
                   <i class="fas fa-music fa-9x" style="margin:56px;color:#757575;"></i></div>
                <h1 class="panel-title"><?php echo sanitize($viewPostData['title']); ?></h1>
                <h1 class="panel-artist"><?php echo sanitize($viewPostData['artist']); ?></h1>
                <div class="panel-forward">
                    <i class="fas fa-forward fa-2x fa-rotate-180" style="margin-left:56px;float:left"></i>
                    <i class="fas fa-pause fa-2x" style="float:left;margin:0 58px ;"></i>
                    <i class="fas fa-forward fa-2x" style="margin-right:56px;float:left;"></i>
                </div>
            </div>
            </div>
            <div class="post-detail">
                <a href="userprof.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&u_id='.$viewPostData['user_id'] : '?u_id='.$viewPostData['user_id']; ?>" class="avatar" style="color:#757575;">
                    <img src="<?php echo sanitize($viewPostData['pic']); ?>" alt="" class="avatar" style="float:left;">
                    <div class="avatarname"><?php echo sanitize($viewPostData['username']); ?></div>
                </a>
                <div class="comment">
                    <p><?php echo sanitize($viewPostData['comment']); ?></p>
                </div>
                <div class="love">
                    <i class="fas fa-heart icn-like js-click-like fa-4x <?php if(isLike($_SESSION['user_id'], $viewPostData['id'])){ echo 'active'; } ?>" aria-hidden="ture" data-postid="<?php echo sanitize($viewPostData['id']); ?>"></i>
                    <div class="love-p">
                    <p class="font">お気に入りに登録しよう</p>
                    </div>
                </div>
            </div>
            
            <section id="comment">
            <h1 class="comment">コメント</h1>
            <div class="area-bord" id="js-scroll-bottom">
              
               <?php
                if(!empty($viewMsgData)){
                    foreach($viewMsgData as $key => $val){
                        if(!empty($val['from_user'])){
                ?>
                       <div class="msg-cnt msg-gest">
                           
                               <img src="<?php echo sanitize(showImg($val['pic'])); ?>" alt="" class="avatar" style="margin-top:15px;float:left;">
                           
                           <div class="comment-area">
                           
                           <p class="msg-inrTxt">
                               
                               <?php echo sanitize($val['msg']); ?>
                           </p>
                           </div>
                           <div class="date" style="font-size:0.7em;"><?php echo sanitize($val['username']); ?>&nbsp;&nbsp;&nbsp;<?php echo sanitize($val['send_date']); ?></div>
                           
                       </div>
                       <?php
                        }
                       ?>
                       
                       <?php
                        }
                    
                }else{
                    ?>
                    
                    <p style="color:#757575;">コメントはありません</p>
                <?php
                }
                ?>
                    
                
                
            </div>
            <div class="area-send-msg">
                <form action="" method="post">
                    <textarea name="msg"  cols="85" rows="3"></textarea>
                    <input type="submit" value="送信" class="btn btn-send">
                </form>
            </div>
            </section> 
            <div class="item-left"><a href="index.php<?php echo appendGetParam(array('p_id')); ?>">&lt; HOMEへ戻る</a></div> 
        </section>
        
        <script src="js/jquery-3.4.1.min.js"></script>
        
        <script>
            $(function(){
                //scrollHeightは要素のスクロールビューの高さを取得するもの
                $('#js-scroll-bottom').animate({scrollTop: $('#js-scroll-bottom')[0].scrollHeight},'fast');
            });
        </script>
        
    </div>
    
    <!-- footer -->
    <?php
    require('footer.php');
    ?>
</body>
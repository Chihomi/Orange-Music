<?php
ini_set('display_error','On');
ini_set('error_reporting',E_ALL);


require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「');
debug(' ユーザー情報　');
debug('「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//=================
////画面処理
//==================

//画面表示用データ取得
//==================

$postUser = getUser($_GET['u_id']);

$dbPostData = getPostListAll($_GET['u_id']);
debug('取得したデータf：'.print_r($dbPostData,true));



debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<');

?>
<?php
$siteTitle = $postUser['username'].'さんのプロフィール';
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
        <section id="main3">
           <div class="avatar">
               <p><img src="<?php echo sanitize($postUser['pic']); ?>" alt="" class="avatar"></p>
                    <div class="avatar-name"><?php echo sanitize($postUser['username']); ?></div>
                    <div class="link">
                    <a href="<?php echo sanitize($postUser['twitter']); ?>">
                        <i class="fab fa-twitter fa-3x" style="color:#009dfb;"></i></a>
                    <i class="fab fa-instagram fa-3x" style="color:#f00075;"></i>
                    <i class="fab fa-facebook fa-3x" style="color:#0869fb;"></i>
                    </div>
                    <div class="avatarprof" style="text-align:center;"><?php echo sanitize($postUser['preference']); ?></div>
                    
                </div>
                <div class="panelpog">
            <div class="panel-list">
                <?php
                foreach($dbPostData as $key => $val):
                ?>
                <a href="postDetail.php?p_id=<?php echo $val['id']; ?>" class="panel">
                    <div class="panel-head">
                        <i class="fas fa-music fa-6x" style="margin:37px;color:#757575;"></i>
                    </div>
                    <div class="panel-body">
                        <h1 class="panel-title"><?php echo sanitize($val['title']); ?></h1>
                        <h1 class="panel-artist"><?php echo sanitize($val['artist']); ?></h1>
                        
                    </div>
                </a>
                <?php
                endforeach;
                ?>
            </div>
            </div>
        </section>
    </div>
    
    <!-- footer -->
    <?php
    require('footer.php');
    ?>
    
</body>
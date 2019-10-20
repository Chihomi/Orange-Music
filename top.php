<?php

ini_set('display_errors','On');
ini_set('error_reporting',E_ALL);

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「');
debug(' トップページ　');
debug('「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//==================
//画面処理
//==================

//画面表示用データ取得
//==================
//カレントページのGETパラメータを取得
$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1;

$genre = (!empty($_GET['g_id'])) ? $_GET['g_id'] : '';

$artist = (!empty($_GET['artist'])) ? $_GET['artist'] : '';
//パラメータに不正な値が入っているかチェック
if(!preg_match('/^[0-9]+$/',$currentPageNum)){
    debug('指定ページに不正な値が入りました');
    header("Location:top.php");
}
//表示件数
$listSpan = 9;
//現在の表示レコード先頭を算出
$currentMinNum = (($currentPageNum -1)*$listSpan);

//DBからポストデータを取得
$dbPostData = getPostList($currentMinNum,$genre,$artist);
debug('情報：'.print_r($dbPostData,true));
//DBからジャンルデータを取得
$dbGenreData = getGenre();

debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<');
?>

<?php
$siteTitle = 'HOME';
require('head.php');
?>

<body>
    
    <!-- ヘッダー　-->
    <?php
    require('header.php');
    ?>
    
    <!-- メニュー　-->
    <p id="js-show-msg" style="display:none;" class="msg-slide">
         <?php echo getSessionFlash('msg_success'); ?>
     </p>
    
    <!-- メインコンテンツ　-->
    <div id="contents" class="site-width">
       <h1 class="page-title2">掲示板</h1>
        <p class="page-comment">お気に入りの曲を投稿する掲示板です</p>
        
        
        <!-- サイド　-->
        <section id="sideber">
            <div class="hidden_box" style="margin-top:15px;">
                <label for="label1">絞り込み</label>
                <input type="checkbox" id="label1">
                <div class="hidden_show">
                    <!--ここから非表示-->
                     <section id="search">
                         <form action="" method="get">
                             <h1>ジャンル</h1>
                             <div class="selectbox">
                                 <span class="icn_select"></span>
                                 <select name="g_id" id="">
                                     <option value="0" <?php if(getFormData('g_id',true) == 0){ echo 'selected';} ?>>すべて</option>
                                     <?php
                                      foreach($dbGenreData as $key => $val){
                                      ?>
                                     <option value="<?php echo $val['id']?>" <?php if(getFormData('g_id',true) === $val['id']){ echo 'selected'; }?>><?php echo $val['name']; ?>
                                     </option>
                                     <?php
                                      }
                                     ?>
                                 </select>
                             </div>
                             <h1>アーティスト</h1>
                             <input  type="text" name="artist" value="<?php if(!empty($_GET['artist'])) echo $_GET['artist']; ?>">
                             <input type="submit" value="検索">
                         </form>
                    </section>
                    <!--ここまで-->
                </div>
            </div>
            <div class="post_in">
                <label><a href="postMusic.php">投稿する</a></label>
            </div>
        </section>
        
        <!-- Main -->
        <section id="main2">
            <div class="search-title">
                
                <div class="search-right">
                    <span class="num"><?php echo (!empty($dbPostData['data'])) ? $currentMinNum+1 : 0; ?></span> - <span class="num"><?php echo $currentMinNum+count($dbPostData['data']); ?></span>件 / <span class="num"><?php echo sanitize($dbPostData['total']); ?></span>件中
                </div>
            </div>
            <div class="panel-list" style="overflow:hidden;">
                <?php
                  foreach($dbPostData['data'] as $key => $val):
                ?>
                <a href="postDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&p_id='.$val['id'] : '?p_id='.$val['id']; ?>" class="panel">
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
            <div class="pagination">
                <ul class="pagination-list">
                    <?php
                    $pageColNum = 3; //表示項目数
                    $totalPageNum = $dbPostData['total_page'];
                    //現在のページが総ページ数と同じかつ総ページ数が表示項目以上なら左に2つだす
                    if($currentPageNum == $totalPageNum && $totalPageNum >= $pageColNum){
                        $minPageNum = $currentPageNum -2;
                        $maxPageNum = $currentPageNum;
                    //現ページが1の場合は左に何もださない。右に2つ    
                    }elseif($currentPageNum == 1 && $totalPageNum >= $pageColNum){
                        $minPageNum = $currentPageNum;
                        $maxPageNum = 3;
                    //総ページ数が表示項目数より少ない場合
                    }elseif($totalPageNum < $pageColNum){
                        $minPageNum = 1;
                        $maxPageNum = $totalPageNum;
                    }else{
                        $minPageNum = $currentPageNum -1;
                        $maxPageNum = $currentPageNum +1;
                    }
                    ?>
                    <?php if($currentPageNum != 1): ?>
                    <li class="list-item"><a href="?p=1<?php echo (!empty(appendGetParam2())) ? appendGetParam2() : ''; ?>">&lt;</a></li>
                    <?php endif; ?>
                    <?php
                     for($i = $minPageNum; $i <= $maxPageNum; $i++):
                    ?>
                    <li class="list-item <?php if($currentPageNum == $i) echo ' active'; ?>"><a href="?p=<?php echo $i; ?><?php echo (!empty(appendGetParam2())) ? appendGetParam2() : ''; ?>"><?php echo $i; ?></a></li>
                    <?php 
                     endfor;
                    ?>
                    <?php if($currentPageNum != $maxPageNum && $maxPageNum > 1): ?>
                    <li class="list-item"><a href="?p=<?php echo $maxPageNum; ?><?php echo (!empty(appendGetParam2())) ? appendGetParam2() : ''; ?>">&gt;</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            
            
            
        </section>
    </div>
    
    <!-- footer -->
    <?php
    require('footer.php');
    ?>
</body>
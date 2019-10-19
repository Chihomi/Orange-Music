<?php
ini_set('display_errors','On');
ini_set('error_repotting',E_ALL);


require('function.php');


debug('「「「「「「「「「「「「「「「「「「「「');
debug('「　投稿・編集画面　');
debug('「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

require('auth.php');

//==================
//画面処理
//==================

//画面表示用データ取得
//=================
//GETデータを格納
$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
//DBから投稿データを取得
$dbFormData = (!empty($p_id)) ? getPost($_SESSION['user_id'],$p_id) : '';
//新規か編集かの判断
$edit_flg = (empty($dbFormData)) ? false : true;
//DBからジャンルデータを取得
$dbGenreData = getGenre();
debug('投稿ID：'.$p_id);
debug('フォーム用DBデータ：'.print_r($dbFormData,true));
debug('ジャンルデータ：'.print_r($dbGenreData,true));

//パラメータ改ざんチェック
//========================
if(!empty($p_id) && empty($dbFormData)){
    debug('GETパラメータの投稿IDが違います');
    header("Location:mypage.php");
}

//post送信処理
//=======================
if(!empty($_POST)){
    debug('post送信があります');
    debug('POST情報：'.print_r($_POST,true));
    
    $title = $_POST['title'];
    $artist = $_POST['artist'];
    $genre = $_POST['genre_id'];
    $comment = $_POST['comment'];
    
    //更新の場合
    if(!empty($dbFormData)){
        if($dbFormData['title'] !== $title){
            validRequired($title,'title');
            validMaxLen($title,'title');
            if(empty($err_msg['title'])){
                if($dbFormData['artist'] !== $artist){
                    validRequired($artist,'artist');
                    validMaxLen($artist,'artist');
                    if(empty($err_msg['artist'])){
                        if($dbFormData['genre_id'] !== $genre){
                            validSelect($genre,'genre_id');
                            if(empty($err_msg['genre_id'])){
                                if($dbFormData['comment'] !== $comment){
                                    validRequired($comment,'comment');
                                    validMaxLen($comment,'comment',250);
                                }
                            }
                        }
                    }
                }
            }
        }
                         
    //投稿の場合 
    }else{
        validRequired($title,'title');
        validMaxLen($title,'title');
        if(empty($err_msg['title'])){
            validRequired($artist,'artist');
            validMaxLen($artist,'artist');
            if(empty($err_msg['artist'])){
                validSelect($genre,'genre_id');
                if(empty($err_msg['genre_id'])){
                    validRequired($comment,'comment');
                    validMaxLen($comment,'comment',250);
                }
            }
        }
        
        
    }


if(empty($err_msg)){
    debug('バリデーションOK');
    
    try{
        
        $dbh = dbConnect();
        
        //更新の場合
        if($edit_flg){
            debug('DB更新です');
            $sql = 'UPDATE post SET title = :title, artist = :artist, genre_id = :genre, comment = :comment WHERE user_id = :u_id AND id = :p_id';
            
            $data = array(':title' => $title,':artist' => $artist, ':genre' => $genre, ':comment' => $comment, ':u_id' => $_SESSION['user_id'], ':p_id' => $p_id);
        //投稿の場合 
        }else{
            debug('DB新規登録です');
            $sql = 'INSERT INTO post (title,artist,genre_id,comment,user_id,created_date) VALUES (:title,:artist,:genre,:comment,:u_id,:date)';
            
            $data = array(':title' => $title,':artist' => $artist, ':genre' => $genre, ':comment' => $comment, ':u_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s'));
        }
        debug('SQL:'.$sql);
        debug('流し込みデータ：'.print_r($data,true));
        
        $stmt = queryPost($dbh,$sql,$data);
        
        if($stmt){
            $_SESSION['msg_success'] = SUC05;
            debug('マイページに遷移します');
            header("Location:index.php");
        }
        
    }catch (Exception $e){
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG06;
    }
}
}
debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
$siteTitle = (!$edit_flg) ? '投稿' : '編集';
require('head.php');
?>


<body>
    
    <!-- メニュー　-->
    <?php
    require('header.php');
    ?>
    
    <!-- メインコンテンツ -->
    <div id="contents" class="site-width">
        
        <!-- Main -->
        <section id="main">
            <div class="form-container" style="height:800px;">
                <section class="copy">
                        <h1>あなたの好きな曲を投稿しよう</h1>
                    </section>
                <form action="" method="post" class="form4">
                   
                    <i class="fas fa-music fa-2x fa-pull-left"style="padding-top:35px;"></i>             
                    <label class="<?php echo getErrStyle('title'); ?>">
                        <input type="text" name="title" value="<?php echo getFormData('title'); ?>" placeholder="曲名">
                    </label>
                    
                    <i class="fas fa-headphones-alt fa-2x fa-pull-left" style="padding-top:28px;"></i>
                    <label class="<?php echo getErrStyle('artist'); ?>">
                        <input type="text" name="artist" value="<?php echo getFormData('artist'); ?>" placeholder="アーティスト">
                    </label>
                    
                    <i class="fas fa-compact-disc fa-2x fa-pull-left"style="padding-top:35px;"></i>
                    <div class="select">
                    <label class="<?php echo getErrStyle('genre_id'); ?>">
                        <select name="genre_id">
                            <option value="0" <?php if(getFormData('genre_id') == 0){echo 'selected';} ?>>ジャンル：選択してください</option>
                            <?php
                              foreach($dbGenreData as $key => $val){
                            ?>
                              <option value="<?php echo $val['id'] ?>" <?php if(getFormData('genre_id') == $val['id']){ echo 'selected';} ?> >
                                  <?php echo $val['name']; ?>
                            </option>
                            <?php
                              }
                            ?>
                        </select>
                    </label>
                    </div>
                    
                    <label class="<?php echo getErrStyle('comment'); ?>">
                        <textarea name="comment" id="js-count" cols="44" rows="10" placeholder="好きなところやおすすめポイント" style="margin-top:30px;"><?php echo getFormData('comment'); ?></textarea>
                    </label>
                    <p class="counter-text"><span id="js-count-view">0</span>/250文字</p>
                     <div class="area-msg">
                        <?php echo getErrMsg('common'); ?>
                    </div>
                    <div class="area-msg">
                        <?php echo getErrMsg('title'); ?>
                    </div>
                    
                    <div class="area-msg">
                        <?php echo getErrMsg('artist'); ?>
                    </div>
                    <div class="area-msg">
                        <?php echo getErrMsg('genre_id'); ?>
                    </div>
                    <div class="area-msg">
                        <?php echo getErrMsg('comment'); ?>
                    </div>
                    <div class="btn-container">
                        <input type="submit" class="btn btn-mid2" value="<?php echo (!$edit_flg) ? '投稿' : '更新'; ?>">
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
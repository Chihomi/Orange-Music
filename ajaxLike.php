<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　Ajax ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//========================
//Ajax処理
//========================

//postがあり、ユーザーIDがあり、ログインしている場合
if(isset($_POST['postId']) && isset($_SESSION['user_id']) && isLogin()){
    debug('post送信があります'.print_r($_POST,true));
    $p_id = $_POST['postId'];
    debug('商品ID:'.$p_id);
    
    //例外処理
    try {
        $dbh = dbConnect();
        
        $sql = 'SELECT * FROM favorite WHERE post_id = :p_id AND user_id = :u_id';
        $data = array(':p_id' => $p_id, ':u_id' => $_SESSION['user_id']);
        
        $stmt = queryPost($dbh,$sql,$data);
        $resultCount = $stmt->rowCount();
        debug($resultCount);
        
        //レコードが１件でもある場合
        if(!empty($resultCount)){
            $sql = 'DELETE FROM favorite WHERE post_id = :p_id AND user_id = :u_id';
            $data = array(':p_id' => $p_id, ':u_id' => $_SESSION['user_id']);
            $stmt = queryPost($dbh,$sql,$data);
        }else{
            $sql = 'INSERT INTO favorite (post_id,user_id,created_date) VALUES(:p_id,:u_id,:date)';
            $data = array(':p_id' => $p_id,':u_id'=> $_SESSION['user_id'],':date' => date('Y-m-d H:i:s'));
            $stmt = queryPost($dbh,$sql,$data);
        }
        
    }catch (Exception $e){
        error_log('エラー発生：'.$e->getMessege());
    }
}
debug('Ajax処理終了 <<<<<<<<<<<<<<<<<<<<<');
?>
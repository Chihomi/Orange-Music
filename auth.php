<?php

//===================
//ログイン認証・自動ログアウト
//===================
//ログインしている場合
if(!empty($_SESSION['login_date'])){
    debug('ログイン済みユーザーです');
    
    //現在日時が最終ログイン日時＋有効期限を超えていた場合
    if(($_SESSION['login_date'] + $_SESSION['login_limit']) < time()){
        debug('ログイン有効期限オーバーです');
        
        //セッション削除
        session_destroy();
        //ログインページへ
        header("Location:login.php");
    }else{
        debug('ログイン有効期限内です');
        //最終ログイン日時を現在日時に変更
        $_SESSION['login_date'] = time();
        
        
        if(basename($_SERVER['PHP_SELF']) === 'login.php'){
            debug('HOMEへ遷移します');
            header("Location:top.php");
        }
    }
}else{
    debug('未ログインユーザーです');
    if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
        header("Location:logins.php");
    }
}
<?php
//共通変数・関数ファイルを読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「');
debug('「　ログアウトページ ');
debug('「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

debug('ログアウトします');

session_destroy();
debug('ログインページへ遷移します');

header("Location:login.php");
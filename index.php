<?php
$siteTitle = 'タイトル';
require('head.php');
?>

<body>
    
    <!-- ヘッダー　-->
    <header>
            <div class="site-width">
                <h1><a href="top.php" style="letter-spacing:0.06em;">Orange Music</a></h1>
            </div>
        </header>
        
        <!-- メインコンテンツ　-->
        <div id="contents" class="site-width2">
            
            <!-- Main -->
            <div class="img-width">
            <img src="img/fimpli-3NgcTH0CFJg-unsplash.jpg" alt="" id="top-baner">
            <p>好きな曲をシェアしよう</p>
            </div>
            <div id="title-right">
            <div class="text-box">
                <p class="text1">Orange Musicは</p>
                <p class="text2">みんなの聴いている音楽を知りたい</p>
                <p class="text2">おすすめの曲を紹介したい</p>
                <p class="text2">同じ趣味の人と交流したい</p>
                <p class="text3">そんなあなたのための音楽専用掲示板です</p>
            </div>
            
            <label class="post_in" style="margin-top:80px;"><a href="login.php">始める</a></label>
            </div>
        </div>
        
        
        <!-- footer -->
        <?php
         require('footer.php');
        ?>
</body>

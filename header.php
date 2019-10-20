<header>
            <div class="site-width">
                <h1><a href="top.php" style="letter-spacing:0.06em;">Orange Music</a></h1>
                <nav id="top-nav">
                    <ul>
                        <?php
                        if(empty($_SESSION['user_id'])){
                        ?>
                        <li><a href="signup.php" class="btn btn-primary" style="padding-left:30px;padding-right:30px;">登録</a></li>
                        <li><a href="login.php">ログイン</a></li>
                        <?php
                        }else{
                        ?>
                        <li><a href="mypage.php">マイページ</a></li>
                        <li><a href="logout.php">ログアウト</a></li>
                        <?php
                        }
                        ?>
                        
                    </ul>
                </nav>
            </div>
        </header>
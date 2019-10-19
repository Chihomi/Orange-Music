
<footer id="footer">
    Copyright 2019 sakuraw All Rights Reserved.
</footer>
<script src="js/jquery-3.4.1.min.js"></script>

<script>
    $(function(){
        
        
        //フッターを最下部に固定
        var $ftr = $('#footer');
        if(window.innerHeight > $ftr.offset().top + $ftr.outerHeight()){
            $ftr.attr({'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) + 'px;'
                      });
        
        }
        
        //メッセージ表示
        var $jsShowMsg = $('#js-show-msg');
        var msg = $jsShowMsg.text();
        if(msg.replace(/^[\s　]+|[\s　]+$/g, "").length){
            $jsShowMsg.fadeToggle('slow');
            setTimeout(function(){ $jsShowMsg.fadeToggle('slow');},3500);
        }
        
        //画像ライブプレビュー
        var $dropArea = $('.area-drop');
        var $fileInput = $('.input-file');
        $dropArea.on('dragover',function(e){
            e.stopPropagation();
            e.preventDefault();
            $(this).css('border','3px #ccc dashed');
        });
        $dropArea.on('dragleave',function(e){
            e.stopPropagation();
            e.preventDefault();
            $(this).css('border','none');
        });
        $fileInput.on('change',function(e){
            $dropArea.css('border','none');
            var file = this.files[0],
                $img = $(this).siblings('.prev-img'),
                fileReader = new FileReader();
            
            fileReader.onload = function(event){
                $img.attr('src',event.target.result).show();
            };
            
            fileReader.readAsDataURL(file);
        });
        
        //テキストエリアカウント
        var $countUp = $('#js-count'),
            $countView = $('#js-count-view');
        $countUp.on('keyup',function(e){
            $countView.html($(this).val().replace(/[\n\s ]/g,"").length);
        });
        
        //お気に入り登録・削除
        var $like,
            likePostId;
        $like = $('.js-click-like') || null;
        likePostId = $like.data('postid') || null;
        
        if(likePostId !== undefined && likePostId !== null){
            $like.on('click',function(){
                var $this = $(this);
                $.ajax({
                    type: "post",
                    url: "ajaxLike.php",
                    data: { postId : likePostId}
                }).done(function(data){
                    console.log('Ajax Success');
                    $this.toggleClass('active');
                }).fail(function(msg){
                    console.log('Ajax Error');
                });
            });
        }
        
    });
</script>
    
    
    
    </body>

</html>
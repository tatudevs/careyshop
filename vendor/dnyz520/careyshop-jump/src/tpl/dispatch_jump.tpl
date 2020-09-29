<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
    <meta name="description" content="本站基于CareyShop商城框架系统搭建">
    <title>跳转提示 - CareyShop</title>
    <link rel="stylesheet" type="text/css" href="https://zh-static.oss-accelerate.aliyuncs.com/css/dispatch.css" />
</head>
<body>
    <div class="system-message">
        <?php switch ($status) {?>
            <?php case 200:?>
            <h1>:)</h1>
            <p class="success"><?php echo(strip_tags($message));?></p>
            <?php break;?>
            <?php case 500:?>
            <h1>:(</h1>
            <p class="error"><?php echo(strip_tags($message));?></p>
            <?php break;?>
        <?php } ?>
        <p class="detail"></p>
        <p class="jump">
            页面自动 <a id="href" href="<?php echo($url);?>">跳转</a> 等待时间： <b id="wait"><?php echo($wait);?></b>
        </p>
    </div>
    <script type="text/javascript">
        (function(){
            var wait = document.getElementById('wait'),
                href = document.getElementById('href').href;
            var interval = setInterval(function(){
                var time = --wait.innerHTML;
                if(time <= 0) {
                    location.href = href;
                    clearInterval(interval);
                };
            }, 1000);
        })();
    </script>
</body>
</html>

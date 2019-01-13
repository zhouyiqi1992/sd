<?php

$a = urlencode('http://hs.tumujinhua.com/bb');
$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxb35b8eb70acd3e3b&redirect_uri='.$a.'&response_type=code&scope=snsapi_base&state=123#wechat_redirect';
Header("Location: $url");

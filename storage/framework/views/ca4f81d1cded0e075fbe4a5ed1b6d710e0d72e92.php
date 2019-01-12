<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Mosaddek">
    <meta name="keyword" content="FlatLab, Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
    

    <title>后台登入</title>
    <style type="text/css">

        #recode {
            cursor: pointer
        }

        #yzm {
            float: right;
            margin-top: 2px;
            margin-right: 5px;
        }

        .new-style-input {
            padding: 0 15px !important;
            background: rgba(45, 45, 45, .15) !important;
            -moz-border-radius: 6px !important;
            -webkit-border-radius: 6px !important;
            border-radius: 6px !important;
            border: 1px solid #3d3d3d !important;
            border: 1px solid rgba(255, 255, 255, .15) !important;
            -moz-box-shadow: 0 2px 3px 0 rgba(0, 0, 0, .1) inset !important;
            -webkit-box-shadow: 0 2px 3px 0 rgba(0, 0, 0, .1) inset !important;
            box-shadow: 0 2px 3px 0 rgba(0, 0, 0, .1) inset !important;
            font-size: 14px !important;
            color: #fff !important;
            width: 290px !important;
            height: 42px !important;
        }

        .new-style-input2 {
            width: 60% !important;
        }
    </style>
    <!-- Bootstrap core CSS -->
    <link href="<?php echo e(URL::asset('css/bootstrap.min.css'), false); ?>" rel="stylesheet">
    <link href="<?php echo e(URL::asset('css/bootstrap-reset.css'), false); ?>" rel="stylesheet">
    <!--external css-->
    <link href="<?php echo e(URL::asset('assets/font-awesome/css/font-awesome.css'), false); ?>" rel="stylesheet"/>
    <!-- Custom styles for this template -->
    <link href="<?php echo e(URL::asset('css/style.css'), false); ?>" rel="stylesheet">
    <link href="<?php echo e(URL::asset('css/style-responsive.css'), false); ?>" rel="stylesheet"/>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
</head>

<body class="login-body" style="background: url(../../img/bc.jpg)">
<div>
    <form class="form-signin" method="post" action="<?php echo e(url('back/login/login'), false); ?>" style="background:none;">
        <?php echo e(csrf_field(), false); ?>

        <h2 class="form-signin-heading" style="background:none;">后台管理系统</h2>
        <div class="login-wrap">
            <?php if(count($errors) > 0): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><font color="red"><?php echo e($error, false); ?></font></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>
            <input type="text" name="username" class="form-control new-style-input" placeholder="用户名" autofocus>
            <input type="password" name="password" class="form-control new-style-input" placeholder="密码">
            <input type="text" name="checkcode" class="form-controla new-style-input2 new-style-input" placeholder="验证码"
                   style="float:left">

            <div id="yzm">
                <a href="javascript:;" id="recode1" title="看不清？换一张！"><img src="<?php echo e(url('back/admin/verify'), false); ?>" border="0"
                                                                          class="verifyimg" id="verifyimg"/></a>
            </div>
            

            <button id="btn-submit" class="btn btn-lg btn-login btn-block" type="submit">登录</button>

        </div>

    </form>

</div>

<script>
    var recode = document.getElementById('recode1');
    var verifyimg = document.getElementById('verifyimg');

    recode.onclick = function () {
        var time = new Date().getTime();
        verifyimg.src = "<?php echo e(url('back/admin/verify'), false); ?>?_=" + time;

    }
</script>
<script>


</script>
</body>
</html>

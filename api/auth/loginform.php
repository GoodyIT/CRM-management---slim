<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $apiObj->getSetting('site_name'); ?></title>
        <link href="<?php echo $apiObj->getSetting('base_uri');?>css/bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo $apiObj->getSetting('base_uri');?>font-awesome/css/font-awesome.css" rel="stylesheet">
        <link href="<?php echo $apiObj->getSetting('base_uri');?>css/plugins/iCheck/custom.css" rel="stylesheet">
        <link href="<?php echo $apiObj->getSetting('base_uri');?>css/animate.css" rel="stylesheet">
        <link href="<?php echo $apiObj->getSetting('base_uri');?>css/style.css" rel="stylesheet">
    </head>
    <body class="gray-bg">
        <div class="middle-box text-center loginscreen  animated fadeInDown">
            <div>
                <div>
                    <h1 class="logo-name">CRM</h1>
                </div>
                <h3>Welcome to CRM</h3>
                <?php
                    $messages = $apiObj->getMessage("success");
                    if(!empty($messages)){
                        foreach($messages as $key=>$value){
                            echo "<h3>".$value."</h3>";   
                        }
                    } else {
                        ?>
                         <p>Help increase your sales with our broker services
                            <!--Continually expanded and constantly improved Inspinia Admin Them (IN+)-->
                        </p>
                        <p>Login in. To see it in action.</p>
                        <?php
                    }
                ?>
               <form method="post" class="m-t" role="form" action="loginnow">
                    <div class="form-group">
                        <input type="email" name="email" class="form-control" placeholder="Email" required="" value="<?php echo trim($apiObj->getFormValue('email'));?>">
                         <?php echo $apiObj->getFormError('email');?>
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" class="form-control" placeholder="Password" required="">
                         <?php echo $apiObj->getFormError('password');?>
                    </div>
                    <button type="submit" class="btn btn-primary block full-width m-b">Login</button>
                    <a href="forgot"><small>Forgot password?</small></a>
                    <p class="text-muted text-center"><small>Do not have an account?</small></p>
                    <a class="btn btn-sm btn-white btn-block" href="signup">Create an account</a>
                </form>
            </div>
        </div>
    </body>
</html>
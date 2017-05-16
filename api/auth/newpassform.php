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
                    <h1 class="logo-name">EBC</h1>
                </div>
                <h3>RESET YOUR PASSWORD</h3>
                Password needs to be 5 or more characters long.
       
               <form method="post" class="m-t" role="form" action="newpass">
                    <div class="form-group">
                        <input type="password" name="password" class="form-control" placeholder="Password" required="">
                         <?php echo $apiObj->getFormError('password');?>
                    </div>
                    <div class="form-group">
                        <input type="password" name="passwordconf" class="form-control" placeholder="Password Conf" required="">
                         <?php echo $apiObj->getFormError('passwordconf');?>
                    </div>
                    <button type="submit" class="btn btn-primary block full-width m-b">RESET NOW</button>
                   
                </form>
            </div>
        </div>
    </body>
</html>
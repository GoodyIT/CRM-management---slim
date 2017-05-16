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
        <div class="middle-box text-center loginscreen   animated fadeInDown">
            <div>
                <div>
                    <h1 class="logo-name">CRM</h1>
                </div>
                <h3>Register to CRM</h3>
                <p>Create account to see it in action.</p>
                <form method="post" class="m-t" role="form" action="register">
                    <input type="hidden" name="user_0_createThing" value="TRUE">
                    <input type="hidden" name="user_0_licensed" value="N" />
                    <div class="form-group">
                        <input name="user_0_firstname" type="text" class="form-control" placeholder="First Name" required="" value="<?php echo trim($apiObj->getFormValue('user_0_firstname'));?>">
                        <?php echo $apiObj->getFormError('user_0_firstname');?>
                    </div>
                     <div class="form-group">
                        <input name="user_0_lastname" type="text" class="form-control" placeholder="Last Name" required="" value="<?php echo trim($apiObj->getFormValue('user_0_lastname'));?>">
                        <?php echo $apiObj->getFormError('user_0_lastname');?>
                    </div>
                    <div class="form-group">
                        <input name="user_0_email"  type="email" class="form-control" placeholder="Email" required="" value="<?php echo trim($apiObj->getFormValue('user_0_email'));?>">
                         <?php echo $apiObj->getFormError('user_0_email');?>
                    </div>
                    <div class="form-group">
                        <input name="user_0_phone"  type="phone" class="form-control" placeholder="Phone" required="" value="<?php echo trim($apiObj->getFormValue('user_0_phone'));?>">
                        <?php echo $apiObj->getFormError('user_0_phone');?>
                    </div>
                   <div class="form-group">
                        <input name="user_0_password"  type="password" class="form-control" placeholder="Password" required="" value="<?php echo trim($apiObj->getFormValue('user_0_password'));?>">
                        <?php echo $apiObj->getFormError('user_0_password');?>
                    </div>
                    <div class="form-group">
                        <input name="user_0_passwordConf"  type="password" class="form-control" placeholder="Confirm Password" required="" value="<?php echo trim($apiObj->getFormValue('user_0_passwordConf'));?>">
                        <?php echo $apiObj->getFormError('user_0_passwordConf');?>
                    </div>
                    <div class="form-group">
                        <input name="user_0_code"  type="text" class="form-control" placeholder="code"  value="">
                    </div>
                    <div class="form-group">
                        <div class="checkbox i-checks"><label> <input name="user_0_licensed" value="Y" type="checkbox" <?php echo $apiObj->getFormChecked('user_0_licensed','Y');?>><i></i> I am a licensed Agent </label></div>
                    </div>
                    <div class="form-group">
                        <div class="checkbox i-checks"><label> <input name="user_0_agreeToTerms" value="Y" type="checkbox" <?php echo $apiObj->getFormChecked('user_0_agreeToTerms','Y');?>><i></i> Agree the terms and policy </label></div>
                          <?php echo $apiObj->getFormError('user_0_agreeToTerms');?>
                    </div>
                    <button type="submit" class="btn btn-primary block full-width m-b">Register</button>
                    <p class="text-muted text-center"><small>Already have an account?</small></p>
                    <a class="btn btn-sm btn-white btn-block" href="login">Login</a>
                </form>
            </div>
        </div>
        <!-- Mainly scripts -->
        <script src="<?php echo $apiObj->getSetting('base_uri');?>js/jquery/jquery-2.1.1.min.js"></script>
        <script src="<?php echo $apiObj->getSetting('base_uri');?>js/bootstrap/bootstrap.min.js"></script>
        <!-- iCheck -->
        <script src="<?php echo $apiObj->getSetting('base_uri');?>js/plugins/iCheck/icheck.min.js"></script>
        <script>
            $(document).ready(function(){
                $('.i-checks').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green',
                });
            });
        </script>
    </body>
</html>
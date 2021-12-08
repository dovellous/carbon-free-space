<!-- Outer Row -->
<div class="row justify-content-center">

    <div class="col-xl-10 col-lg-12 col-md-9">

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                    <div class="col-lg-6">
                        <div class="p-4">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">
                                    <img class="login-logo" src="<?php echo base_url(); ?>themes/Images/xsaas-logo-512.png" />
                                </h1>
                                <h1 class="h4 text-gray-900 mb-4">
                                    <?php echo lang('Auth_Users.page_login_title', [], $config["system"]["i18n.user.defined"]); ?>
                                </h1>
                            </div>

                            <?php

                            $session = \Config\Services::session();

                            $flashMsg = NULL;

                            if(is_array($session->getFlashdata())) {

                                if (array_key_exists("fmsg_auth", $session->getFlashdata())) {

                                    $flashMsg = $session->getFlashdata()["fmsg_auth"];

                                }

                            }

                            ?>

                            <?php if($flashMsg) { ?>

                            <div class="alert alert-<?php echo $flashMsg["class"]; ?>" role="alert">
                                <strong><?php echo $flashMsg["title"]; ?></strong>
                                <br>
                                <?php echo $flashMsg["message"]; ?>
                            </div>

                            <?php }

                            $form_action = acme_base_url($config["system"]["path.login.path.auth"]);

                            $form_attributes = array(
                                "class" => "user",
                                "method" => "post",
                                "id" => "user-sign-in-form"
                            );

                            echo form_open_multipart($form_action, $form_attributes);

                            ?>
                                <div class="form-group">
                                    <input type="email" autocomplete="on" class="form-control form-control-user" id="email" name="email" placeholder="Enter Email Address...">
                                </div>
                                <div class="form-group">
                                    <input type="password" autocomplete="on" class="form-control form-control-user" id="password" name="password" placeholder="Password">
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox small">
                                        <input type="checkbox" class="custom-control-input" id="customCheck" name="remember">
                                        <label class="custom-control-label" for="customCheck">
                                            <?php echo lang('Auth_Users.remember_me', [], $config["system"]["i18n.user.defined"]); ?>
                                        </label>
                                    </div>
                                </div>
                                <hr>
                            <?php if(acme_get_env("acme.config.system.google.captcha.enabled", "bool")) : ?>
                                <div class="form-group">
                                    <div class="g-recaptcha" data-sitekey="<?php echo $config["system"]["google.captcha.site.key"]; ?>"></div>
                                </div>
                            <?php endif; ?>
                                <button href="#" id="login-submit-button" class="btn btn-primary btn-user btn-block login-submit-button">
                                    <?php echo lang('Auth_Users.buttons_login', [], $config["system"]["i18n.user.defined"]); ?>
                                </button>
                            <?php if($config["system"]["social.login.enabled"] == "yes") : ?>
                                <hr>
                                <div class="row">
                                    <div class="col-md-4 m-b-20">
                                <a href="#" class="btn btn-google btn-user btn-block login-with-google">
                                    <i class="fab fa-google fa-fw"></i>
                                    <?php echo lang('Auth_Users.buttons_google', [], $config["system"]["i18n.user.defined"]); ?>
                                </a>
                                    </div>
                                    <div class="col-md-4 m-b-20">
                                        <a href="#" class="btn btn-facebook btn-user btn-block login-with-facebook">
                                            <i class="fab fa-facebook-f fa-fw"></i>
                                            <?php echo lang('Auth_Users.buttons_facebook', [], $config["system"]["i18n.user.defined"]); ?>
                                        </a>
                                    </div>
                                    <div class="col-md-4 m-b-20">
                                        <a href="#" class="btn btn-twitter btn-user btn-block login-with-twitter">
                                            <i class="fab fa-twitter fa-fw"></i>
                                            <?php echo lang('Auth_Users.buttons_twitter', [], $config["system"]["i18n.user.defined"]); ?>
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                            </form>
                            <hr>
                            <div class="row">
                                <div class="col text-left">
                                    <a class="small" href="<?php echo base_url(); ?>/auth/login/forgot-password">
                                        <?php echo lang('Auth_Users.label_forgot_password', [], $config["system"]["i18n.user.defined"]); ?>
                                    </a>
                                </div>
                                <div class="col text-right">
                                    <a class="small" href="<?php echo base_url(); ?>/auth/login/register">
                                        <?php echo lang('Auth_Users.label_create_account', [], $config["system"]["i18n.user.defined"]); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
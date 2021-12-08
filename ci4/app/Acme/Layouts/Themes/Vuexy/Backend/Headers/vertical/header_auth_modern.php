<!DOCTYPE html>
<html lang="en">

<head>

    <?= $contentAfterHeadOpen; ?>

    <title>
        <?=  acme_lang('Auth_Users.page_login_title', [], $config["system"]["i18n.user.defined"], $config["namespace.path"] ); ?> | <?= $config["system"]["app.name"]; ?>
    </title>
    
    <link rel="apple-touch-icon" href="<?= base_url(); ?>/app-assets/images/mambos/icon.png">
    <link rel="shortcut icon" type="image/x-icon" href="<?= base_url(); ?>/app-assets/images/mambos/icon.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>/app-assets/vendors/css/vendors.min.css">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>/app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>/app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>/app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>/app-assets/css/components.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>/app-assets/css/themes/dark-layout.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>/app-assets/css/themes/bordered-layout.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>/app-assets/css/themes/semi-dark-layout.css">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>/app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>/app-assets/css/plugins/forms/form-validation.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>/app-assets/css/pages/page-auth.css">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>/assets/css/style.css">
    <!-- END: Custom CSS-->
    
    <?php if(!empty($customCSS)) : ?>
    <!-- Begin custom styles styles -->
    <?=  $customCSS; ?>
    <!-- // end custom styles -->
    <?php endif; ?>

    <?=  $contentCustomStyles; ?>

    <?=  $contentBeforeHeadClose; ?>
    

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern blank-page navbar-floating footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="blank-page">
    <?=  $contentAfterBodyOpen ?>
    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row"></div>
            <div class="content-body">

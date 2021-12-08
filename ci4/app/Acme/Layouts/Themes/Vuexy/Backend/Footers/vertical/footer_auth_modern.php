            </div>
        </div>
    </div>
    <!-- END: Content-->

<?php echo $contentAfterFooterOpen ?>

<!-- BEGIN: Vendor JS-->
    <script src="<?= base_url(); ?>/app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="<?= base_url(); ?>/app-assets/vendors/js/forms/validation/jquery.validate.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="<?= base_url(); ?>/app-assets/js/core/app-menu.js"></script>
    <script src="<?= base_url(); ?>/app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="<?= base_url(); ?>/app-assets/js/scripts/pages/page-auth-login.js"></script>
    <!-- END: Page JS-->

    <script>
        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
        })
    </script>

<?php echo $contentCustomScripts; ?>

<?php echo $contentBeforeFooterClose; ?>

<?php echo $contentBeforeBodyClose; ?>

</body>

</html>

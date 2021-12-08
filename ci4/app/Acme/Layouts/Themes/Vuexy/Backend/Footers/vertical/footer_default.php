            </div>
        </div>
    </div>
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <!-- BEGIN: Footer-->
    <footer class="footer footer-static footer-light">

        <?php echo $contentAfterFooterOpen ?>

        <p class="clearfix mb-0">
            <span class="float-md-start d-block d-md-inline-block mt-25">
                COPYRIGHT &copy; <?= date('Y') ?>
                <a class="ms-25" href="https://1.envato.market/pixinvent_portfolio" target="_blank">Mambo's Chicken</a>
                <span class="d-none d-sm-inline-block">, All rights Raserved</span>
            </span>
            <span class="float-md-end d-none d-md-block">Hand-crafted & Made with<i data-feather="heart"></i> by Mambo's Chichen</span>
        </p>
        
    </footer>
    
        <?php echo $contentBeforeFooterClose; ?>

    <button class="btn btn-primary btn-icon scroll-top" type="button"><i data-feather="arrow-up"></i></button>
    <!-- END: Footer-->

    <!-- BEGIN: Vendor JS-->
    <script src="<?= base_url(); ?>/app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->
    
    <?php  //var_dump(file_exists(ROOTPATH . $params["views.footer.scripts"] . ".php")); die(); exit; ?>

    <?php echo include( ROOTPATH . $params["views.footer.scripts"] . ".php"); ?>

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
    
    <?php echo $contentBeforeBodyClose; ?>

</body>
<!-- END: Body-->

</html>

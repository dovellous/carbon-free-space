
    <!-- BEGIN: Page Vendor JS-->
    <script src="<?= base_url(); ?>/app-assets/vendors/js/extensions/moment.min.js"></script>
    <script src="<?= base_url(); ?>/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js"></script>
    <script src="<?= base_url(); ?>/app-assets/vendors/js/tables/datatable/datatables.buttons.min.js"></script>
    <script src="<?= base_url(); ?>/app-assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js"></script>
    <script src="<?= base_url(); ?>/app-assets/vendors/js/tables/datatable/datatables.checkboxes.min.js"></script>
    <script src="<?= base_url(); ?>/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js"></script>
    <script src="<?= base_url(); ?>/app-assets/vendors/js/tables/datatable/responsive.bootstrap4.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="<?= base_url(); ?>/app-assets/js/core/app-menu.js"></script>
    <script src="<?= base_url(); ?>/app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>/app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>/app-assets/css/pages/app-invoice-list.css">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>/assets/css/style.css">
    <!-- END: Custom CSS-->

    <script>

        $(window).on('load', function() {

            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }

        });

        /*=========================================================================================
        File Name: app-invoice-list.js
        Description: app-invoice-list Javascripts
        ----------------------------------------------------------------------------------------
        Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
        Version: 1.0
        Author: PIXINVENT
        Author URL: http://www.themeforest.net/user/pixinvent
        ==========================================================================================*/

        $(function () {
            'use strict';

            var statuses  = {
                NEW: { class: 'bg-light-warning', icon: 'clock' },
                ACCEPTED: { class: 'bg-light-secondary', icon: 'archive' },
                PREPARING: { class: 'bg-light-warning', icon: 'coffee' },
                DISPATCHED: { class: 'bg-light-info', icon: 'shopping-bag' },
                MOVING: { class: 'bg-light-secondary', icon: 'truck' },
                DELIVERED: { class: 'bg-light-success', icon: 'map-pin' },
                COMPLETED: { class: 'bg-light-success', icon: 'check-circle' },
                COLLECTED: { class: 'bg-light-success', icon: 'check-circle' },
                CLOSED: { class: 'bg-light-success', icon: 'check-circle' },
                ON_HOLD: { class: 'bg-light-primary', icon: 'pause-circle' },
                CANCELLED: { class: 'bg-light-danger', icon: 'x-circle' },
                DRAFT: { class: 'bg-light-secondary', icon: 'file-text' },
            };

            var dtOrderTable = $('.orders-list-table'),
                assetPath = '<?= base_url(); ?>/dashboard/order_json/get_all/',
                orderPreview = '<?= base_url(); ?>/dashboard/order_preview',
                orderAdd = 'app-invoice-add.html',
                orderEdit = 'app-invoice-edit.html';

            // datatable
            if (dtOrderTable.length) {
                var dtOrder = dtOrderTable.DataTable({
                    ajax: assetPath + '/', // JSON file to add data
                    autoWidth: false,
                    columns: [
                        // columns according to JSON
                        { data: 'order_id' },
                        { data: 'order_id' },
                        { data: 'order_status' },
                        { data: 'firstname' },
                        { data: 'currency' },
                        { data: 'order_total' },
                        { data: 'order_date' },
                        { data: 'cart_title' },
                        { data: '' }
                    ],
                    columnDefs: [
                        {
                            // For Responsive
                            className: 'control',
                            responsivePriority: 2,
                            targets: 0
                        },
                        {
                            // Order ID
                            targets: 1,
                            width: '72px',
                            responsivePriority: 3,
                            render: function (data, type, full, meta) {
                                var $orderId = full['order_id'];
                                // Creates full output for row
                                var $rowOutput = '<a class="fw-bold" target="_blank" href="' + orderPreview + '/'+$orderId+'/"> <strong> #' + $orderId + ' </strong> </a>';
                                return $rowOutput;
                            }
                        },
                        {
                            // Order status: states = ['success', 'danger', 'warning', 'info', 'primary', 'secondary'],
                            targets: 2,
                            width: '72px',
                            responsivePriority: 4,
                            render: function (data, type, full, meta) {

                                console.log(":: FULL ::", full);

                                var $orderStatus = full['state'],
                                    $updatedDate = full['order_date'],
                                    $status = full['state'],
                                    roleObj = statuses;
                                return (
                                    "<span data-bs-toggle='tooltip' data-bs-html='true' title='<span>" +
                                    '<span class="fw-bold">Status:</span> ' +
                                    $status +
                                    '<br> <span class="fw-bold">Last Updated:</span> ' +
                                    $updatedDate +
                                    "</span>'>" +
                                    '<div class="avatar avatar-status ' +
                                    roleObj[$orderStatus].class +
                                    '">' +
                                    '<span class="avatar-content">' +
                                    feather.icons[roleObj[$orderStatus].icon].toSvg({ class: 'avatar-icon' }) +
                                    '</span>' +
                                    '</div>' +
                                    '</span>'
                                );
                            }
                        },
                        {
                            // Client name and Service
                            targets: 3,
                            responsivePriority: 5,
                            width: '270px',
                            render: function (data, type, full, meta) {
                                var $name = full['firstname']+' '+full['lastname'],
                                    $email = full['email'],
                                    $mobile = full['mobile'],
                                    $image = '',
                                    stateNum = Math.floor(Math.random() * 6),
                                    states = ['success', 'danger', 'warning', 'info', 'primary', 'secondary'],
                                    $state = states[stateNum],
                                    $name = full['firstname']+' '+full['lastname'],
                                    $initials = $name.match(/\b\w/g) || [];
                                $initials = (($initials.shift() || '') + ($initials.pop() || '')).toUpperCase();
                                if ($image) {
                                    // For Avatar image
                                    var $output =
                                        '<img  src="' + $image + '" alt="Avatar" width="32" height="32">';
                                } else {
                                    // For Avatar badge
                                    $output = '<div class="avatar-content">' + $initials + '</div>';
                                }
                                // Creates full output for row
                                var colorClass = $image === '' ? ' bg-light-' + $state + ' ' : ' ';

                                var $rowOutput =
                                    '<div class="d-flex justify-content-left align-items-center">' +
                                    '<div class="avatar-wrapper">' +
                                    '<div class="avatar' +
                                    colorClass +
                                    'me-50">' +
                                    $output +
                                    '</div>' +
                                    '</div>' +
                                    '<div class="d-flex flex-column">' +
                                    '<h6 class="user-name text-truncate mb-0">' +
                                    $name +
                                    '</h6>' +
                                    '<small class="text-truncate text-muted">' +
                                    $email +
                                    '<br/><small class="text-truncate text-muted">' +
                                    $mobile +
                                    '</small>' +
                                    '</div>' +
                                    '</div>';
                                return $rowOutput;
                            }
                        },
                        {
                            // Total Order Amount
                            targets: 4,
                            width: '125px',
                            responsivePriority: 6,
                            render: function (data, type, full, meta) {
                                var $total = full['order_total'];
                                var $currency = full['currency'];
                                return '<span class="d-none">' + $currency + ' ' + $total + '</span>' + $currency + ' ' + ($total).toFixed(2);
                            }
                        },
                        {
                            // Due Date
                            targets: 5,
                            width: '130px',
                            responsivePriority: 7,
                            render: function (data, type, full, meta) {
                                var $orderDate = new Date(full['order_date']);
                                // Creates full output for row
                                var $rowOutput =
                                    '<span class="d-none">' +
                                    moment($orderDate).format('YYYYMMDD') +
                                    '</span>' +
                                    moment($orderDate).format('DD MMM YYYY');
                                $orderDate;
                                return $rowOutput;
                            }
                        },
                        {
                            // Client Balance/Status
                            targets: 6,
                            width: '120px',
                            responsivePriority: 8,
                            render: function (data, type, full, meta) {

                                var $paymentMethod = "N/A";
                                var $paymentTXNID = "N/A";
                                var $badge_class = 'badge-light-danger';
                                var $paymentStatus = 'PENDING';

                                var $hasPaid = full.payment.result;

                                if($hasPaid){

                                    if(full.payment.result.payment_status === 'COMPLETED'){
                                        $badge_class = 'badge-light-success';
                                    }else{
                                        $badge_class = 'badge-light-warning';
                                    }

                                    $paymentStatus = full.payment.result.payment_status;
                                    $paymentTXNID = full.payment.result.txn_id;
                                    $paymentMethod = full.payment.method;
                                }

                                return "<span data-bs-toggle='tooltip' data-bs-html='true' title='<span>" +
                                    '<span class="fw-bold">Method: </span> ' +
                                    $paymentMethod +
                                    '<br> <span class="fw-bold">Txn ID:</span> ' +
                                    $paymentTXNID +
                                    "</span>'>" +
                                    '<span class="badge rounded-pill ' + $badge_class + '" text-capitalized> ' + $paymentStatus + ' </span>' +
                                    '</span>';
                            }
                        },
                        {
                            // Due Date
                            targets: 7,
                            width: '130px',
                            responsivePriority: 8,
                            render: function (data, type, full, meta) {
                                var $orderTitle = full['cart_title'];
                                return $orderTitle;
                            }
                        },
                        {
                            // Actions
                            targets: -1,
                            title: 'Actions',
                            width: '80px',
                            orderable: false,
                            render: function (data, type, full, meta) {
                                return (
                                    '<div class="d-flex align-items-center col-actions">' +
                                    '<a class="me-1" href="#" onclick="sendEmail(' + full['order_id'] +')" data-bs-toggle="tooltip" data-bs-placement="top" title="Send Mail">' +
                                    feather.icons['send'].toSvg({ class: 'font-medium-2' }) +
                                    '</a>' +
                                    '<a class="me-1" target="_blank" href="' +
                                    orderPreview + '/' + full['order_id'] +
                                    '" data-bs-toggle="tooltip" data-bs-placement="top" title="Preview Order">' +
                                    feather.icons['eye'].toSvg({ class: 'font-medium-2' }) +
                                    '</a>' +
                                    '<div class="dropdown">' +
                                    '<a class="btn btn-sm btn-icon px-0" data-bs-toggle="dropdown">' +
                                    feather.icons['more-vertical'].toSvg({ class: 'font-medium-2' }) +
                                    '</a>' +
                                    '<div class="dropdown-menu dropdown-menu-end">' +
                                    '<a href="#" class="dropdown-item">' +
                                    feather.icons['download'].toSvg({ class: 'font-small-4 me-50' }) +
                                    'Download</a>' +
                                    '<a href="' +
                                    orderEdit +
                                    '" class="dropdown-item">' +
                                    feather.icons['edit'].toSvg({ class: 'font-small-4 me-50' }) +
                                    'Edit</a>' +
                                    '<a href="#" class="dropdown-item">' +
                                    feather.icons['trash'].toSvg({ class: 'font-small-4 me-50' }) +
                                    'Delete</a>' +
                                    '<a href="#" class="dropdown-item">' +
                                    feather.icons['copy'].toSvg({ class: 'font-small-4 me-50' }) +
                                    'Duplicate</a>' +
                                    '</div>' +
                                    '</div>' +
                                    '</div>'
                                );
                            }
                        }
                    ],
                    order: [[1, 'desc']],
                    dom:
                    '<"row d-flex justify-content-between align-items-center m-1"' +
                    '<"col-lg-6 d-flex align-items-center"l<"dt-action-buttons text-xl-end text-lg-start text-lg-end text-start "B>>' +
                    '<"col-lg-6 d-flex align-items-center justify-content-lg-end flex-lg-nowrap flex-wrap pe-lg-1 p-0"f<"order_status ms-sm-2">>' +
                    '>t' +
                    '<"d-flex justify-content-between mx-2 row"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6"p>' +
                    '>',
                    language: {
                        sLengthMenu: 'Show _MENU_',
                        search: 'Search',
                        searchPlaceholder: 'Search Order',
                        paginate: {
                            // remove previous & next text from pagination
                            previous: '&nbsp;',
                            next: '&nbsp;'
                        }
                    },
                    // Buttons with Dropdown
                    buttons: [
                        {
                            text: 'Refresh Orders',
                            className: 'btn btn-secondary btn-add-record ms-2',
                            action: function (e, dt, button, config) {
                                window.location.reload;
                            }
                        },
                        {
                            text: 'PDF',
                            className: 'btn btn-danger btn-add-record ms-2',
                            action: function (e, dt, button, config) {
                                window.location.reload;
                            }
                        },
                        {
                            text: 'Excel',
                            className: 'btn btn-success btn-add-record ms-2',
                            action: function (e, dt, button, config) {
                                window.location.reload;
                            }
                        },
                        {
                            text: 'Email',
                            className: 'btn btn-primary btn-add-record ms-2',
                            action: function (e, dt, button, config) {
                                window.location.reload;
                            }
                        },
                    ],
                    // For responsive popup
                    responsive: {
                        details: {
                            display: $.fn.dataTable.Responsive.display.modal({
                                header: function (row) {
                                    var data = row.data();
                                    return 'Details of ' + data['firstname'];
                                }
                            }),
                            type: 'column',
                            renderer: $.fn.dataTable.Responsive.renderer.tableAll({
                                tableClass: 'table',
                                columnDefs: [
                                    {
                                        targets: 2,
                                        visible: false
                                    },
                                    {
                                        targets: 3,
                                        visible: false
                                    }
                                ]
                            })
                        }
                    },
                    initComplete: function () {
                        $(document).find('[data-bs-toggle="tooltip"]').tooltip();
                        // Adding role filter once table initialized
                        this.api()
                            .columns(3)
                            .every(function () {
                                var column = this;
                                var select = $(
                                    '<select id="UserRole" class="form-select ms-50 text-capitalize"><option value=""> Select Status </option></select>'
                                )
                                    .appendTo('.order_status')

                                    .on('change', function () {

                                        var val = $.fn.dataTable.util.escapeRegex($(this).val());

                                        $("#DataTables_Table_0_filter").find("input").val(val).click().keyup().focus().val("").blur();

                                    });

                                    for(status in statuses) {

                                        select.append('<option value="' + status + '" class="text-capitalize">' + status + '</option>');

                                    }

                                }

                            );

                        //

                    },
                    drawCallback: function () {
                        $(document).find('[data-bs-toggle="tooltip"]').tooltip();
                    }
                });
            }
        });


    </script>
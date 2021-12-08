
    <!-- BEGIN: Page Vendor JS-->
    <script src="<?= base_url(); ?>/app-assets/vendors/js/forms/repeater/jquery.repeater.min.js"></script>
    <script src="<?= base_url(); ?>/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>/app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>/app-assets/css/plugins/forms/pickers/form-flat-pickr.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>/app-assets/css/pages/app-invoice.css">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>/assets/css/style.css">
    <!-- END: Custom CSS-->

    <!-- BEGIN: Theme JS-->
    <script src="<?= base_url(); ?>/app-assets/js/core/app-menu.js"></script>
    <script src="<?= base_url(); ?>/app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->

    <script>

        $(window).on('load', function() {
            //
        });


        /*=========================================================================================
            File Name: app-invoice.js
            Description: app-invoice Javascripts
            ----------------------------------------------------------------------------------------
            Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
           Version: 1.0
            Author: PIXINVENT
            Author URL: http://www.themeforest.net/user/pixinvent
        ==========================================================================================*/
        $(function () {
            'use strict';

            var applyChangesBtn = $('.btn-apply-changes'),
                discount,
                tax1,
                tax2,
                discountInput,
                tax1Input,
                tax2Input,
                sourceItem = $('.source-item'),
                date = new Date(),
                datepicker = $('.date-picker'),
                dueDate = $('.due-date-picker'),
                select2 = $('.invoiceto'),
                countrySelect = $('#customer-country'),
                btnAddNewItem = $('.btn-add-new '),
                adminDetails = {
                    'App Design': 'Designed UI kit & app pages.',
                    'App Customization': 'Customization & Bug Fixes.',
                    'ABC Template': 'Bootstrap 4 admin template.',
                    'App Development': 'Native App Development.'
                },
                customerDetails = {
                    shelby: {
                        name: 'Thomas Shelby',
                        company: 'Shelby Company Limited',
                        address: 'Small Heath, Birmingham',
                        pincode: 'B10 0HF',
                        country: 'UK',
                        tel: 'Tel: 718-986-6062',
                        email: 'peakyFBlinders@gmail.com'
                    },
                    hunters: {
                        name: 'Dean Winchester',
                        company: 'Hunters Corp',
                        address: '951  Red Hawk Road Minnesota,',
                        pincode: '56222',
                        country: 'USA',
                        tel: 'Tel: 763-242-9206',
                        email: 'waywardSon@gmail.com'
                    }
                };

            // init date picker
            if (datepicker.length) {
                datepicker.each(function () {
                    $(this).flatpickr({
                        defaultDate: date
                    });
                });
            }

            if (dueDate.length) {
                dueDate.flatpickr({
                    defaultDate: new Date(date.getFullYear(), date.getMonth(), date.getDate() + 5)
                });
            }

            // Country Select2
            if (countrySelect.length) {
                countrySelect.select2({
                    placeholder: 'Select country',
                    dropdownParent: countrySelect.parent()
                });
            }

            // Close select2 on modal open
            $(document).on('click', '.add-new-customer', function () {
                select2.select2('close');
            });

            // Select2
            if (select2.length) {
                select2.select2({
                    placeholder: 'Select Customer',
                    dropdownParent: $('.invoice-customer')
                });

                select2.on('change', function () {
                    var $this = $(this),
                        renderDetails =
                            '<div class="customer-details mt-1">' +
                            '<p class="mb-25">' +
                            customerDetails[$this.val()].name +
                            '</p>' +
                            '<p class="mb-25">' +
                            customerDetails[$this.val()].company +
                            '</p>' +
                            '<p class="mb-25">' +
                            customerDetails[$this.val()].address +
                            '</p>' +
                            '<p class="mb-25">' +
                            customerDetails[$this.val()].country +
                            '</p>' +
                            '<p class="mb-0">' +
                            customerDetails[$this.val()].tel +
                            '</p>' +
                            '<p class="mb-0">' +
                            customerDetails[$this.val()].email +
                            '</p>' +
                            '</div>';
                    $('.row-bill-to').find('.customer-details').remove();
                    $('.row-bill-to').find('.col-bill-to').append(renderDetails);
                });

                select2.on('select2:open', function () {
                    if (!$(document).find('.add-new-customer').length) {
                        $(document)
                            .find('.select2-results__options')
                            .before(
                                '<div class="add-new-customer btn btn-flat-success cursor-pointer rounded-0 text-start mb-50 p-50 w-100" data-bs-toggle="modal" data-bs-target="#add-new-customer-sidebar">' +
                                feather.icons['plus'].toSvg({ class: 'font-medium-1 me-50' }) +
                                '<span class="align-middle">Add New Customer</span></div>'
                            );
                    }
                });
            }

            // Repeater init
            if (sourceItem.length) {
                sourceItem.on('submit', function (e) {
                    e.preventDefault();
                });
                sourceItem.repeater({
                    show: function () {
                        $(this).slideDown();
                    },
                    hide: function (e) {
                        $(this).slideUp();
                    }
                });
            }

            // Prevent dropdown from closing on tax change
            $(document).on('click', '.tax-select', function (e) {
                e.stopPropagation();
            });

            // On tax change update it's value
            function updateValue(listener, el) {
                listener.closest('.repeater-wrapper').find(el).text(listener.val());
            }

            // Apply item changes btn
            if (applyChangesBtn.length) {
                $(document).on('click', '.btn-apply-changes', function (e) {
                    var $this = $(this);
                    tax1Input = $this.closest('.dropdown-menu').find('#tax-1-input');
                    tax2Input = $this.closest('.dropdown-menu').find('#tax-2-input');
                    discountInput = $this.closest('.dropdown-menu').find('#discount-input');
                    tax1 = $this.closest('.repeater-wrapper').find('.tax-1');
                    tax2 = $this.closest('.repeater-wrapper').find('.tax-2');
                    discount = $('.discount');

                    if (tax1Input.val() !== null) {
                        updateValue(tax1Input, tax1);
                    }

                    if (tax2Input.val() !== null) {
                        updateValue(tax2Input, tax2);
                    }

                    if (discountInput.val().length) {
                        var finalValue = discountInput.val() <= 100 ? discountInput.val() : 100;
                        $this
                            .closest('.repeater-wrapper')
                            .find(discount)
                            .text(finalValue + '%');
                    }
                });
            }

            // Item details select onchange
            $(document).on('change', '.item-details', function () {
                var $this = $(this),
                    value = adminDetails[$this.val()];
                if ($this.next('textarea').length) {
                    $this.next('textarea').val(value);
                } else {
                    $this.after('<textarea class="form-control mt-2" rows="2">' + value + '</textarea>');
                }
            });
            if (btnAddNewItem.length) {
                btnAddNewItem.on('click', function () {
                    if (feather) {
                        // featherSVG();
                        feather.replace({ width: 14, height: 14 });
                    }
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                });
            }
        });


        function addPaymentMethod(){

            let uid = $("#payment-order-uid").val();

            let order_id = $("#payment-order-id").val();

            let order_number = $("#payment-order-number").val();

            let date = $("#payment-date").val();

            let note = $("#payment-note").val();

            let method = $('#payment-method').val();

            let amount = parseFloat($("#payment-amount").val());

            let currency = $("#payment-currency").val();

            let status = $("#payment-status").val();

            if(amount > 0) {

                let paidOrder = $.ajax({
                    type: 'POST',
                    url: "<?= base_url(); ?>/dashboard/orders_add_payment/"+method,
                    data: {uid: uid, order_id: order_id, order_number: order_number, date: date, note: note, method: method, currency: currency, amount: amount, status: status},
                    dataType: "json",
                    success: function (resultData) {

                        console.log("Payment Added Successfully!", resultData);

                        alert('Payment of '+amount+' via '+method+' was successfully added');

                        setTimeout(function () {
                            toastr['success'](
                                'Payment of '+amount+' via '+method+' was successfully added',
                                'Payment Saved!',
                                {
                                    closeButton: true,
                                    tapToDismiss: false,
                                    rtl: false
                                }
                            );
                        }, 2000);

                        $("#add-payment-sidebar").modal("hide");

                    },
                    error: function (e) {
                        console.error("Something went wrong", e);
                    }
                });

            }

        }

        function updateOrderStatus(){

            let status = $("#order_statuses_list_view").val();

            let comments = $("#order_statuses_comments").val();

            let push_to = $('#send_order_to_kdu').is(":checked");

            let order_id = $("#order_statuses_order_id").val();

            let uid = $("#order_statuses_uid").val();

            if(status !== 0 || status !== "0") {

                let updatedOrder = $.ajax({
                    type: 'POST',
                    url: "<?= base_url(); ?>/dashboard/orders_update_status/"+status,
                    data: {uid: uid, status: status, comments: comments, push_to_kds: push_to, order_id: order_id},
                    dataType: "json",
                    success: function (resultData) {

                        console.log("Save Complete", resultData);

                        alert('Order #'+order_id+' was updated with status '+status+'.');

                        setTimeout(function () {
                            toastr['success'](
                                'Order #'+order_id+' was updated with status '+status+'.',
                                'Status Update compled!',
                                {
                                    closeButton: true,
                                    tapToDismiss: false,
                                    rtl: false
                                }
                            );
                        }, 2000);

                    },
                    error: function (e) {
                        console.error("Something went wrong", e);
                    }
                });

            }

        }

        function assignDriver(){

            let order_id = $("#order_statuses_order_id").val();

            let uid = $("#order_drivers_list_view").val();

            let vehicle = $("#order_vehicles_list_view").val();

            if(order_id !== "") {

                let updatedDriver = $.ajax({
                    type: 'POST',
                    url: "<?= base_url(); ?>/dashboard/orders_update_driver/"+order_id,
                    data: {uid: uid, order_id: order_id, vehicle: vehicle},
                    dataType: "json",
                    success: function (resultData) {

                        console.log("Save Complete", resultData);

                        alert('Order #'+order_id+' : Driver assigned successfully');

                        setTimeout(function () {
                            toastr['success'](
                                'Order #'+order_id+' : Driver assigned successfully',
                                'Status Update compled!',
                                {
                                    closeButton: true,
                                    tapToDismiss: false,
                                    rtl: false
                                }
                            );
                        }, 2000);

                    },
                    error: function (e) {
                        console.error("Something went wrong", e);
                    }
                });

            }

        }

        function updateOrderStatusChanged(){

            let status = $("#order_statuses_list_view").val();

            if(status==="S10"){

                $("#send_order_to_kdu_container").fadeIn();

            }else{

                $("#send_order_to_kdu_container").fadeOut();

            }

        }

        function updateDriverStatusChanged(){

        }

    </script>

    <style>
        .flatpickr-calendar.animate{
            display: none;
        }
    </style>
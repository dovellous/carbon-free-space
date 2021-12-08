
<?php

$order = json_decode($params["firebase.data"],1);

$drivers = $params["drivers"];

?>

<?php //var_dump($order); ?>

<?php if(isset($order["order_id"])){ ?>

<section class="invoice-preview-wrapper">
    <div class="row invoice-preview">
        <!-- Invoice -->
        <div class="col-xl-9 col-md-8 col-12">
            <div class="card invoice-preview-card">
                <div class="card-body invoice-padding pb-0">
                    <!-- Header starts -->
                    <div class="d-flex justify-content-between flex-md-row flex-column invoice-spacing mt-0">
                        <div>
                            <div class="logo-wrapper">
                                <img src="<?= base_url(); ?>/app-assets/images/mambos/logo.png" style="max-height: 50px;" />
                            </div>
                            <p class="card-text mb-25">17 Park Street, Cnr Kwame</p>
                            <p class="card-text mb-25">Harare CBD, Zimbabwe</p>
                            <p class="card-text mb-0">+263 242 712345</p>
                        </div>
                        <div class="mt-md-0 mt-2">
                            <h4 class="invoice-title">
                                Order
                                <span class="invoice-number">#<?= $order["order_id"]; ?></span>
                            </h4>
                            <div class="invoice-date-wrapper">
                                <p class="invoice-date-title ">Date:</p>
                                <p class="invoice-date"><?= date("j M Y", round($order["time"]/1000)); ?></p>
                            </div>
                            <div class="invoice-date-wrapper">
                                <p class="invoice-date-title ">Delivery:</p>
                                <p class="invoice-date"><?= $order["delivery_date"]; ?></p>
                            </div>
                            <div class="invoice-date-wrapper">
                                <p class="invoice-date-title ">Time:</p>
                                <p class="invoice-date"><?= $order["delivery_time"]; ?></p>
                            </div>
                        </div>
                    </div>
                    <!-- Header ends -->
                </div>

                <hr class="invoice-spacing" />

                <!-- Address and Contact starts -->
                <div class="card-body invoice-padding pt-0">
                    <div class="row invoice-spacing">
                        <div class="col-xl-8 p-0">
                            <h6 class="mb-2">Deliver To:</h6>
                            <h6 class="mb-25"><?= $order["firstname"]; ?> <?= $order["lastname"]; ?></h6>
                            <p class="card-text mb-25"><?= $order["delivery_location"]; ?></p>
                            <p class="card-text mb-25">Distance: <?= $order["distance"]; ?>, Duration: <?= $order["duration"]; ?></p>
                            <p class="card-text mb-25">Phone: <?= $order["mobile"]; ?></p>
                            <p class="card-text mb-0">Email: <?= $order["email"]; ?></p>
                        </div>
                        <div class="col-xl-4 p-0 mt-xl-0 mt-2">
                            <h6 class="mb-2">Payment Details:</h6>
                            <table>
                                <tbody>
                                <tr>
                                    <td class="pe-1">Total Amount:</td>
                                    <td><span class="fw-bold">(<?= $order["currency"]; ?>) <?= money_format($order["order_total"], $order["currency"]); ?></span></td>
                                </tr>
                                <tr>
                                    <td class="pe-1">Method:</td>
                                    <td><?= $order["payment"]["method"]; ?></td>
                                </tr>
                                <tr>
                                    <td class="pe-1">TXN Id:</td>
                                    <?php if(isset($order["payment"]["result"]["txn_id"])){ ?>
                                    <td><?= $order["payment"]["result"]["txn_id"]; ?></td>
                                    <?php }else{ ?>
                                        <td>N/A</td>
                                    <?php } ?>
                                </tr>
                                <tr>
                                    <td class="pe-1">Payment Date:</td>
                                    <?php if(isset($order["payment"]["order_placed"])){ ?>
                                        <td><?= $order["payment"]["order_placed"]["time"]; ?></td>
                                    <?php }else{ ?>
                                        <td>NOT PAID</td>
                                    <?php } ?>
                                </tr>
                                <tr>
                                    <td class="pe-1">Payment Status:</td>
                                    <?php if(isset($order["payment"]["result"]["payment_status"])){ ?>
                                        <td><?= $order["payment"]["result"]["payment_status"]; ?></td>
                                    <?php }else{ ?>
                                        <td>NOT PAID</td>
                                    <?php } ?>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Address and Contact ends -->

                <!-- Invoice Description starts -->
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th class="py-1">Order Items</th>
                            <th class="py-1">Quantity</th>
                            <th class="py-1">Unit Price</th>
                            <th class="py-1">Total</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php foreach($order["cart_object"]["cart"] as $cartItemId=>$cartItem){ ?>

                        <tr>

                            <td class="py-1">
                                <p class="card-text fw-bold mb-25">
                                    <?= $cartItem["product_name"]; ?>
                                </p>
                                <p class="card-text text-nowrap">
                                    <?= $cartItem["product_except"]; ?>
                                </p>
                            </td>

                            <td class="py-1 px-3" style="text-align: right">
                                <span class="fw-bold text-center"><?= $cartItem["product_qty"]; ?></span>
                            </td>
                            <td class="py-1 px-3" style="text-align: right">
                                <span class="fw-bold text-right"> <?= money_format($cartItem["product_price_regular"], $order["currency"]); ?></span>
                            </td>
                            <td class="py-1 px-3" style="text-align: right">
                                <span class="fw-bold text-right"> <?= money_format($cartItem["product_price_grand_total"], $order["currency"]); ?></span>
                            </td>
                        </tr>

                        <?php } ?>

                        </tbody>
                    </table>
                </div>

                <div class="card-body invoice-padding pb-0" style="border-top: 1px solid #ebe9f1;background-color: #f7f7f7;">
                    <div class="row invoice-sales-total-wrapper">
                        <div class="col-md-9 order-md-1 order-2 mt-md-0 mt-3">
                            <p class="card-text mb-0">
                                <span class="fw-bold">Call Center:</span> <span class="ms-75">Fadzai</span>
                            </p>
                            <p class="card-text mb-0">
                                <?php if(isset($order["delivery_address"]["address_instructions"])){ ?>
                                <span class="fw-bold">Address Instructions:</span> <span class="ms-75"><?= $order["delivery_address"]["address_instructions"]; ?></span>
                                <?php } ?>
                            </p>
                            <p class="card-text mb-0">
                                <span class="fw-bold">Order Notes:</span> <span class="ms-75">N/A</span>
                            </p>
                        </div>
                        <div class="col-md-3 d-flex justify-content-end order-md-2 order-1 order-costs-bg" style=" background-color: #e32627; color: white; padding: 20px; ">
                            <div class="invoice-total-wrapper">
                                <div class="invoice-total-item">
                                    <p class="invoice-total-title">Subtotal:</p>
                                    <p class="invoice-total-amount"><?= money_format($order["costs"]["subtotal"], $order["currency"]); ?></p>
                                </div>
                                <div class="invoice-total-item">
                                    <p class="invoice-total-title">Discount:</p>
                                    <p class="invoice-total-amount"><?= money_format($order["costs"]["discount"], $order["currency"]); ?></p>
                                </div>
                                <div class="invoice-total-item">
                                    <p class="invoice-total-title">Delivery:</p>
                                    <p class="invoice-total-amount"><?= money_format($order["costs"]["delivery"], $order["currency"]); ?></p>
                                </div>
                                <hr class="my-50" />
                                <div class="invoice-total-item">
                                    <p class="invoice-total-title">Total:</p>
                                    <p class="invoice-total-amount"><?= money_format($order["costs"]["total"], $order["currency"]); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Invoice Description ends -->

                <hr class="invoice-spacing" />

                <!-- Invoice Note starts -->
                <div class="card-body invoice-padding pt-0">
                    <div class="row">
                        <div class="col-12">
                            <h3 class="fw-bold">Order Status: <?= $order["state"]; ?></h3>
                            <?php if(is_array($order["status"])){ ?>
                            <?php foreach($order["status"] as $status){ ?>
                                <?php if(isset($status["state"])){ ?>
                                <?php if($status["state"] === $order["state"]){ ?>
                                    <span><?= $status["desc"]; ?></span><br/>
                                    <span>Updated: <?= date("j M Y, h:i A", round($status["updated_time"]/1000)); ?></span>
                                <?php } ?>
                                <?php } ?>
                            <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <!-- Invoice Note ends -->
            </div>
        </div>
        <!-- /Invoice -->

        <!-- Invoice Actions -->
        <div class="col-xl-3 col-md-4 col-12 invoice-actions mt-md-0 mt-2">
            <div class="card">
                <div class="card-body">
                    <button class="btn btn-primary w-100 mb-75" data-bs-toggle="modal" data-bs-target="#send-invoice-sidebar">
                        Send Invoice
                    </button>
                    <button class="btn btn-outline-secondary w-100 btn-download-invoice mb-75" onclick="window.location.href='<?= base_url(); ?>/dashboard/order_download/pdf/<?= $order["order_id"]; ?>'">Download</button>
                    <a class="btn btn-outline-secondary w-100 mb-75" href="<?= base_url(); ?>/dashboard/order_print_preview/<?= $order["order_id"]; ?>" target="_blank"> Print </a>
                    <button class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#add-payment-sidebar">
                        Add Payment
                    </button>
                </div>
            </div>

            <div class="card" style="margin-top: 35px;">
                <div class="card-body">
                    <h4>Update Order Status</h4>
                    <select class="form-control" style="margin: 20px auto; width: 100%;" id="order_statuses_list_view" onchange="updateOrderStatusChanged()">
                        <option value="0">Select Status</option>
                        <?php

                        $statuses_txt = acme_get_statuses_text();

                        $statuses_obj = json_decode($statuses_txt, 1);

                        foreach($statuses_obj as $key=>$obj){

                        ?>

                            <option title="<?= $obj["desc"]; ?>" value="<?= $key; ?>"><?= $obj["state"]; ?></option>

                        <?php } ?>

                    </select>

                    <textarea class="form-control" style="margin: 20px auto; width: 100%; height: 120px;" id="order_statuses_comments"></textarea>

                    <div class="form-check form-check-inline  form-check-secondary" style="margin: 20px 0; display: none;" id="send_order_to_kdu_container" >
                        <input class="form-check-input" type="checkbox" id="send_order_to_kdu" value="checked" >
                        <label class="form-check-label" for="send_order_to_kdu">Send Order to th Kitchen (KDU)</label>
                    </div>

                    <input type="hidden" id="order_statuses_order_id" value="<?= $order["order_id"]; ?>" />

                    <input type="hidden" id="order_statuses_uid" value="<?= $order["uid"]; ?>" />

                    <button class="btn btn-success w-100" onclick="updateOrderStatus()" >
                        Update Status
                    </button>

                </div>
            </div>

            <div class="card" style="margin-top: 35px;">
                <div class="card-body">
                    <h4>Drivers</h4>
                    <select class="form-control" style="margin: 20px auto; width: 100%;" id="order_drivers_list_view" onchange="updateDriverStatusChanged()">
                        <option value="0">Select Driver</option>
                        <?php

                        foreach($drivers as $key=>$driver){

                            ?>

                            <option value="<?= $key; ?>"><?= $driver["name"]; ?> </option>

                        <?php } ?>

                    </select>

                    <select class="form-control" style="margin: 20px auto; width: 100%;" id="order_vehicles_list_view" >
                        <option value="0">Select Vehicle</option>
                        <option>ADK 8372</option>
                        <option>ACS 5352</option>
                        <option>AHG 9877</option>
                        <option>ADK 1353</option>
                        <option>AEX 0743</option>
                    </select>

                    <button class="btn btn-success w-100" onclick="assignDriver()" >
                        Assign Driver
                    </button>

                </div>
            </div>

        </div>
        <!-- /Invoice Actions -->
    </div>
</section>

<!-- Send Invoice Sidebar -->
<div class="modal modal-slide-in fade" id="send-invoice-sidebar" aria-hidden="true">
    <div class="modal-dialog sidebar-lg">
        <div class="modal-content p-0">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
            <div class="modal-header mb-1">
                <h5 class="modal-title">
                    <span class="align-middle">Send Invoice</span>
                </h5>
            </div>
            <div class="modal-body flex-grow-1">
                <form>
                    <div class="mb-1">
                        <label for="invoice-from" class="form-label">From</label>
                        <input type="text" class="form-control" id="invoice-from" value="Mambo's Chicken Dlivery <delivery@mamboschicken.com>" placeholder="company@email.com" />
                    </div>
                    <div class="mb-1">
                        <label for="invoice-to" class="form-label">To</label>
                        <input type="text" class="form-control" id="invoice-to" value="<?= $order["email"]; ?>" placeholder="company@email.com" />
                    </div>
                    <div class="mb-1">
                        <label for="invoice-subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="invoice-subject" value="Invoice for Order#<?= $order["order_id"]; ?>: <?= $order["cart_title"]; ?>" placeholder="Invoice regarding goods" />
                    </div>
                    <div class="mb-1">
                        <label for="invoice-message" class="form-label">Message</label>
                        <textarea class="form-control" name="invoice-message" id="invoice-message" cols="3" rows="11" placeholder="Message...">
Dear <?= $order["firstname"]; ?>,

Thank you for your business, always a pleasure to work with you!

We have generated a new invoice in the amount of <?= money_format($order["costs"]["total"], $order["currency"]); ?>

We would appreciate payment of this invoice.

                        Kind regards

                        Mambo's Chicken</textarea>
                    </div>
                    <div class="mb-1">
                                        <span class="badge badge-light-primary">
                                            <i data-feather="link" class="me-25"></i>
                                            <span class="align-middle"><a href="<?= base_url(); ?>/dashboard/order_download/pdf/<?= $order["order_id"]; ?>">Invoice_<?= $order["order_id"]; ?>.pdf Attached</a></span>
                                        </span>
                    </div>
                    <div class="mb-1 d-flex flex-wrap mt-2">
                        <button type="button" class="btn btn-primary me-1" data-bs-dismiss="modal">Send</button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Send Invoice Sidebar -->

<!-- Add Payment Sidebar -->
<div class="modal modal-slide-in fade" id="add-payment-sidebar" aria-hidden="true">
    <div class="modal-dialog sidebar-lg">
        <div class="modal-content p-0">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">×</button>
            <div class="modal-header mb-1">
                <h5 class="modal-title">
                    <span class="align-middle">Add Payment</span>
                </h5>
            </div>
            <div class="modal-body flex-grow-1">
                <form>
                    <!--
                    <div class="mb-1">
                        <input id="payment-balance" class="form-control" type="text" value="Invoice Balance: 5000.00" disabled />
                    </div>

                    let uid = $("#payment-order-uid").val();

                    let order_id = $("#payment-order-id").val();

                    let order_number = $("#payment-order-number").val();

                    let date = $("#payment-date").val();

                    let note = $("#payment-note").val();

                    let method = $('#payment-method').val();

                    let amount = parseFloat($("#payment-amount").val());

                    let currency = $("#payment-currency").val();

                    let status = $("#payment-status").val();

                    -->

                    <input id="payment-order-uid" type="hidden" value="<?= $order["uid"]; ?>" />

                    <input id="payment-order-id" type="hidden" value="<?= $order["order_id"]; ?>" />

                    <input id="payment-order-number" type="hidden" value="<?= $order["order_number"]; ?>" />

                    <div class="mb-1">
                        <label class="form-label" for="amount">Payment Amount</label>
                        <input id="payment-amount" class="form-control" type="number" placeholder="$1000" />
                    </div>
                    <div class="mb-1">
                        <label class="form-label" for="payment-date">Payment Date</label>
                        <input id="payment-date" class="form-control date-picker" type="text" />
                    </div>
                    <div class="mb-1">
                        <label class="form-label" for="payment-method">Payment Method</label>
                        <select class="form-select" id="payment-method">
                            <option value="" selected disabled>Select payment method</option>
                            <option value="Bank_Transfer">Bank Transfer</option>
                            <option value="Card_Debit">Debit Card</option>
                            <option value="Card_Credit">Credit</option>
                            <option value="Paypal">Paypal</option>
                            <option value="Mobile_Ecocash">Ecocash</option>
                            <option value="Mobile_OneMoney">OneMoney</option>
                            <option value="Mobile_Telecash">Telecash</option>
                            <option value="Zipit">Zipit</option>
                            <option value="Card_Swipe">Swipe</option>
                            <option value="Cash_USD">Cash - USD</option>
                            <option value="Cash_RTGS">Cash - (ZWL) RTGS</option>
                            <option value="Cash_Rands">Cash - RANDS</option>
                            <option value="Cash_Euros">Cash - EUROS</option>
                            <option value="Cash_Pounds">Cash - POUNDS</option>
                            <option value="Cash_Pula">Cash - PULA</option>
                            <option value="Cash_Other">Cash - OTHER</option>
                        </select>
                    </div>
                    <div class="mb-1">
                        <label class="form-label" for="payment-currency">Currency</label>
                        <select class="form-select" id="payment-currency">
                            <option value="" selected disabled>Select payment currency</option>
                            <option value="ZWL">Zimbabwean Dollars</option>
                            <option value="USD">United States Dollars</option>
                            <option value="ZAR">Rands</option>
                        </select>
                    </div>
                    <div class="mb-1">
                        <label class="form-label" for="payment-status">Payment Status</label>
                        <select class="form-select" id="payment-status">
                            <option value="" selected disabled>Select payment status</option>
                            <option value="COMPLETED">COMPLETED</option>
                            <option value="PENDING">PENDING</option>
                            <option value="FAILED">FAILED</option>
                        </select>
                    </div>
                    <div class="mb-1">
                        <label class="form-label" for="payment-note">Internal Payment Note</label>
                        <textarea class="form-control" id="payment-note" rows="5" placeholder="Internal Payment Note"></textarea>
                    </div>
                    <div class="d-flex flex-wrap mb-0">
                        <button type="button" class="btn btn-primary me-1" onclick="addPaymentMethod()">Send</button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Add Payment Sidebar -->

<?php } ?>
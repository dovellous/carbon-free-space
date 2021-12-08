CREATE NEW
<hr/>
<?php

$att = array(
    "method" => "post"
);

echo form_open_multipart("auth/users/insert_item", $att); ?>
<hr/>
<?php

foreach($columns as $col){
    echo "<tr><td>$col</td><td><input type='text' name='".$col."' value='"."'></td></tr>";
}

?>
<hr/>
<?php

echo form_submit();

echo form_close(); ?>

<?php print("<pre>".print_r(get_defined_vars(),true)."</pre>"); ?>
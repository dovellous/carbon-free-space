UPDATE
<hr/>

<?php echo form_open(base_url("public/auth/users/update_item/" . $rows[0]["uuid"]),
    ['id' => 'frmUsers', 'method'=>'post']); ?>

    <table><?php


foreach($columns as $col){
    echo "<tr><td>$col</td><td><input type='text' name='".$col."' value='".$rows[0][$col]."'></td></tr>";
}


?>
        <input type="submit">
        </table>
    </form>
<?php print("<pre>".print_r(get_defined_vars(),true)."</pre>"); ?>
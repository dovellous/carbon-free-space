<?php 

namespace Acme\Core\System\Libraries;

if (!defined('ACME_NAMESPACE')) acme_exception(null, 'The application namespace is undefined. Please check your installation');

class CategoriesLibrary
{


    public $output_began = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function get_top_level($id=null, $include_public_only=true)
    {

        if($id == null || $id == 0){

            $sql = "select * from `tbl_categories` where id < 1000 AND id <> 0 ";

        }else{

            $sql = "select * from `tbl_categories` where parent_id = '$id' ";

        }

        if($include_public_only){

            $sql .= " and `tbl_categories`.`is_public` = 1";

        }

        $query = $this->db->query($sql);

        $result_array = array();

        $rows = $query->result_array();

        foreach($rows as $row){

            $parent_name = $this->get_category_name_by_id($row["parent_id"]);

            $row["parent_name"] = $parent_name;

            $result_array[] = $row;

        }

        return $result_array;

    }

    public function save_a_category(){

        $categories = $this->input->post("category_names");

        $data = array();

        foreach($categories as $category){

            $data[] = array(
                "category_name" => $category,
                "slug" => $category,
                "parent_id" => $this->input->post("parent_id"),
                "image" => 0,
                "date_updated" => date("Y-m-d H:i:s"),
                "is_public"=>1
            );

        }

        $this->db->insert_batch('`tbl_categories`', $data);

    }

    public function ban_category(){

        $id = $this->input->post("id");

        $data = array("is_public"=>0);

        $this->db->where("id", $id);

        $id = $this->db->update('`tbl_categories`', $data);

        echo json_encode(array("id"=>$id));

    }

    public function assign_category($id, $new_parent){

        $data = array("parent_id"=>$new_parent);

        $this->db->where("id", $id);

        $uid = $this->db->update('`tbl_categories`', $data);

        return array("id"=>$id, "new_parent"=>$new_parent, "uid"=>$uid);

    }

    public function unban_category(){

        $id = $this->input->post("id");

        $data = array("is_public"=>1);

        $this->db->where("id", $id);

        $this->db->update('`tbl_categories`', $data);

        echo json_encode(array("id"=>$id));

    }

    public function get_all($include_public_only=true)
    {
        $sql = "select * from tbl_categories";

        if($include_public_only){

            $sql .= " where `tbl_categories`.`is_public` = 1";

        }

        $query = $this->db->query($sql);

        //var_dump($sql);

        //echo $this->db->last_query();

        return $query->result_array();

    }


    public function get_from_ids()
    {

        $ids = $this->input->post("ids");

        $sql = "select * from tbl_categories where id in ($ids)";

        $query = $this->db->query($sql);

        //echo $this->db->last_query();

        $result = $query->result_array();

        //var_dump($result);

        return $result;

    }

    public function get_category_children()
    {

        $parent_id = $this->input->post("parent_id");

        $column =  $this->input->post("column");

        if($column == NULL){

            $column = "parent_id";

        }

        $sql = "SELECT * FROM `tbl_categories` where `$column` = $parent_id";

        $query = $this->db->query($sql);

        return $query->result_array();
    }

    public function get_category_by_id($id, $columns=array())
    {

        if(!empty($columns)){

            $columns_list = implode(",", $columns);

        }else{

            $columns_list = "*";

        }

        $sql = "SELECT $columns_list FROM `tbl_categories` WHERE category_id = '" . $id."'";

        $query = $this->db->query($sql);

        $row = $query->row_array();

        return $row;

    }

    public function get_category_parent_by_id($id, $columns=array())
    {

        $row = $this->get_category_by_id($id, array("parent_id"));

        if(!empty($row) && isset($row["category_id"])){

            return $this->get_category_by_id($row["category_id"], $columns);

        }else{

            return null;

        }

    }

    public function get_ancestors($id, $include_public_only=true)
    {

        $sql = "SELECT * FROM `tbl_categories`";

        if($include_public_only){

            $sql .= " where `tbl_categories`.`is_public` = 1";

        }

        if($id > 0){

            $sql .= " and id = '$id'";

        }

        $query = $this->db->query($sql);

        $result = $query->row_array();

        if($result != NULL){

            if(count($result)){

                if($result["parent_id"] != -1){

                    try {

                        $this->category_ids[] = array("id"=>$result["id"], "parent_id"=>$result["parent_id"], "category_name"=>$result["category_name"],
                            "image"=>$result["image"]);

                        $this->get_ancestors($result["parent_id"]);

                    } catch (Exception $e) {

                    }

                }

            }

        }

        return array_reverse($this->category_ids);


    }

    public function get_descendants($id, $include_public_only=true)
    {

        $sql = "SELECT * FROM `tbl_categories`";

        if($include_public_only){

            $sql .= " where `tbl_categories`.`is_public` = 1";

        }

        if($id > 0){

            $sql .= " and parent_id = '$id'";

        }

        $query = $this->db->query($sql);

        $result = $query->result_array();

        if($result != NULL){

            if(count($result)){

                foreach($result as $_result){

                    if($_result["id"] != -1 && $_result["parent_id"] != -1){

                        try {

                            $this->category_ids[] = array("id"=>$_result["id"], "parent_id"=>$_result["parent_id"], "category_name"=>$_result["category_name"]);

                            $this->get_descendants($_result["id"]);

                        } catch (Exception $e) {

                        }

                    }

                }

            }

        }

        return array_reverse($this->category_ids);


    }

    public function get_descendants_cats_ids($id, $include_public_only=true)
    {

        $result = $this->get_descendants($id);

        $array = array($id);

        foreach($result as $arr){

            $array[] = $arr["id"];

        }

        return $array;

    }

    public function get_ancestors_cats_ids($id, $include_public_only=true)
    {

        $result = $this->get_ancestors($id);

        $array = array($id);

        foreach($result as $arr){

            $array[] = $arr["id"];

        }

        return $array;

    }

    public function get_descendants_list($category){

        $all_categories = array();

        $cats           = $this->Categories_model->get_descendants_cats_ids( $category );

        $all_categories = array_unique( array_merge( $all_categories, $cats ), SORT_REGULAR );

        $categories     = implode( ",", $all_categories );

        return $categories;

    }

    public function get_ancestors_list($category){

        $all_categories = array();

        $cats           = $this->Categories_model->get_ancestors_cats_ids( $category );

        $all_categories = array_unique( array_merge( $all_categories, $cats ), SORT_REGULAR );

        $categories     = implode( ",", $all_categories );

        return $categories;

    }

    public function get_descendants_json($id, $include_public_only=true)
    {

        $result = get_descendants($id);

        return $result;

    }

    public function get_children($id, $include_public_only=true)
    {
        $sql = "SELECT * FROM `tbl_categories`";

        if($include_public_only){

            $sql .= " where `tbl_categories`.`is_public` = 1";

        }

        if($id > 0){

            $sql .= " and parent_id = $parent_id";

        }

        $query = $this->db->query($sql);

        return $query->result_array();
    }

    public function get_grand_children($parent_id, $include_public_only=true)
    {
        $sql = "SELECT * FROM `tbl_categories`";

        if($include_public_only){

            $sql .= " and `tbl_categories`.`is_public` = 1";

        }

        if($id > 0){

            $sql .= " and parent_id = $parent_id";

        }

        $query = $this->db->query($sql);

        return $query->result_array();
    }


    /*
    echo '<pre>';
    print_r(fetch_recursive(buildtree($a), 3));
    echo '</pre>';
    */
    public function fetch_recursive($src_arr, $currentid, $parentfound = false, $cats = array())
    {
        foreach($src_arr as $row)
        {
            if((!$parentfound && $row['id'] == $currentid) || $row['parent_id'] == $currentid)
            {
                $rowdata = array();
                foreach($row as $k => $v)
                    $rowdata[$k] = $v;
                $cats[] = $rowdata;
                if($row['parent_id'] == $currentid)
                    $cats = array_merge($cats, fetch_recursive($src_arr, $row['id'], true));
            }
        }
        return $cats;
    }

    public function childCategoriesArray() {
        $childCategories = array();
        $result = $this->get_all();
        foreach($result as $row) {
            if ($row['parent_id']) {
                if (!isset($childCategories[$row['parent_id']])) $childCategories[$row['parent_id']] = array();
                $childCategories[$row['parent_id']][] = $row['id'];
            }
        }
        return $childCategories;
    }

    public function getRecursiveCategories($id) {
        $childCategories = $this->childCategoriesArray();
        $ret = array();
        if (!isset($childCategories[$id])) return $ret;
        foreach ($childCategories[$id] as $childId) {
            $ret[] = $childId;
            $ret = array_merge($ret, $this->getRecursiveCategories($childId, $childCategories));
        }
        return $ret;
    }

    public function buildtree($src_arr, $parent_id = 0, $tree = array())
    {
        foreach($src_arr as $idx => $row)
        {
            if($row['parent_id'] == $parent_id)
            {
                foreach($row as $k => $v)
                    $tree[$row['id']][$k] = $v;
                unset($src_arr[$idx]);
                $tree[$row['id']]['children'] = $this->buildtree($src_arr, $row['id']);
            }
        }
        ksort($tree);
        return $tree;
    }

    function get_categories_tree(){

        $cats = $this->get_all();

        $result = $this->buildtree($cats);

        return $result;

    }


    public function ulTree( $tree, $class='', $item_p=false) {

        if(count($tree) != 0){

            if(!$this->output_began){
                $class = '  class="category-selector"';
            }

            $this->output_began = true;

            $html = '<ul  '.$class.'>';

            $script = "";



            $ul_attr = "";

            $html .= "<li id='not-valid'  data-id='-10' data-parent-id='-10' data-category-name='-10' > --- SELECT CATEGORY ---</li>";

            if($item_p){

                $html .= "<li id='".$item_p['id']."'  data-id='".$item_p['id']."' data-parent-id='".$item_p['parent_id']."' data-category-name='".$item_p['category_name']."' ><strong>".$item_p['category_name']." </strong></li>";

            }

            foreach ( $tree as $item ) {

                if(isset( $item['children'] ) && count($item['children']) > 1 ){
                    $ul_attr = '  class="dl-submenu"';
                }

                $has_child = array_key_exists("children", $item);

                $children = 0;

                if($has_child){

                    $children = count($item['children']);

                }

                if( $children == 0){
                    $script = " onclick='select_this_sub_cat(this);' ";
                }

                $ddown_text = '';
                $ul_inner = $item['category_name'];
                if( isset ($item['children'])) {
                    $ddown_text = "data-dropdown-text = '" . $item['category_name'] . "'";
                    $ul_inner = "";
                }
                $html .= "<li id='".$item['id']."'  data-id='".$item['id']."' data-parent-id='".$item['parent_id']."' data-category-name='".$item['category_name']."' ".$script." ".$ddown_text.">".$ul_inner;
                if ( isset( $item['children'] ) ) {
                    $html .= $this->ulTree( $item['children'], $ul_attr, $item );
                }
                $html .= " </li>";
            }
            $html .= '</ul>';

            return $html;

        }
    }

    public function ollitree( $tree, $class='' ) {

        if(count($tree) != 0){

            if(!$this->output_began){
                $class = '  class="dl-menu" style=" max-height: 189px; overflow: scroll; overflow-x: hidden; margin-top: 30px; border: 1px solid #b5b5b5"';
            }

            $this->output_began = true;

            $html = '<ul data-tree-children="'.count($tree).'" '.$class.'>';

            $script = "";

            if(isset( $item['children'] )){
                $html .= '<li class="dl-back"><a href="#">back</a></li>';
            }

            $ul_attr = "";
            foreach ( $tree as $item ) {

                if(isset( $item['children'] ) && count($item['children']) > 1 ){
                    $ul_attr = '  class="dl-submenu"';
                }

                $has_child = array_key_exists("children", $item);

                $children = 0;

                if($has_child){

                    $children = count($item['children']);

                }

                if( $children == 0){
                    $script = " onclick='select_this_sub_cat(this);' ";
                }

                $html .= "<li id='".$item['id']."'  data-id='".$item['id']."' data-parent-id='".$item['parent_id']."' data-category-name='".$item['category_name']."' ".$script." ><a href='#'>".$item['category_name']."</a>";
                if ( isset( $item['children'] ) ) {
                    $html .= $this->ollitree( $item['children'], $ul_attr );
                }
                $html .= " </li>";
            }
            $html .= '</ul>';

            return $html;

        }
    }

    public function get_title(Array $arr, $find, $firstLevel = true) {
        $resultArray = array();
        foreach($arr as $val){
            if (isset($val['url']) && $val['url'] == $find) {
                return array('<li><a href="' . $val['url'] . '">' . $val['title'] . '</a></li>');
            }
            if (isset($val['sub'])) {
                $result = getTitle( $val['sub'], $find, false);
                if($result){
                    $resultArray = array_merge($result);
                    $resultArray[] = '<li>' . $val['title'] . '</li>';
                    if(!$firstLevel){
                        return $resultArray;
                    }
                }
            }
        }
        if(count($resultArray)){
            return implode(array_reverse($resultArray));
        }
        return false;
    }

    public function breadcrumb($menu, $id) {

        $it = new ParentKeysIterator($menu);
        foreach ($it as $key) {
            if ($key !== $id) continue;
            printf("Key '%s' found: '%s'\n", $key, implode("' -> '", $it->key()));
        }

    }

    public function breadcrumbs($category) {

        function get_breadcrumb($tree, $needle, &$result = array()) {

            $result = array();

            if (is_array($tree)) {
                foreach ($tree as $node) {
                    if ($node['category_name'] == $needle) {
                        $result[] = $node;
                        return true;
                    } else if (!empty($node['children'])) {
                        if (get_breadcrumb($node['children'], $needle, $result)){
                            $result[] = $node;
                            return true;
                        }
                    }
                }
            } else {
                if ($tree == $needle) {
                    $result[] = $tree;
                    return true;
                }
            }
            return false;
        }

        get_breadcrumb($this->get_categories_tree(), $category, $result);

        $li_array = array();

        $result = array_reverse($result);
        $count=0;
        foreach($result as $item){

            $active = "";
            $count++;
            if($count==count($result)){ $active = "active"; }

            if($item["id"]<100){
                $controller = 'products';
                $link = 'product_grid_buyer';
            }else{
                $controller = 'buyer';
                $link = 'get_suppliers_for_category';
            }

            $li_array[] = '<li class="breadcrumb-item '.$active.'"><a href="'.base_url().''.$controller.'/'.$link.'/'.$item["id"].'/'.$item["category_name"].'">'.$item["category_name"].'</a></li>';

        }

        return implode("\n", $li_array);

    }

    public function get_breadcrumb($tree, $needle, &$result = array()) {

        $result = array();

        if (is_array($tree)) {
            foreach ($tree as $node) {
                if ($node['name'] == $needle) {
                    $result[] = $node['name'];
                    return true;
                } else if (!empty($node['children'])) {
                    if (breadcrumb($node['children'], $needle, $result)){
                        $result[] = $node['name'];
                        return true;
                    }
                }
            }
        } else {
            if ($tree == $needle) {
                $result[] = $tree;
                return true;
            }
        }
        return false;
    }

}

class ParentKeysIterator extends RecursiveIteratorIterator
{
    public function __construct(array $array) {
        parent::__construct(new  RecursiveArrayIterator($array));
    }

    public function current() {
        return parent::key();
    }

    public function key() {
        return $this->getParentKeys();
    }


    public function getParentKeys() {
        $keys = [];
        for ($depth = $this->getDepth() - 1; $depth; $depth--) {
            array_unshift($keys, $this->getSubIterator($depth)->key());
        }
        return $keys;
    }
}

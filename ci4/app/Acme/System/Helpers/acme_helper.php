<?php //if (!defined('ACME_NAMESPACE')) exit('The application namespace is undefined. Please check your installation');

use Config\Services;

function acme_get_statuses_text(){
    $statuses_txt = '
            {
                "S00":                
                {
                    "id":"step-placed",
                    "title":"Order Placed",
                    "state":"NEW",
                    "desc":"Your order has been placed",
                    "updated":false,
                    "updated_time":0,
                    "updated_by_uid":0,
                    "update_comments":false,
                    "step":"00",
                    "visible":true
                },
                "S10":
                {
                    "id":"step-accepted",
                    "title":"Order Accepted",
                    "state":"ACCEPTED",
                    "desc":"Your order has been accepted",
                    "updated":false,
                    "updated_time":0,
                    "updated_by_uid":0,
                    "update_comments":false,
                    "step":"10",
                    "visible":true
                },
                "S20":
                {
                    "id":"step-preparing",
                    "title":"Order Preparation",
                    "state":"PREPARING",
                    "desc":"Your order is being prepared",
                    "updated":false,
                    "updated_time":0,
                    "updated_by_uid":0,
                    "update_comments":false,
                    "step":"20","visible":true
                },
                "S25":
                {
                    "id":"step-dispatched",
                    "title":"Order Ready",
                    "state":"DISPATCHED",
                    "desc":"Your order is now ready for {0}",
                    "updated":false,
                    "updated_time":0,
                    "updated_by_uid":0,
                    "update_comments":false,
                    "step":"25",
                    "visible":true
                },
                "S30":
                {
                    "id":"step-moving",
                    "title":"On The Way",
                    "state":"MOVING",
                    "desc":"Your order is on the way",
                    "updated":false,
                    "updated_time":0,
                    "updated_by_uid":0,
                    "update_comments":false,
                    "step":"30",
                    "visible":true
                },
                "S40":
                {
                    "id":"step-delivered",
                    "title":"Order Delivered",
                    "state":"DELIVERED",
                    "desc":"Your order has been delivered",
                    "updated":false,"updated_time":0,
                    "updated_by_uid":0,
                    "update_comments":false,
                    "step":"40",
                    "visible":true
                },
                "S50":
                {
                    "id":"step-completed",
                    "title":"Order Completed",
                    "state":"COMPLETED",
                    "desc":"Your order has been completed. Thank you for ordering with us. Please call again.",
                    "updated":false,
                    "updated_time":0,
                    "updated_by_uid":0,
                    "update_comments":false,
                    "step":"50",
                    "visible":false
                },
                "S60":
                {
                    "id":"step-on-hold",
                    "title":"Order On Hold",
                    "state":"ON_HOLD",
                    "desc":"Your order has been put onhold.",
                    "updated":false,
                    "updated_time":0,
                    "updated_by_uid":0,
                    "update_comments":false,
                    "step":"60",
                    "visible":false
                },
                "S70":
                {
                    "id":"step-cancelled",
                    "title":"Order Cancelled",
                    "state":"CANCELLED",
                    "desc":"Your order has been cancelled.",
                    "updated":false,
                    "updated_time":0,
                    "updated_by_uid":0,
                    "update_comments":false,
                    "step":"70",
                    "visible":false
                },
                "S80":
                {
                    "id":"step-draft",
                    "title":"Draft Order",
                    "state":"DRAFT",
                    "desc":"Your order has been saved as a draft pending confirmation.",
                    "updated":false,
                    "updated_time":0,
                    "updated_by_uid":0,
                    "update_comments":false,
                    "step":"80",
                    "visible":false
                }
            }';
    return $statuses_txt;
}

function money_format($val,$symbol='USD')
{

    $formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
    return $formatter->formatCurrency($val, $symbol);

}



if (!function_exists('acme_slug')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_slug($str, $delimiter = '-')
    {

        return strtolower(
            trim(
                preg_replace(
                    '/[\s-]+/',
                    $delimiter,
                    preg_replace(
                        '/[^A-Za-z0-9-]+/',
                        $delimiter,
                        preg_replace(
                            '/[&]/',
                            'and',
                            preg_replace(
                                '/[\']/',
                                '',
                                iconv(
                                    'UTF-8',
                                    'ASCII//TRANSLIT',
                                    $str
                                )
                            )
                        )
                    )
                ),
                $delimiter
            )
        );

    }

}

function acme_curl_post($url, $headers, $fields)
{


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

    $result = curl_exec($ch);

    if ($result === FALSE) {

        $result = curl_error($ch);

    }

    curl_close($ch);

    return $result;

}

if (!function_exists('acme_time_ago')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_time_ago($ts)
    {
        if (!ctype_digit($ts)) {
            $ts = strtotime($ts);
        }
        $diff = time() - $ts;
        if ($diff == 0) {
            return 'now';
        } elseif ($diff > 0) {
            $day_diff = floor($diff / 86400);
            if ($day_diff == 0) {
                if ($diff < 20) return 'just now';
                if ($diff < 60) return $diff . 'sec ago';
                if ($diff < 90) return '1 minute ago';
                if ($diff < 180) return 'a few minutes ago';
                if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
                if ($diff < 7200) return '1 hour ago';
                if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
            }
            if ($day_diff == 1) {
                return 'Yesterday';
            }
            if ($day_diff < 7) {
                return $day_diff . ' days ago';
            }
            if ($day_diff < 31) {
                return ceil($day_diff / 7) . ' weeks ago';
            }
            if ($day_diff < 60) {
                return 'last month';
            }
            return date('F Y', $ts);
        } else {
            $diff = abs($diff);
            $day_diff = floor($diff / 86400);
            if ($day_diff == 0) {
                if ($diff < 120) {
                    return 'in a minute';
                }
                if ($diff < 3600) {
                    return 'in ' . floor($diff / 60) . ' minutes';
                }
                if ($diff < 7200) {
                    return 'in an hour';
                }
                if ($diff < 86400) {
                    return 'in ' . floor($diff / 3600) . ' hours';
                }
            }
            if ($day_diff == 1) {
                return 'Tomorrow';
            }
            if ($day_diff < 4) {
                return date('l', $ts);
            }
            if ($day_diff < 7 + (7 - date('w'))) {
                return 'next week';
            }
            if (ceil($day_diff / 7) < 4) {
                return 'in ' . ceil($day_diff / 7) . ' weeks';
            }
            if (date('n', $ts) == date('n') + 1) {
                return 'next month';
            }
            return date('F Y', $ts);
        }
    }

    function acme_time_ago_2($time, $_this, $format = "Y-m-d H:i:s")
    {
        //var_dump($time); exit;
        if ($time == NULL) {
            return $time;
        }

        if (!is_object($_this)) {

            return $time;

        }

        $_this->load->library("TimeAgo");

        $timeAgo = $_this->timeago->load();

        $datetime = new DateTime();
        $newDate = $datetime->createFromFormat($format, $time);

        return $timeAgo->inWords($newDate);

    }

}

if (!function_exists('acme_secret')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_secret()
    {

        return md5(time() . rand(1000000, 999999));

    }

}

if (!function_exists('acme_token')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_token()
    {

        return sha1(acme_secret());

    }

}

if (!function_exists('acme_random_md5')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_random()
    {

        return rand(1000000, 999999);

    }

}

if (!function_exists('acme_random_md5')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_random_md5()
    {

        return md5(time() . rand(1000000, 999999));

    }

}

if (!function_exists('acme_ul_li_to_array')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_ul_li_to_array($dom)
    {

        require_once(APPPATH . "thirdparty" . "simplehtmldom" . DS . "simple_html_dom.php");

        $ul_li_array = array();

        function acme_walk_ul($ul, &$ar)
        {
            foreach ($ul->children as $li) {
                if ($li->tag != "li") {
                    continue;
                }
                $ul_li_array_2 = array();
                foreach ($li->children as $ulul) {
                    if ($ulul->tag != "ul") {
                        continue;
                    }
                    acme_walk_ul($ulul, $ul_li_array_2);
                }
                $ar[$li->find("a", 0)->plaintext] = $ul_li_array_2;
            }
        }

        acme_walk_ul($dom->find("ul", 0), $ul_li_array);

        return $ul_li_array;

    }

}

if (!function_exists('acme_array_to_ul_li')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_array_to_ul_li($data)
    {
        $menu = array();

        foreach ($data as $key => $value) {

            if (!is_null($value['parent_id'])) {

                if (isset($menu[$value['parent_id']])) {

                    $menu[$value['parent_id']][$key] = array();

                } else {

                    //Locate the correct path to the menu in question

                    $array = array($value['parent_id']);

                    acme_array_to_ul_li_deep($array, $value, $data);

                    $num = count($array) - 1;

                    $temp = &$menu[$array[$num--]];

                    for (; $num >= 0; --$num) {

                        if ($num == 0) {

                            $temp[$array[$num]][$key] = array();

                        } else {

                            $temp = &$temp[$array[$num]];

                        }
                    }
                }

            } else {

                $menu[$key] = array();

            }
        }

        acme_create_ul($menu, $data);

    }

}

if (!function_exists('acme_system_config')) {
    /**
     * return the day of the week
     *
     * @param string $config : describe var3
     * @return array 0 : return value
     */
    function acme_system_config($config)
    {
        
        if(is_array($config)){
        
            if(array_key_exists("system", $config)){
                
                $system_config = $config["system"];
        
            }else if(array_key_exists("system.config", $config)){
                
                $system_config = $config["system.config"];
                
            }else{
                
                $system_config = $config;
                
            }
            

        }else{
            
            $system_config = $config;
            
        }
        
        return $system_config;
        
            
    }

}

if (!function_exists('acme_array_to_ul_li_deep')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_array_to_ul_li_deep(&$array, $value, $data)
    {
        if (!is_null($data[$value['parent_id']]['parent_id'])) {

            $array[] = $data[$value['parent_id']]['parent_id'];

            acme_array_to_ul_li_deep($array, $data[$value['parent_id']], $data);

        }
    }

}

if (!function_exists('acme_array_flatten')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_array_flatten($array)
    {
        if (!is_array($array)) {
            return false;
        }
        $result = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = array_merge($result, array_flatten($value));
            } else {
                $result = array_merge($result, array($key => $value));
            }
        }

        return $result;

    }

}

if (!function_exists('acme_array_linear_2_multidimensional')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_array_linear_2_multidimensional($tmpArr)
    {

        $array = array();

        foreach (array_reverse($tmpArr) as $arr)
            $array = array($arr => $array);

        return $array;

    }

}

if (!function_exists('acme_create_ul')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_create_ul($menu, $data)
    {

        $html = '';

        $html .= '<ul>';

        foreach ($menu as $key => $value) {

            $html .= '<li>';

            $html .= $data[$key]['ItemText'];

            $html .= '</li>';

            if (!empty($value)) {

                $html .= '<li>';

                acme_create_ul($value, $data);

                $html .= '</li>';

            }

        }

        $html .= '</ul>';

        return $html;

    }

}


if (!function_exists('get_progress_color')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function get_progress_color($percentage)
    {

        $color = "pink";

        if ($percentage > 25) {
            $color = "pink";
        }

        if ($percentage > 50) {
            $color = "yellow";
        }

        if ($percentage > 75) {
            $color = "blue";
        }

        if ($percentage > 90) {
            $color = "green";
        }

        return $color;

    }

}


if (!function_exists('acme_camel_to_space')) {
    /**
     * @param null $string
     * @return mixed
     */
    function acme_camel_to_space($string = NULL)
    {
        $pattern = '/([A-Z])/';
        $replacement = ' ${1}';
        return preg_replace($pattern, $replacement, $string);
    }

}

if (!function_exists('acme_camel_to_underscore')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_camel_to_underscore($input)
    {

        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

}

if (!function_exists('acme_underscore_to_camel')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_underscore_to_camel($str = NULL, $firstcap = false, $sep = "_", $replacement = '')
    {
        $str = str_replace($sep, $replacement, ucwords($str, $sep));

        if (!$firstcap) {
            $str = lcfirst($str);
        }

        return $str;

    }

}

if (!function_exists('acme_underscore_to_space')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_underscore_to_space($string = NULL, $firstcap = false)
    {
        return acme_camel_to_space(acme_underscore_to_camel($string));
    }

}

if (!function_exists('acme_text_within_tags')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_text_within_tags($string, $tagname)
    {

        $pattern = "#<$tagname.*?>([^<]+)</$tagname>#";

        preg_match($pattern, $string, $matches);

        return $matches;

    }

}

if (!function_exists('acme_sanitise_email')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_sanitise_email($string = NULL)
    {
        return preg_replace('((?:\n|\r|\t|%0A|%0D|%08|%09)+)i', '', $string);
    }

}

if (!function_exists('acme_alpha_num')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_alpha_num($string = NULL)
    {
        return preg_replace('/[^a-zA-Z0-9]/', '', $string);
    }

}

if (!function_exists('acme_alpha_num_space')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_alpha_num_space($string = NULL)
    {
        return preg_replace('/[^a-zA-Z0-9\s]/', '', $string);
    }

}

if (!function_exists('acme_alpha_num_underscore')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_alpha_num_underscore($string = NULL)
    {
        return preg_replace('/[^a-zA-Z0-9_]/', '', $string);
    }

}

if (!function_exists('acme_alpha_num_dot')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_alpha_num_dot($string = NULL)
    {
        return preg_replace('/[^a-zA-Z0-9\.]/', '', $string);
    }

}

if (!function_exists('acme_num_only')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_num_only($string = NULL)
    {
        return preg_replace('/[^0-9\s]/', '', $string);
    }

}

if (!function_exists('acme_alpha_only')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_alpha_only($string = NULL)
    {
        return preg_replace('/[^a-zA-Z]/', '', $string);
    }

}

if (!function_exists('acme_convert_to_gsm')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_convert_to_gsm($t)
    {
        $t = strtr($t, array(
            "@" => "%00",
            "Â£" => "%01",
            "$" => "%02",
            "Â¥" => "%03",
            "Ã¨" => "%04",
            "Ã©" => "%05",
            "Ã¹" => "%06",
            "Ã¬" => "%07",
            "Ã²" => "%08",
            "Ã‡" => "%09",
            "\r\n" => "%0A",
            "\n" => "%0A",
            "\r" => "%0A",
            "Ã˜" => "%0B",
            "Ã¸" => "%0C",
            // "\n" => "%0D",
            "Ã…" => "%0E",
            "Ã¥" => "%0F",
            "Î" => "%10",
            "_" => "%11",
            "Î¦" => "%12",
            "Î" => "%13",
            "Î›" => "%14",
            "Î©" => "%15",
            "Î " => "%16",
            "Î¨" => "%17",
            "Î£" => "%18",
            "Î˜" => "%19",
            "Îž" => "%1A",
            // "escape" => "%1B",
            // "form feed" => "%1B%0A"
            "^" => "%1B%14",
            "{" => "%1B%28",
            "}" => "%1B%29",
            "\\" => "%1B%2F",
            "[" => "%1B%3C",
            "~" => "%1B%3D",
            "]" => "%1B%3E",
            "|" => "%1B%40",
            "â‚¬" => "%1B%65",
            "Ã†" => "%1C",
            "Ã¦" => "%1D",
            "ÃŸ" => "%1E",
            "Ã‰" => "%1F",
            " " => "%20",
            "!" => "%21",
            "\"" => "%22",
            "#" => "%23",
            "Â¤" => "%24",
            "%" => "%25",
            "&" => "%26",
            "\'" => "%27",
            "(" => "%28",
            ")" => "%29",
            "*" => "%2A",
            "+" => "%2B",
            "," => "%2C",
            "-" => "%2D",
            "." => "%2E",
            "/" => "%2F",
            //digits %30-%39
            ":" => "%3A",
            ";" => "%3B",
            "<" => "%3C",
            "=" => "%3D",
            ">" => "%3E",
            "?" => "%3F",
            "Â¡" => "%40",
            //upper chars %41-%5A
            "Ã" => "%5B",
            "Ã–" => "%5C",
            "Ã'" => "%5D",
            "Ãœ" => "%5E",
            "Â§" => "%5F",
            "Â¿" => "%60",
            //lower chars %61-%7A
            "Ã¤" => "%7B",
            "Ã¶" => "%7C",
            "Ã±" => "%7D",
            "Ã¼" => "%7E",
            "Ã " => "%7F",

            "Î'" => "%41",  //A
            "Î'" => "%42",  //B
            "Î•" => "%45",  //E
            "Î—" => "%48",  //H
            "Î™" => "%49",  //I
            "Îš" => "%4B",  //K
            "Îœ" => "%4D",  //M
            "Î" => "%4E",  //N
            "ÎŸ" => "%4F",  //O
            "Î¡" => "%50",  //P
            "Î¤" => "%54",  //T
            "Î§" => "%58",  //X
            "Î¥" => "%59",  //Y
            "Î–" => "%5A",  //Î–

            "Î±" => "%41",  //A
            "Î¬" => "%41",  //Î±
            "Î²" => "%42",  //Î²
            "Î³" => "%13",  //Î³
            "Îµ" => "%45",  //Îµ
            "Î­" => "%45",  //Î­
            "Î´" => "%10",  //Î´
            "Î¶" => "%5A",  //Î´
            "Î¹" => "%49",  //I
            "Î·" => "%48",  //Î´
            "Î®" => "%48",  //Î´
            "Î¯" => "%49",  //I
            "Îº" => "%4B",  //K
            "Î¼" => "%4D",  //M
            "Î½" => "%4E",  //N
            "Î¿" => "%4F",  //O
            "ÏŒ" => "%4F",  //O
            "Ï" => "%50",  //P
            "Ï" => "%54",  //T
            "Ï‡" => "%58",  //X
            "Ï…" => "%59",  //Y
            "Ï" => "%59",  //Y
            "Î¶" => "%5Î'",  //Î–

            "Ï†" => "%12",
            "Î³" => "%13",
            "Î»" => "%14",
            "Ï‰" => "%15",
            "ÏŽ" => "%15",
            "Ï€" => "%16",
            "Ïˆ" => "%17",
            "Ïƒ" => "%18",
            "Î¸" => "%19",
            "Î¾" => "%1A",

            "â‚¬" => "%1B%66",
        ));

        return $t;
    }

}

if (!function_exists('acme_remove_accent')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_remove_accent($str)
    {
        $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
        return str_replace($a, $b, $str);
    }

}

if (!function_exists('acme_clean_accent')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_clean_accent($str)
    {
        return preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'),
            array('', '-', ''), acme_remove_accent($str));
    }

}

if (!function_exists('acme_prepare_json')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_prepare_json($input)
    {

        //This will convert ASCII/ISO-8859-1 to UTF-8.
        //Be careful with the third parameter (encoding detect list), because
        //if set wrong, some input encodings will get garbled (including UTF-8!)
        $input = mb_convert_encoding($input, 'UTF-8', 'ASCII,UTF-8,ISO-8859-1');

        //Remove UTF-8 BOM if present, json_decode() does not like it.
        if (substr($input, 0, 3) == pack("CCC", 0xEF, 0xBB, 0xBF)) {

            $input = substr($input, 3);

        }

        return $input;

    }

}


if (!function_exists('acme_bullshit_meter')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_bullshit_meter($string, $bs)
    {
        /*** example bs ***/
        $bs = explode(",", $bs);

        /*** make the string lower case for comparison ***/
        $string = strtolower($string);

        /*** check the length of the string ***/
        $text_count = strlen($string);

        /*** set bs count to zero ***/
        $bs_count = 0;
        foreach ($bs as $text) {
            if ($pos = strpos($string, $text)) {
                /*** get the number of occurances ***/
                $occurances = substr_count($string, $text);

                /*** get the lenth of the bs ***/
                $bs_count += strlen($text) * $occurances;
            }
        }

        /*** do the percentage ***/
        $percent = (int)$bs_count / (int)$text_count * 100;

        /*** round to two places ***/
        return round($percent, 2);
    }

}

if (!function_exists('acme_highlight')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_highlight($word, $subject)
    {
        $split_subject = explode(" ", $subject);
        $split_word = explode(" ", $word);

        foreach ($split_subject as $k => $v) {
            foreach ($split_word as $k2 => $v2) {
                if ($v2 == $v) {
                    $split_subject[$k] = "<span class='highlight'>" . $v . "</span>";
                }
            }
        }
        return implode(' ', $split_subject);
    }

}

if (!function_exists('acme_color_progress')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_color_progress($percentage)
    {
        $colors = "FF0000|FF1000|FF2000|FF3000|FF4000|FF5000|FF6000|FF7000|FF8000|FF9000|FFA000|FFB000|FFC000|FFD000|FFE000|FFF000|FFFF00|F0FF00|E0FF00|D0FF00|C0FF00|B0FF00|A0FF00|90FF00|80FF00|70FF00|60FF00|50FF00|40FF00|30FF00|20FF00|10FF00";

        $colors_array = explode("|", $colors);

        $i = (int)round($percentage * count($colors_array) / 100);

        $color = $colors_array[$i];

        return "#" . $color;

    }

}

if (!function_exists('acme_color_full')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_color_full($color)
    {
        return preg_replace("/(?(?=[^0-9a-f])[^.]|(.))/i", '$1$1', $color);
    }

}

if (!function_exists('acme_hex_2_rgb')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_hex_2_rgb($hex)
    {
        $int = hexdec($hex);
        return array("red" => 0xFF & ($int >> 0x10), "green" => 0xFF & ($int >> 0x8), "blue" => 0xFF & $int);
    }

}

if (!function_exists('acme_valid_ip')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_valid_ip($ip)
    {
        return preg_match("/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])" .
            "(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/", $ip);
    }

}

if (!function_exists('acme_get_os')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_get_os($userAgent)
    {
        // Create list of operating systems with operating system name as array key
        $oses = array(
            'iPhone' => '(iPhone)',
            'Windows 3.11' => 'Win16',
            'Windows 95' => '(Windows 95)|(Win95)|(Windows_95)', // Use regular expressions as value to identify operating system
            'Windows 98' => '(Windows 98)|(Win98)',
            'Windows 2000' => '(Windows NT 5.0)|(Windows 2000)',
            'Windows XP' => '(Windows NT 5.1)|(Windows XP)',
            'Windows 2003' => '(Windows NT 5.2)',
            'Windows Vista' => '(Windows NT 6.0)|(Windows Vista)',
            'Windows 7' => '(Windows NT 6.1)|(Windows 7)',
            'Windows NT 4.0' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
            'Windows ME' => 'Windows ME',
            'Open BSD' => 'OpenBSD',
            'Sun OS' => 'SunOS',
            'Linux' => '(Linux)|(X11)',
            'Safari' => '(Safari)',
            'Macintosh' => '(Mac_PowerPC)|(Macintosh)',
            'QNX' => 'QNX',
            'BeOS' => 'BeOS',
            'OS/2' => 'OS/2',
            'Search Bot' => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp/cat)|(msnbot)|(ia_archiver)'
        );

        foreach ($oses as $os => $pattern) {

            // Loop through $oses array

            // Use regular expressions to check operating system type

            if (preg_match("/$pattern/", $userAgent)) { // Check if a value in $oses array matches current user agent.

                return $os; // Operating system was matched so return $oses key

            }

        }

        return 'Unknown'; // Cannot find operating system so return Unknown

    }

}

if (!function_exists('acme_check_path')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_check_path($path, $create_dir)
    {
        if (!file_exists($path)) {

            if ($create_dir) {

                return mkdir($path, 0777, true);

            } else {

                return false;

            }

        } else {

            return true;

        }

    }

}

if (!function_exists('acme_valid_filename')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_valid_filename($str)
    {

        $filename = preg_replace('/[^0-9a-zа-яіїё\`\~\!\@\#\$\%\^\*\(\)\; \,\.\'\-]/i', ' ', $str);

        $filename = trim($filename);

        $RemoveChars = array("([\40])", "([^a-zA-Z0-9-_\/])", "(-{2,})");

        $ReplaceWith = array("-", "", "-");

        return preg_replace($RemoveChars, $ReplaceWith, $filename);

    }

}

if (!function_exists('acme_us2fr')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_us2fr($date)
    {
        return preg_replace("/([0-9]{4})\/([0-9]{2})\/([0-9]{2})/i", "$3/$2/$1", $date);
    }

}

if (!function_exists('acme_fr2us')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_fr2us($date)
    {
        return preg_replace("/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/i", "$3/$2/$1", $date);
    }

}

if (!function_exists('acme_seconds_to_time')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_seconds_to_time($seconds)
    {
        $t = round($seconds);
        return sprintf('%02d:%02d:%02d', ($t / 3600), ($t / 60 % 60), $t % 60);

    }

}

if (!function_exists('acme_compare_nonce')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_compare_nonce($acme_post_nonce, $acme_session_nonce)
    {

        return true;

    }

}

if (!function_exists('acme_name_to_slug')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_name_to_slug($name)
    {

        return true;

    }

}

if (!function_exists('acme_name_to_alias')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_name_to_alias($name)
    {

        return true;

    }

}

if (!function_exists('acme_user_data')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_user_data($key, $user_data = NULL)
    {

        if (array_key_exists("oauth_data", $user_data) && array_key_exists("user_data", $user_data)) {

            $userdata = (array)$user_data["user_data"];

            if (isset($user_data["oauth_data"]) && array_key_exists($key, $user_data["oauth_data"])) {

                if (!empty($user_data["oauth_data"][$key])) {

                    return $user_data["oauth_data"][$key];

                }

            } else if (array_key_exists($key, $userdata)) {

                if (!empty($userdata[$key])) {

                    return $userdata[$key];

                }

            } else {

                return "";

            }
        }

    }

}

if (!function_exists('acme_form')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_form($k = null, $v = null)
    {

        $html = "";

        if ($k != null && $v != null) {

            if (is_array($v)) {

                if (array_key_exists("container_class", $v)) {

                    $html .= '<div class="' . $v["container_class"] . '">';

                }

                if (array_key_exists("label_attributes", $v)) {

                    $label_attributes = $v["label_attributes"];

                } else {

                    $label_attributes = array();

                }

                if (array_key_exists("extras", $v)) {

                    foreach ($v["extras"] as $k => $v) {

                        $data[$k] = $v;

                    }
                }

                $html .= acme_form_label($v["name"], $k, array('class' => "block"), $label_attributes);

                switch ($v["element"]) {

                    case "input" :
                        {

                            $data = array(
                                'type' => $v["type"],
                                'name' => $v["name"],
                                'id' => $k,
                                'value' => $v["value"],
                                'class' => $v["class"] . " " . ($v["required"] ? "required" : "")
                            );

                            $html .= acme_form_element('input', $data);
                            break;
                        }

                    case "textarea" :
                        {

                            $data = array(
                                'type' => $v["type"],
                                'name' => $v["name"],
                                'id' => $k,
                                'value' => $v["value"],
                                'class' => $v["class"] . " " . ($v["required"] ? "required" : "")
                            );

                            $html .= acme_form_element('textarea', $data);
                            break;
                        }

                    case "select" :
                        {

                            $data = array(
                                'type' => $v["type"],
                                'name' => $v["name"],
                                'id' => $k,
                                'value' => $v["value"],
                                'class' => $v["class"] . " " . ($v["required"] ? "required" : "")
                            );

                            $selected_items = $v["selected"];

                            $html .= acme_form_element('dropdown', $data, $v["options"], $selected_items);
                            break;
                        }

                    case "checkbox" :
                        {
                            $data = array(
                                'type' => $v["type"],
                                'name' => $v["name"],
                                'id' => $k,
                                'value' => $v["value"],
                                'checked' => $v["checked"],
                                'class' => $v["class"] . " " . ($v["required"] ? "required" : "")
                            );

                            $html .= acme_form_element('checkbox', $data);
                            break;
                        }

                    case "radio" :
                        {
                            $data = array(
                                'type' => $v["type"],
                                'name' => $v["name"],
                                'id' => $k,
                                'value' => $v["value"],
                                'checked' => $v["checked"],
                                'class' => $v["class"] . " " . ($v["required"] ? "required" : "")
                            );

                            $html .= acme_form_element('radio', $data);
                            break;
                        }

                    case "file" :
                        {

                            $data = array(
                                'type' => $v["type"],
                                'name' => $v["name"],
                                'id' => $k,
                                'value' => $v["value"],
                                'class' => $v["class"] . " " . ($v["required"] ? "required" : "")
                            );

                            $html .= acme_form_element('upload', $data);
                            break;
                        }

                    default :
                        {

                            break;

                        }

                }

                if (array_key_exists("container_class", $v)) {

                    $html .= '</div>';

                }

            }

        }

        return $html;

    }

}

if (!function_exists('acme_replace_strings')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_replace_strings($contents, $replacements)
    {

        foreach ($replacements as $k => $v) {

            if (!array($v)) {

                $contents = str_replace($k, $v, $contents);

            }

        }

        return $contents;

    }

}

if (!function_exists('acme_replace_array')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_replace_array($contents, $replacements)
    {

        preg_match_all('~\{\{(.*?)\}\}~s', $contents, $datas);

        $array = array('0' => $replacements);

        $html = $contents;

        foreach ($datas[1] as $value) {
            $html = str_replace($value, $array[0][$value], $html);
        }

        return str_replace(array("{", "}"), '', $html);

    }

}

if (!function_exists('acme_get_file_info')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_get_file_info($file)
    {
        return new SplFileInfo($file);
    }

}

if (!function_exists('acme_get_file_type')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_get_file_type($file)
    {
        return new SplFileInfo($file);
    }

}

if (!function_exists('acme_html_clean')) {
    /**
     * clear the html text rendered before
     */
    function acme_html_clean()
    {
        return '';
    }

}

if (!function_exists('acme_get_file_ext')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_get_file_ext($filename)
    {
        return preg_replace('/\?.*/', '', substr(strrchr($filename, '.'), 1));
    }

}

if (!function_exists('acme_get_file_mime')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_get_file_mime($file)
    {
        return new SplFileInfo($file);
    }

}

if (!function_exists('acme_format_size')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_format_size($size)
    {
        $mod = 1024;
        $units = explode(' ', 'B KB MB GB TB PB');
        for ($i = 0; $size > $mod; $i++) {
            $size /= $mod;
        }
        return round($size, 2) . ' ' . $units[$i];
    }

}

if (!function_exists('acme_dir_stats')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_dir_stats($directory)
    {

        $size = 0;
        $file_count = 0;
        $directory_count = 0;

        $pdf = array("total_size" => 0, "file_count" => 0, "files" => array());
        $ppt = array("total_size" => 0, "file_count" => 0, "files" => array());
        $doc = array("total_size" => 0, "file_count" => 0, "files" => array());
        $xls = array("total_size" => 0, "file_count" => 0, "files" => array());
        $img = array("total_size" => 0, "file_count" => 0, "files" => array());
        $txt = array("total_size" => 0, "file_count" => 0, "files" => array());
        $zip = array("total_size" => 0, "file_count" => 0, "files" => array());
        $msc = array("total_size" => 0, "file_count" => 0, "files" => array());

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) {

            $file_size = $file->getSize();
            $size += $file_size;
            $path = $file->getPathname();
            $name = $file->getFilename();
            $file_type = get_file_ext($path);

            $skip = false;

            if (is_dir($path)) {
                if ($name === ".") {
                    $directory_count++;
                }
                $skip = true;
            } else {
                $file_count++;
            }

            if (!$skip) {

                switch ($file_type) {

                    case "pdf" :
                        {
                            $pdf["total_size"] = $pdf["total_size"] + $file_size;
                            $pdf["file_count"] = $pdf["file_count"] + 1;
                            $pdf["files"][] = $file;
                            break;
                        }
                    case "pptx" :
                    case "ppt" :
                        {
                            $ppt["total_size"] = $ppt["total_size"] + $file_size;
                            $ppt["file_count"] = $ppt["file_count"] + 1;
                            $ppt["files"][] = $file;
                            break;
                        }
                    case "docx" :
                    case "doc" :
                        {
                            $doc["total_size"] = $doc["total_size"] + $file_size;
                            $doc["file_count"] = $doc["file_count"] + 1;
                            $doc["files"][] = $file;
                            break;
                        }
                    case "xlsx" :
                    case "xls" :
                        {
                            $xls["total_size"] = $xls["total_size"] + $file_size;
                            $xls["file_count"] = $xls["file_count"] + 1;
                            $xls["files"][] = $file;
                            break;
                        }
                    case "bmp" :
                    case "jpg" :
                    case "jpeg" :
                    case "png" :
                    case "gif" :
                    case "tiff" :
                        {
                            $img["total_size"] = $img["total_size"] + $file_size;
                            $img["file_count"] = $img["file_count"] + 1;
                            $img["files"][] = $file;
                            break;
                        }
                    case "rtf" :
                    case "txt" :
                        {
                            $txt["total_size"] = $txt["total_size"] + $file_size;
                            $txt["file_count"] = $txt["file_count"] + 1;
                            $txt["files"][] = $file;
                            break;
                        }
                    case "rar" :
                    case "tar" :
                    case "gz" :
                    case "zip" :
                        {
                            $zip["total_size"] = $txt["total_size"] + $file_size;
                            $zip["file_count"] = $txt["file_count"] + 1;
                            $zip["files"][] = $file;
                            break;
                        }
                    default :
                        {
                            $msc["total_size"] = $msc["total_size"] + $file_size;
                            $msc["file_count"] = $msc["file_count"] + 1;
                            $msc["files"][] = $file;
                            break;
                        }
                }

            }
        }

        return array("size_formatted" => format_size($size), "size" => $size, "file_count" => $file_count, "directory_count" => $directory_count, "files" => array(
            "pdf" => $pdf,
            "xls" => $xls,
            "doc" => $doc,
            "ppt" => $ppt,
            "img" => $img,
            "txt" => $txt,
            "zip" => $zip,
            "msc" => $msc
        ));

    }

}

if (!function_exists('acme_dir_size')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_dir_size($directory)
    {
        $size = 0;

        $files = array();

        $count = 0;

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) {

            $file_size = $file->getSize();

            $size += $file_size;

            $count++;

            $files[] = array($file, $file_size);

        }

        return array("size_formatted" => acme_format_size($size), "size" => $size, "count" => $count, "files" => $files);

    }

}

if (!function_exists('acme_get_files')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_get_files($dir)
    {
        // array to hold return value
        $retval = array();

        // add trailing slash if missing
        if (substr($dir, -1) != "/") {
            $dir .= "/";
        }

        if (file_exists($dir)) {

            foreach (scandir($dir) as $key => $file) {
                if (preg_match('/[\s\S]+\.(png|jpg|jpeg|tiff|gif|bmp|pdf|doc|docx|xls|xlsx|ppt|pptx)/iu', $file)) {
                    $retval[] = [
                        'file' => "{$file}"
                    ];
                }
            }

        }

        return $retval;
    }

}

if (!function_exists('acme_money')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_money($amount, $currency = "$")
    {

        //trigger exception in a "try" block
        try {
            $amnt = $currency . " " . number_format($amount, 2);
        } catch (Exception $e) {
            $amnt = $currency . " " . $amount;
        }
        return $amnt;

    }

}

if (!function_exists('acme_date')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_date($date, $new = "l jS F , Y")
    {

        $timestamp = strtotime($date);

        return date($new, $timestamp);

    }

}

if (!function_exists('acme_date_format')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_date_format($date, $new = "Y-m-d H:i:s")
    {

        $timestamp = strtotime($date);

        return date($new, $timestamp);

    }

}

if (!function_exists('acme_redirect')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_redirect($url)
    {

        header("HTTP/1.1 301 Moved Permanently");
        header("Location: " . $url);
        exit();

    }

}

if (!function_exists('acme_domain')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_domain($url)
    {
        $pieces = parse_url($url);
        $domain = isset($pieces['host']) ? $pieces['host'] : '';
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
            return $regs['domain'];
        }
        return FALSE;
    }
}

if (!function_exists('acme_array_find_key_value')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_array_find_key_value($array, $key, $val)
    {
        foreach ($array as $item) {
            if (is_array($item) && find_key_value($item, $key, $val)) return true;

            if (isset($item[$key]) && $item[$key] == $val) return true;
        }

        return false;
    }
}

if (!function_exists('acme_array_find_value')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_array_find_value($array, $val)
    {
        foreach ($array as $k => $v) {
            if (strtolower($v) == strtolower($val)) {
                return true;
            }
        }

        return false;
    }
}
if (!function_exists('acme_base_url')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_base_url($path = "")
    {

        if (getenv("app.baseURL")) {

            return getenv("app.baseURL") . $path;

        } else if (defined("ACME_BASE_URL")) {

            return constant("ACME_BASE_URL") . $path;

        } else {

            return base_url($path);

        }

    }
}

if (!function_exists('acme_get_env')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_get_env($key = "", $type = false)
    {

        $value = getenv($key);

        if ($type) {
            $value = acme_validate_type($value, $type);
        }

        return $value;

    }
}

if (!function_exists('acme_streamline_env')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_streamline_env($key = "", $multidimension = false, $type = false)
    {

        $regex = "/^" . $key . "/";

        $env = getenv();

        $envArray = array_merge($_ENV, $env);

        $resultArray = array();

        foreach ($envArray as $k => $v) {

            preg_match($regex, $k, $matches, PREG_OFFSET_CAPTURE);

            if ($type) {
                $v = acme_validate_type($v, $type);
            }

            if (!empty($matches)) {

                $k = str_replace($key . ".", "", $k);

                if ($multidimension) {

                    $tmpArray = explode(".", $k);

                    $tmpArray[] = $v;

                    $resultArray = array_merge_recursive(
                        $resultArray,
                        acme_array_linear_2_multidimensional($tmpArray)
                    );

                } else {

                    $resultArray[$k] = $v;

                }

            }

        }

        return $resultArray;

    }
}

if (!function_exists('acme_validate_type')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_validate_type($value = "", $type = "string")
    {

        switch ($type) {

            case "bool" :
                {

                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

                    break;

                }

            case "array" :
                {

                    $value = explode(",", $value);

                    break;

                }

            case "json" :
                {

                    $value = json_decode($value, TRUE);

                    break;

                }

            case "int" :
                {

                    $value = filter_var($value, FILTER_VALIDATE_INT);

                    break;

                }

            default :
                {

                    break;

                }

        }

        return $value;

    }
}

if (!function_exists('acme_lang')) {
    /**
     * return the day of the week
     *
     * @param string $line : describe var1
     * @param integer $args : describe var2
     * @param string $locale : describe var3
     * @param boolean $path : describe var3
     * @return string 0 : return value
     */
    function acme_lang($line, $args, $locale, $path=false)
    {

        if(!$path){
            $path=ROOTPATH."ACME/Core/System/Modules/Auth/Users/";
        }
        
        $path = str_replace("\\", DIRECTORY_SEPARATOR, $path);

        return Services::language($locale)->getLine($line, $args, $path);

    }

}

if (!function_exists('acme_to_datatype')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_to_datatype($value, $datatype)
    {

        return acme_validate_type($value, $datatype);

    }
}

if (!function_exists('acme_active_li')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_active_li($navItem, $requestObj)
    {

        $segments = ""; //$requestObj->segments();

        $url = $navItem["url"];

        echo " $url ";

        if ($segments == $url) {

            echo " active ";

        }

    }
}

if (!function_exists('acme_exception')) {
    /**
     * return the day of the week
     *
     * @param string $var1 : describe var1
     * @param integer $var2 : describe var2
     * @param boolean $var3 : describe var3
     * @return array 0 : return value
     */
    function acme_exception($type = "", $message = "")
    {

        switch ($type) {

            case "database" :
                {

                    throw new \CodeIgniter\DatabaseException($message);
                    break;

                }

            case "config" :
                {

                    throw new \CodeIgniter\ConfigException($message);
                    break;

                }

            case "input" :
                {

                    throw new \CodeIgniter\UserInputException($message);
                    break;

                }

            case "file" :
                {

                    throw new \CodeIgniter\UnknownFileException($message);
                    break;

                }

            case "class" :
                {

                    throw new \CodeIgniter\UnknownClassException($message);
                    break;

                }

            case "method" :
                {

                    throw new \CodeIgniter\UnknownMethodException($message);
                    break;

                }

            default :
                {

                    throw new \Exception($message);
                    break;

                }

        }


    }
}

if (!function_exists('acme_round_robin')) {

    function acme_round_robin(array $teams)
    {

        if (count($teams) % 2 != 0) {
            array_push($teams, "bye");
        }
        $away = array_splice($teams, (count($teams) / 2));
        $home = $teams;
        for ($i = 0; $i < count($home) + count($away) - 1; $i++) {
            for ($j = 0; $j < count($home); $j++) {
                $round[$i][$j]["Home"] = $home[$j];
                $round[$i][$j]["Away"] = $away[$j];
            }
            if (count($home) + count($away) - 1 > 2) {
                $s = array_splice($home, 1, 1);
                $slice = array_shift($s);
                array_unshift($away, $slice);
                array_push($home, array_pop($away));
            }
        }
        return $round;
    }

}

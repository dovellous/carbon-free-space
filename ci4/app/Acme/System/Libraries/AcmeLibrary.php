<?php 

namespace Acme\System\Libraries;

if (!defined('ACME_NAMESPACE')) acme_exception(null, 'The application namespace is undefined. Please check your installation');

use CodeIgniter\Events\Events;
use Acme\System\Config\AcmeConfig;

class AcmeLibrary
{

    /**
     * @var array $acmeConfig
     */
    public $acmeConfig;

    /**
     * @var resource $acmeRequest
     */
    public $acmeRequest;

    /**
     * The name of this module
     *
     * @var string $moduleName
     */
    public $moduleName;

    /**
     * AcmeLibrary constructor.
     */
    public function __construct()
    {

        $this->setupConfig();

    }

    /**
     * 
     */
    public function setupConfig()
    {

        $acmeConfig = new AcmeConfig();

        $this->acmeConfig = $acmeConfig->systemConfig;

    }

    /**
     * @param $dataObjectResult
     * @return array
     */
    public function normaliseConfigMetaData( $dataObjectResult )
    {
        $normalisedData = array();

        $rows = $dataObjectResult["resultObject"];

        foreach($rows as $row){

            $normalisedData[$row["env_meta_key"]] = $row["env_meta_data"];

        }

        return $normalisedData;

    }

    /**
     * Get the array object for table insert data
     *
     * @param string $operation
     * @return array
     */
    public function getCRUDArray( $operation="INSERT" )
    {

        // Initialise the insert data array
        $insertData = array();

        // Get the post data
        $postData = $this->acmeRequest->getPost();

        // Create a Dataset
        foreach( $postData as $k=>$v ){

            $insertData[acme_camel_to_underscore($k)] = $v;

        }

        // For record update, we can skip null values
        if(strtoupper($operation) == 'UPDATE') {

            // Iterate the array object and check if we have a null value
            foreach ($insertData as $k => $v) {

                // If at this point in time, the value is null, then unset the key
                if ($v === null) {

                    // remove the key together with its value
                    unset($insertData[$k]);

                }

            }

        }

        // Remove the csrf token
        unset($insertData[csrf_token()]);

        // Return the readily variable insertData
        return $insertData;

    }

    /**
     * @param $moduleName
     * @param $result
     * @param $dataObject
     * @param $config
     * @param $theme
     * @param $locale
     * @param $layout
     * @param $ns_header
     * @param $ns_content
     * @param $ns_footer
     * @param $cache
     */
    public function renderHTMLView( 
        $moduleName, 
        $result, 
        $dataObject, 
        $config, 
        $theme, 
        $locale, 
        $layout, 
        $ns_header, 
        $ns_content, 
        $ns_footer, 
        $cache
    )
    {
        
        // Prepare view data
        $viewData = array(
            "queryResult"=>$result,
            "layoutName"=>$layout,
            "theme"=>$theme
        );

        // Language
        $viewData["locale"] = $locale;

        // Language
        $viewData["language"] = $dataObject["language"];

        // User Parameters
        $viewData["data_object"] = $dataObject;

        // Module name
        $viewData["module"] = $moduleName;

        // User Parameters
        $params = $dataObject["params"];

        //die(json_encode($viewData)); die(); exit;

        //Normalise the views data
        $data = $this->normaliseViewsData(
            $moduleName,
            $config,
            $viewData,
            $params
        );

        // Todo: Turn on output buffering
        //ob_start();

        // Load the module header
        echo view(
            $ns_header,
            $data,
            [
                'cache' => $cache,
                'cache_name' => 'cached_view_default_header'
            ]
        );

        // Load the main content
        echo view(
            $ns_content,
            $data,
            [
                'cache' => $cache,
                'cache_name' => 'cached_view_main'
            ]
        );

        // Load the module footer
        echo view(
            $ns_footer,
            $data,
            [
                'cache' => $cache,
                'cache_name' => 'cached_view_default_footer'
            ]
        );

        // Flush (send) the output buffer
        ob_flush();

    }

    /**
     * Normalise the data for the views
     *
     * @param array $metadata
     * @return array
     */
    public function normaliseEventSubscriptions( $eventSubscriptions )
    {

        $subscriptions = array();

        if(isset($eventSubscriptions["resultObject"])){

            $rows = $eventSubscriptions["resultObject"];

            if(count($rows) > 0){

                foreach($rows as $row){

                    $event = array(
                        "eventName"=>$row["event_name"],
                        "methodName"=>$row["event_method"],
                        "actionName"=>$row["event_action"]
                    );

                    $subscriptions[] = $event;

                }

            }

        }

        return $subscriptions;

    }

    /**
     * Normalise the data for the views
     *
     * @param array $metadata
     * @return array
     */
    public function normaliseViewsData( $module, $config, $object, $params )
    {

        // Initialise the array
        $data = array();

        // Pass the module name to the view
        $data["module"] = $module;

        // Pass the config data to the views
        $data["config"] = $config;

        // Pass the frontend theme to be used by the view
        $data["theme"] = $object["theme"];

        // Include the page title here
        if(array_key_exists("title", $object))
            $data["title"] = $object["title"];

        // Include the file name for the layout
        $data["layout"] = $object["layoutName"];

        // Include the file name for the layout
        $data["language"] = $object["language"];

        // Include the table column names for the data
        $data["columns"] = $object["queryResult"]["fieldNames"];

        // Get the data
        $data["rows"] = $object["queryResult"]["resultObject"];

        // Get the data
        $data["params"] = $params;

        // We are going to pass a view from another
        // Hence we need to embed the data object in itself
        $data["dataObject"] = $data;

        // Return the normilsed data
        return $data;

    }

    public function translateText( $textObject ){

        foreach ($textObject as $item){

            if(!is_array($item)){

                return $item;

            }

        };

    }

    public function onEventHandler( $eventData ){

        if(!file_exists(WRITEPATH . 'events')){

            mkdir(WRITEPATH . 'events');

        }

        file_put_contents(WRITEPATH . 'events' . DIRECTORY_SEPARATOR. 'E' .time().' .json', json_encode($eventData, JSON_PRETTY_PRINT));


    }

    public function dispatchEvent( $params, $input, $output ){

        $simulateEvents = false;

        if(isset($this->acmeConfig["events.simulate"])){

            $simulateEvents = $this->acmeConfig["events.simulate"];

        }

        // Simulate Events
        Events::simulate( $simulateEvents );

        // Set the event name
        $eventName = $params["namespace"] . "." . strtolower($params["method"]);

        // Trigger the read event
        Events::trigger(
            $eventName,
            array(
                "params"=>$params,
                "input"=>$input,
                "output"=>$output
            )
        );

    }

    public function validateRequest( $requestType, $apiKey=null ){
        
        if(!$apiKey) {

            $method = $this->acmeRequest->getMethod();

            if ($method == strtolower($requestType)) {

                return array(
                    "vars" => $this->acmeRequest->getVar(),
                    "isValid" => true,
                    "method" => $method
                );

            } else {

                return array(
                    "isValid" => false,
                    "method" => $method
                );

            }

        }else{
            
            //TODO validate the api key
            
        }

    }

    public static function writeAccessJournal( ...$params /*$uri_string, $host, $path, $port, $segments, $fragments, $query, $vars*/){

        //File write access log

        //Writ to user activity log

    }

    public function writeDBJournal( ...$params /*$operation, $module, $method, $params*/){

        //File write access log

        //Writ to user activity log

        

    }

    /**
     * @param $userSessionData
     * @param $namespace
     * @param $class
     * @param $method
     * @param $module
     * @return array
     */
    public function isOperationAllowed($userSessionData, $request, $systemConfig, $module, $method, $namespace, $class)
    {

        $uriSegments = $request->uri->getSegments();

        if( isset($uriSegments[0]) ) {

            $segmentInArray = in_array(
                $uriSegments[0],
                $systemConfig["public"]["views"]
            );

            if ($segmentInArray) {

                //TODO drill down from Module > Method

                return true;

            }else {

                //var_dump($uriSegments); exit;

                if(!is_array($userSessionData)){

                    return true;

                }

                if (!array_key_exists("authUserData", $userSessionData)) {

                    return false;

                } else {

                    $userWorkGroupModules = $this->getUserSessionWorkGroupArray($userSessionData, "views");

                    if (empty($userWorkGroupModules)) {

                        return false;

                    } else {

                        //var_dump($userWorkGroupModules); exit;

                        //TODO Now dpo the actual verification of the items operation

                        return true;

                    }

                }

            }

        }else {

            return false;

        }

    }

    public function getUserSessionWorkGroupArray($userSessionData, $item){

        $workGroupArray = array();

        $userSessionItem = $this->getUserSessionItem($userSessionData, $item);

        foreach($userSessionItem as $workGroup){

            $workGroupArray[] = $workGroup;

        }

        return $workGroupArray;

    }

    public function getUserSessionItem($userSessionData, $item){

        $userSessionItemArray = array();

        if(array_key_exists("authUserData", $userSessionData)){

            if(array_key_exists("work_groups", $userSessionData["authUserData"])) {

                $workGroups = $userSessionData["authUserData"]["work_groups"];

                foreach ($workGroups as $workGroup) {

                    if (array_key_exists($item, $workGroup)) {

                        $userSessionItemArray[$workGroup["work_group_slug"]] = $workGroup[$item];

                    }

                }

            }

        }

        return $userSessionItemArray;

    }

}
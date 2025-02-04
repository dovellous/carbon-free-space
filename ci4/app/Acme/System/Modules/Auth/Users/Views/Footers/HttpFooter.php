<?php

/**
 * Load the theme default footer to after loading Users
 *
 * @author      ThemeXenia
 * @copyright   Acme (c) 2019
 * @version     1.0.0
 * @link        https://acme.app
 * @since       1.0.0
 * @package     Auth
 * @subpackage  Users
 */

// NOTE: Do not edit this file

// Path to the template file
$viewPath = 'Acme/Layouts/Themes/' . $theme . '/Footers/footer_0';

// Path to the view hooks file
$viewHooksPath = 'Acme/System/Modules/Auth/Users/Views/Hooks/';

// Checks whether the page actually exists.
// PHP’s native is_file() function is used to check whether the file is where it’s expected to be.
// The PageNotFoundException is a CodeIgniter exception that causes the default error page to show.
if ( ! is_file(ROOTPATH . $viewPath . '.php'))
{

    ob_get_clean();

    //Clean (erase) the output buffer and turn off output buffering
    ob_end_clean();

    // Whoops, we don't have a page for that!
    throw new \CodeIgniter\Exceptions\PageNotFoundException($viewPath);

}else{

    // Adjust the path so that the view parser can locate our template
    $layoutFile = '../../' . $viewPath;

    // Adjust the path so that the view parser can locate our view hooks
    $hooksPath = '../../' . $viewHooksPath;

    // Get the content to display immediately after the <footer> open tag
    $contentAfterFooterOpen = view($hooksPath . "UsersFooterAfterOpen");
    // Get the user custom scripts for this specific module
    $contentCustomScripts = view($hooksPath . "UsersCustomScripts");
    // Get the content to display immediately before the <footer> close tag
    $contentBeforeFooterClose = view($hooksPath . "UsersFooterBeforeClose");

    // Get the content to display immediately before the <body> close tag
    $contentBeforeBodyClose = view( $hooksPath . "UsersBodyBeforeClose");

    // Consolidate the views
    $viewData = array(
        "contentAfterFooterOpen" => $contentAfterFooterOpen,
        "contentCustomScripts" => $contentCustomScripts,
        "contentBeforeFooterClose" => $contentBeforeFooterClose,
        "contentBeforeBodyClose" => $contentBeforeBodyClose
    );

    // Render the moduletemplate :: modulecomponent layout
    echo view($layoutFile, $viewData);

}

?>


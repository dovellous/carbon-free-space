<?php 

namespace Acme\Core\System\Libraries;

if (!defined('ACME_NAMESPACE')) acme_exception(null, 'The application namespace is undefined. Please check your installation');

class ImageLibrary
{
    
    public function test_library()
    {

        return "CoreLibrary ::: LIBRARY";

    }

}
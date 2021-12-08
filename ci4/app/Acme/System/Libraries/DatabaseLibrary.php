<?php 

namespace Acme\Core\System\Libraries;

if (!defined('ACME_NAMESPACE')) acme_exception(null, 'The application namespace is undefined. Please check your installation');

class DatabaseLibrary
{

    public function test_library()
    {

        return "CoreLibrary ::: LIBRARY";

    }

    public function getPrimaryKey( $databaseTable )
    {

        return "CoreLibrary ::: LIBRARY";

    }

}
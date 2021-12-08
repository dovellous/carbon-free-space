<?php 

namespace Acme\Core\System\Libraries;

if (!defined('ACME_NAMESPACE')) acme_exception(null, 'The application namespace is undefined. Please check your installation');
/**
 * This is a PHP library that handles calling reCAPTCHA.
 *    - Documentation and latest version
 *          https://developers.google.com/recaptcha/docs/php
 *    - Get a reCAPTCHA API Key
 *          https://www.google.com/recaptcha/admin/create
 *    - Discussion group
 *          http://groups.google.com/group/recaptcha
 *
 * @copyright Copyright (c) 2014, Google Inc.
 * @link      http://www.google.com/recaptcha
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
/**
 * A ReCaptchaResponse is returned from checkAnswer().
 */

use \Acme\Core\System\Libraries\ReCaptcha;

class ReCaptchaLibrary {
    
    public $reCaptcha;

    public function __construct($secret, $enabled='no')
    {
        
        $this->reCaptcha = new ReCaptcha($secret, $enabled);

    }

    /**
     * Add a user, password will be hashed
     *
     * @param array user
     * @return int id
     */
    public function verify() {

        // empty response
        $response = null;

        // if submitted check response
        if (isset($_POST["g-recaptcha-response"])) {

            $response = $this->reCaptcha->verifyResponse(

                $_SERVER["REMOTE_ADDR"],

                $_POST["g-recaptcha-response"]

            );

        }

        //
        //$response->success = true;

        return $response;

    }

}
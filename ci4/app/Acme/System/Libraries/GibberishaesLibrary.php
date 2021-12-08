<?php 

namespace Acme\Core\System\Libraries;

if (!defined('ACME_NAMESPACE')) acme_exception(null, 'The application namespace is undefined. Please check your installation');

class GibberishaesLibrary
{

    // database table name
    protected $_nKeySize = 256;            // The key size in bits

    protected static $valid_key_sizes = array(128, 192, 256);   // Sizes in bits

    /**
     * Get the settings from config
     */
    public function __construct() {

    }


    /**
     * Encrypt AES (256, 192, 128)
     * @param $string string
     * @param $key string algorithm encryption
     * @return string base64 encoded encrypted cipher
     */
    public function encrypt($string, $key=null)
    {
        $salt = openssl_random_pseudo_bytes(8);

        $salted = '';
        $dx = '';

        if(!$key){

            $key = $this->config->item("encryption_key");
        }

        // Lengths in bytes:
        $key_length = (int) ($this->_nKeySize / 8);
        $block_length = 16; // 128 bits, iv has the same length.
        // $salted_length = $key_length (32, 24, 16) + $block_length (16) = (48, 40, 32)
        $salted_length = $key_length + $block_length;

        while (strlen($salted) < $salted_length)
        {
            $dx = md5($dx.$key.$salt, true);
            $salted .= $dx;
        }

        $key = substr($salted, 0, $key_length);
        $iv = substr($salted, $key_length, $block_length);

        return base64_encode('Salted__' . $salt . openssl_encrypt($string, "aes-".$this->_nKeySize."-cbc", $key, true, $iv));
    }

    /**
     * Decrypt AES (256, 192, 128)
     * @param $string base64 encoded cipher
     * @param $key string algorithm encryption
     * @return dencrypted string
     */
    public function decrypt($string, $key=null)
    {

        if(!$key){

            $key = $this->config->item("encryption_key");
        }

        // Lengths in bytes:
        $key_length = (int) ($this->_nKeySize / 8);
        $block_length = 16;

        $data = base64_decode($string);
        $salt = substr($data, 8, 8);
        $encrypted = substr($data, 16);

        /**
         * From https://github.com/mdp/gibberish-aes
         *
         * Number of rounds depends on the size of the AES in use
         * 3 rounds for 256
         *     2 rounds for the key, 1 for the IV
         * 2 rounds for 128
         *     1 round for the key, 1 round for the IV
         * 3 rounds for 192 since it's not evenly divided by 128 bits
         */
        $rounds = 3;
        if (128 === $this->_nKeySize)
        {
            $rounds = 2;
        }

        $data00 = $key.$salt;
        $md5_hash = array();
        $md5_hash[0] = md5($data00, true);
        $result = $md5_hash[0];

        for ($i = 1; $i < $rounds; $i++)
        {
            $md5_hash[$i] = md5($md5_hash[$i - 1].$data00, true);
            $result .= $md5_hash[$i];
        }

        $key = substr($result, 0, $key_length);
        $iv = substr($result, $key_length, $block_length);

        return openssl_decrypt($encrypted, "aes-".$this->_nKeySize."-cbc", $key, true, $iv);
    }

    /**
     * Sets the key-size for encryption/decryption in number of bits
     * @param  $nNewSize int The new key size. The valid integer values are: 128, 192, 256 (default) */
    public function setMode($nNewSize)
    {
        if (is_null($nNewSize) || empty($nNewSize) || !is_int($nNewSize) || !in_array($nNewSize, self::$valid_key_sizes))
            return;

        $this->_nKeySize = $nNewSize;
    }

}
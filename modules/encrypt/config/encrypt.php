<?php
/**
 * The following options must be set:
 *
 * string   key     secret passphrase
 */

use Kohana_Encrypt_Engine_Mcrypt as Mcrypt;
use Kohana_Encrypt_Engine_Openssl as OpenSSL;
use Kohana_Encrypt_Engine_Sodium as Sodium;
use Kohana_Encrypt_Engine as Engine;

return [
    'default' => [
        Engine::CONFIG_TYPE => OpenSSL::TYPE,
        Engine::CONFIG_KEY => NULL,
    ],
//    'sodium' => [
//        Engine::CONFIG_TYPE => Sodium::TYPE,
//        Engine::CONFIG_KEY => NULL,
//    ],
//    /**
//     * Mcrypt is deprecated and should not be used,
//     * however it requires additional options:
//     *
//     * integer  mode    encryption mode, one of MCRYPT_MODE_*
//     * integer  cipher  encryption cipher, one of the Mcrypt cipher constants
//     */
//    'mcrypt' => [
//        Encrypt::CONFIG_TYPE => Mcrypt::TYPE,
//        Encrypt::CONFIG_KEY => NULL,
//        // Additional mcrypt configuration
//        Mcrypt::CONFIG_CIPHER => MCRYPT_RIJNDAEL_128,
//        Mcrypt::CONFIG_MODE => MCRYPT_MODE_CBC,
//    ],
];

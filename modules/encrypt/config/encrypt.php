<?php
/**
 * The following options must be set:
 *
 * string   key     secret passphrase
 */

return [
    'default' => [
        Kohana_Encrypt_Engine::CONFIG_TYPE => Kohana_Encrypt_Engine_Openssl::TYPE,
        Kohana_Encrypt_Engine::CONFIG_KEY => NULL,
        //Additional OpenSSL configuration
        Kohana_Encrypt_Engine::CONFIG_CIPHER => Kohana_Encrypt_Engine_Openssl::AES_256_CBC,
    ],
//    'sodium' => [
//        Kohana_Encrypt_Engine::CONFIG_TYPE => Kohana_Encrypt_Engine_Sodium::TYPE,
//        Kohana_Encrypt_Engine::CONFIG_KEY => NULL,
//    ],
//    /**
//     * Mcrypt is deprecated and should not be used,
//     * however it requires additional options:
//     *
//     * integer  mode    encryption mode, one of MCRYPT_MODE_*
//     * integer  cipher  encryption cipher, one of the Mcrypt cipher constants
//     */
//    'mcrypt' => [
//        Kohana_Encrypt_Engine::CONFIG_TYPE => Kohana_Encrypt_Engine_Mcrypt::TYPE,
//        Kohana_Encrypt_Engine::CONFIG_KEY => NULL,
//        // Additional mcrypt configuration
//        Kohana_Encrypt_Engine::CONFIG_CIPHER => MCRYPT_RIJNDAEL_128,
//        Kohana_Encrypt_Engine_Mcrypt::CONFIG_MODE => MCRYPT_MODE_CBC,
//    ],
];

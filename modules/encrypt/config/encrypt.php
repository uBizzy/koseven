<?php
/**
 * The following options must be set:
 *
 * string   key     secret passphrase
 */
return [
    'default' => [
        'type' => Kohana_Encrypt_Engine_Openssl::TYPE,
        'key' => NULL
    ],
//    'sodium' => [
//        'type' => Kohana_Encrypt_Engine_Sodium::TYPE,
//        'key' => NULL
//    ],
//    /**
//     * Mcrypt is deprecated and should not be used,
//     * however it requires additional options:
//     *
//     * integer  mode    encryption mode, one of MCRYPT_MODE_*
//     * integer  cipher  encryption cipher, one of the Mcrypt cipher constants
//     */
//    'mcrypt' => [
//        'type' => Kohana_Encrypt_Engine_Mcrypt::TYPE,
//        'cipher' => MCRYPT_RIJNDAEL_128,
//        'mode' => MCRYPT_MODE_CBC,
//        'key' => NULL
//    ]
];

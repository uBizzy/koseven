# Encrypt 

The encrypt module is included with Koseven, but needs to be enabled before you can use it. To enable, open your `application/bootstrap.php` file and modify the call to [KO7::modules] by including the encrypt module like so:

    KO7::modules(array(
        ...
        'encrypt' => MODPATH.'encrypt',
        ...
    ));

Next, you will need to set the encryption key in encrypt.php inside config directory.

Encryption and decryption is supported thru the following engines:

- mcrypt (deprecated)
- openssl
- sodium

## Quick usage

encrypt.php config file:

    return [
        'default' => [
            Engine::CONFIG_TYPE => OpenSSL::TYPE,
            Engine::CONFIG_KEY => NULL,
            //Additional OpenSSL configuration
            Engine::CONFIG_CIPHER => OpenSSL::AES_256_CBC,
        ]
    ]

your code:

    $encoded = Encrypt::instance()->encode('data');
    echo Encrypt::instance()->decode($encoded); // data

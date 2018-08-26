# Encrypt 

The encrypt module is included with Koseven, but needs to be enabled before you can use it. To enable, open your `application/bootstrap.php` file and modify the call to [Kohana::modules] by including the encrypt module like so:

    Kohana::modules(array(
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

    $encoded = Encrypt::instance()->encode('data');
    $decoded = Encrypt::instance()->encode($encoded); // data

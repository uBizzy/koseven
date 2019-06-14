# Encryption

Koseven supports built-in encryption and decryption via the [Encrypt] class.

To use the class, first start by ensuring you either have the Mcrypt, OpenSSL or Sodium extension loaded to your PHP config. 

Next, copy the default config/encryption.php from system/config folder to your application/config folder.

The default Encryption config file that ships with Koseven looks like this:

    <?php

    return array(
        'default' => [
        		'type'	 => 'openssl',
        		'key'	 => NULL,
        		'cipher' => Encrypt_Engine_Openssl::AES_256_CBC
        	],
        	.....
    );


A couple of notes about the config.
First, you may have multiple first-level keys other than 'default' if you need to.
In this respect, the config file is similar to having multiple databases defined in your config/database.php file.
Second, notice there is no key provided. You need to add that.
It is strongly recommended that you choose a high-strength random key using the [pwgen linux program](http://linux.die.net/man/1/pwgen)...

    shell> pwgen 63 1
    trwQwVXX96TIJoKxyBHB9AJkwAOHixuV1ENZmIWyanI0j1zNgSVvqywy044Agaj

...or by going to [GRC.com/passwords.htm](https://www.grc.com/passwords.htm).

## Basic Usage

### Create an instance

To use the Encryption class, obtain an instance of the Encrypt class by calling it's *instance* method,
optionally passing the desired configuration group. If you do not pass a config group to the instance method,
the default group will be used.

    $encrypt = Encrypt::instance('tripledes');

### Encoding Data

Next, encode some data using the *encode* method:

    $encrypt = Encrypt::instance('tripledes');
    $encrypted_data = $encrypt->encode('Data to Encode');
    // $encrypted_data now contains pCD5Z6oVdb9hbLxxV+FgGrhwVzZuhQoH

[!!] Raw encrypted strings usually won't print in a browser, and may not store properly in a VARCHAR or TEXT field. For this reason, Koseven's Encrypt class automatically calls base64_encode on encode, and base64_decode on decode, to prevent this problem.

[!!] One word of caution. The length of the encoded data expands quite a bit, so be sure your database column is long enough to store the encrypted data. If even one character is truncated, the data will not be recoverable.

### Decoding Data

To decode some data, load it from the place you stored it (most likely your database) then pass it to the *decode* method:

    $encrypt = Encrypt::instance('tripledes');
    $decoded_string = $encrypt->decode($encrypted_data);
    echo $decoded_string;
    // prints 'Data to Encode'

You can't know in advance what the encoded string will be, and it's not reproducible, either.
That is, you can encode the same value over and over, but you'll always obtain a different encoded version,
even without changing your key, cipher and mode.  This is because Koseven adds some random entropy before encoding it with your value.
This ensures an attacker cannot easily discover your key and cipher, even given a collection of encoded values.

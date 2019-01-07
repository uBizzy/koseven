<?php

// Get the latest logo contents
$data = base64_encode(file_get_contents('http://koseven.ga/media/img/k7.png'));

// Create the logo file
file_put_contents('logo.php', "<?php
/**
 * K7 Logo, base64_encoded PNG
 * 
 * @copyright  (c) K7 Team
 * @license    https://koseven.ga/LICENSE.md
 */
return array('mime' => 'image/png', 'data' => '{$data}'); ?>");

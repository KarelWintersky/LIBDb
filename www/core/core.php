<?php
// функции ядра

/**
 * @param $filename
 * @return string
 */
function floadfile($filename)
{
    $fh = fopen($filename,"rb");
    $real_filesize = filesize($filename);
    $blobdata = fread($fh, $real_filesize);
    fclose($fh);
    return $blobdata;
}


function pluralForm($number, $forms, $glue = '|')
{
    if (is_string($forms)) {
        $forms = explode($glue, $forms);
    } elseif (!is_array($forms)) {
        return null;
    }

    if (count($forms) != 3) {
        return null;
    }

    return $number % 10 == 1 && $number % 100 != 11 ? $forms[0] : ($number % 10 >= 2 && $number % 10 <= 4 && ($number % 100 < 10 || $number % 100 >= 20) ? $forms[1] : $forms[2]);
}


/**
 * @param bool $debugmode
 * @return bool
 */
function isAjaxCall($debugmode=false)
{
    $debug = (isset($debugmode)) ? $debugmode : false;
    return ((!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) || ($debug);
}

/**
 * @param $url
 */
function Redirect($url)
{
    if (headers_sent() === false) {
        header('Location: '.$url );
    } else {
        die('Headers sent');
    }
    exit;
}

/**
 * Возвращает 1 если мы залогинены в системе.
 * @return int
 */
function isLogged()
{
    $key_session_userid = Config::get('auth:session/user_id');
    $key_cookie_is_logged = Config::get('auth:cookies/user_is_logged');

    // вот тут мы проверямем куки и сессию на предмет "залогинились ли мы"
    $we_are_logged = !empty($_SESSION);

    $we_are_logged = $we_are_logged && isset($_SESSION[ $key_session_userid  ]);
    $we_are_logged = $we_are_logged && $_SESSION[ $key_session_userid ] !== -1;

    $we_are_logged = $we_are_logged && isset($_COOKIE[ $key_cookie_is_logged ]);
    return (int) $we_are_logged ;
}

/**
 * @param $str
 */
function printr($str)
{
    echo '<pre>'.print_r($str,true).'</pre>';
}

/**
 * dump
 *
 * @param $data
 */
function d($data)
{
    echo '<pre>';
    var_dump($data);
}

/**
 * dump and die
 *
 * @param $data
 */
function dd($data)
{
    d($data);
    die;
}


/*
Converts value (filesize) to human-friendly view like '5.251 M', 339.645 K or 4.216 K
$value : converted value
$closeness : number of digits after dot, default 0
*/
/**
 * @param $value
 * @param int $closeness
 * @return string
 */
function ConvertToHumanBytes($value, $closeness = 0) {
    $filesizename = array(" Bytes", " K", " M", " G", " T", " P", " E", " Z", " Y");
    return $value ? round($value / pow(1024, ($i = (int)floor(log($value, 1024)))), $closeness) . $filesizename[$i] : '0 Bytes';
}


/**
 * @param $string
 */
function println( $string )
{
    print($string . '<br/>' . "\r\n");
}

/**
 * @todo comment
 * @param $data
 * @param $allowed_values_array
 * @return $data if it is in allowed values array, NULL otherwise
 */
function getAllowedRef( $data, $allowed_values_array )
{
    $key = array_search($data, $allowed_values_array);
    return ($key !== FALSE )
        ? $allowed_values_array[ $key ]
        : FALSE;
}

/**
 * Проверяет заданную переменную на допустимость (на основе массива допустымых значений)
 * и если находит - возвращает её. В противном случае возвращает $default_value (по умолчанию NULL).
 *
 * @param $data
 * @param $allowed_values_array
 * @param $default_value
 * @return null|mixed
 */
function getAllowedValue( $data, $allowed_values_array, $default_value = NULL )
{
    if (empty($data)) {
        return NULL;
    } else {
        $key = array_search($data, $allowed_values_array);
        return ($key !== FALSE )
            ? $allowed_values_array[ $key ]
            : NULL;
    }
}

/**
 * Эквивалент isset( array[ key ] ) ? array[ key ] : default ;
 * at PHP 7 useless, z = a ?? b;*
 * @param $array
 * @param $key
 * @param $default
 * @return mixed
 */
function at($array, $key, $default)
{
    return isset($array[$key]) ? $array[$key] : $default;
}


function ossl_encrypt($data)
{
    $OPENSSL_ENCRYPTION_KEY = Config::get('OPENSSL_ENCRYPTION_KEY');

    $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext_raw = openssl_encrypt($data, $cipher, $OPENSSL_ENCRYPTION_KEY, $options = OPENSSL_RAW_DATA, $iv);
    $hmac = hash_hmac('sha256', $ciphertext_raw, $OPENSSL_ENCRYPTION_KEY, $as_binary = true);
    $ciphertext = base64_encode($iv . $hmac . $ciphertext_raw);

    return $ciphertext;
}

function ossl_decrypt($data)
{
    $OPENSSL_ENCRYPTION_KEY = Config::get('OPENSSL_ENCRYPTION_KEY');

    $c = base64_decode($data);
    $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
    $iv = substr($c, 0, $ivlen);
    $hmac = substr($c, $ivlen, $sha2len = 32);
    $ciphertext_raw = substr($c, $ivlen + $sha2len);
    $decrypted_data = openssl_decrypt($ciphertext_raw, $cipher, $OPENSSL_ENCRYPTION_KEY, $options = OPENSSL_RAW_DATA, $iv);
    $calcmac = hash_hmac('sha256', $ciphertext_raw, $OPENSSL_ENCRYPTION_KEY, $as_binary = true);

    return (hash_equals($hmac, $calcmac)) ? $decrypted_data : null;
}

function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . ' ' . $units[$pow];
}


/**
 * Enable or disable JIT-compilation for PCRE
 *
 * http://php.net/manual/ru/pcre.configuration.php#ini.pcre.jit
 *
 * Required for
 */
function pcre_jit_disable(){
    ini_set('pcre.jit', 0);
}

function pcre_jit_enable(){
    ini_set('pcre.jit', 1);
}

/**
 * Возвращает версию движка и ассетов из файла .version в корне
 * Генерация этого файла делается так:
 *
 * git log --oneline --format=%B -n 1 HEAD | head -n 1 > ./www/.version
 * git log --oneline --format="%at" -n 1 HEAD | xargs -I{} date -d @{} +%Y-%m-%d >> ./www/.version
 * git rev-parse --short HEAD >> ./www/.version 
 *
 * 
 * 
 * @param string $file
 * @return array
 */
function get_engine_version($file = __DIR__ . DIRECTORY_SEPARATOR . '../.version') {
    $versions = ['KW LIBDb Engine'];
    $versions = array_merge($versions, is_file($file) ? file($file) : [ '2.0alpha', date('Y-m-d'), '0' ]);

    return [
        'copyright'         =>  'KW LIBDb Engine',
        'meta_version'      =>  trim($versions[1]) . " (" . trim($versions[2]) . ")",
        'assets_version'    =>  $versions[3]
    ];
}
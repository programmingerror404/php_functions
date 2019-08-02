<?php

/**
 * Gets the ip address.
 *
 * @return     <string>  The ip address.
 */
function getIpAddress()
{
    // check for shared internet/ISP IP
    if (!empty($_SERVER['HTTP_CLIENT_IP']) && validateIp($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } //!empty($_SERVER['HTTP_CLIENT_IP']) && validateIp($_SERVER['HTTP_CLIENT_IP'])
    
    // check for IPs passing through proxies
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // check if multiple ips exist in var
        if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
            $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($iplist as $ip) {
                if (validateIP($ip)) {
                    return $ip;
                } //validateIP($ip)
                
            } //$iplist as $ip
        } //strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false
        else {
            if (validateIP($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
            } //validateIP($_SERVER['HTTP_X_FORWARDED_FOR'])
            
        }
    } //!empty($_SERVER['HTTP_X_FORWARDED_FOR'])
    if (!empty($_SERVER['HTTP_X_FORWARDED']) && validateIP($_SERVER['HTTP_X_FORWARDED'])) {
        return $_SERVER['HTTP_X_FORWARDED'];
    } //!empty($_SERVER['HTTP_X_FORWARDED']) && validateIP($_SERVER['HTTP_X_FORWARDED'])
    
    if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && validateIP($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
        return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
    } //!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && validateIP($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])
    
    if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && validateIP($_SERVER['HTTP_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_FORWARDED_FOR'];
    } //!empty($_SERVER['HTTP_FORWARDED_FOR']) && validateIP($_SERVER['HTTP_FORWARDED_FOR'])
    
    if (!empty($_SERVER['HTTP_FORWARDED']) && validateIP($_SERVER['HTTP_FORWARDED'])) {
        return $_SERVER['HTTP_FORWARDED'];
    } //!empty($_SERVER['HTTP_FORWARDED']) && validateIP($_SERVER['HTTP_FORWARDED'])
    
    // return unreliable ip since all else failed
    return $_SERVER['REMOTE_ADDR'];
}

/**
 * Ensures an ip address is both a valid IP and does not fall within
 * a private network range.
 *
 * @param      boolean|integer  $ip     { parameter_description }
 *
 * @return     boolean          ( description_of_the_return_value )
 */
function validateIP($ip)
{
    if (strtolower($ip) === 'unknown') {
        return false;
    } //strtolower($ip) === 'unknown'
    
    // generate ipv4 network address
    $ip = ip2long($ip);
    
    // if the ip is set and not equivalent to 255.255.255.255
    if ($ip !== false && $ip !== -1) {
        // make sure to get unsigned long representation of ip
        // due to discrepancies between 32 and 64 bit OSes and
        // signed numbers (ints default to signed in PHP)
        $ip = sprintf('%u', $ip);
        // do private network range checking
        if ($ip >= 0 && $ip <= 50331647) {
            return false;
        } //$ip >= 0 && $ip <= 50331647
        
        if ($ip >= 167772160 && $ip <= 184549375) {
            return false;
        } //$ip >= 167772160 && $ip <= 184549375
        
        if ($ip >= 2130706432 && $ip <= 2147483647) {
            return false;
        } //$ip >= 2130706432 && $ip <= 2147483647
        
        if ($ip >= 2851995648 && $ip <= 2852061183) {
            return false;
        } //$ip >= 2851995648 && $ip <= 2852061183
        
        if ($ip >= 2886729728 && $ip <= 2887778303) {
            return false;
        } //$ip >= 2886729728 && $ip <= 2887778303
        
        if ($ip >= 3221225984 && $ip <= 3221226239) {
            return false;
        } //$ip >= 3221225984 && $ip <= 3221226239
        
        if ($ip >= 3232235520 && $ip <= 3232301055) {
            return false;
        } //$ip >= 3232235520 && $ip <= 3232301055
        
        if ($ip >= 4294967040) {
            return false;
        } //$ip >= 4294967040
        
    } //$ip !== false && $ip !== -1
    return true;
}

/** getUrl
 *
 *
 * @return void
 */
function getUrl()
{
    $url = @($_SERVER["HTTPS"] != 'on') ? 'http://' . $_SERVER["SERVER_NAME"] : 'https://' . $_SERVER["SERVER_NAME"];
    $url .= $_SERVER["REQUEST_URI"];
    return $url;
}

/**
 * Gets the url without parameter.
 *
 * @return     <type>  The url without parameter.
 */
function getUrlWithoutParam()
{
    $url = getUrl();
    $url = explode('?', $url);
    return $url[0];
}

/**
 * Gets the query string and return it.
 *
 * @return     <type>  The parameter.
 */
function getParam()
{
    $url = getUrl();
    $url = explode('?', $url);
    return $url[1];
}
/**
 * dataUri - create a base64 image
 *
 * @param  mixed $file
 * @param  mixed $mime
 *
 * @return void
 */
function dataUri($file, $mime)
{
    $contents = file_get_contents($file);
    $base64   = base64_encode($contents);
    echo "data:$mime;base64,$base64";
}

/**
 * printData
 *
 * @param  mixed $data
 *
 * @return void
 */
function printData($data)
{
    echo '<br/><pre>';
    print_r($data);
    echo '</pre><br/>';
}

/**
 * setupENV
 *
 * @return void
 */
function setupENV()
{
    $_ENV['environment'] = $_SERVER['HTTP_HOST'];
    $_ENV['ip']          = getIpAddress();
    $_ENV['host']        = gethostbyaddr($_SERVER['SERVER_ADDR']);
    $_ENV['current_url'] = getUrl();
}
/**
 * cleanInput
 *
 * @param  mixed $input
 *
 * @return void
 */
function cleanInput($input)
{
    
    $search = array(
        '@<script[^>]*?>.*?
</script>@si', // Strip out javascript
        '@<[\/\!]*?[^ <>]*?>@si', // Strip out HTML tags
        '@<style[^>]*?>.*?</style>@siU', // Strip style tags properly
        '@
    <![\s\S]*?--[ \t\n\r]*>@' // Strip multi-line comments
    );
    $output = preg_replace($search, '', $input);
    return $output;
}
/**
 * stripcleantohtml
 *
 * @param  mixed $s
 *
 * @return void
 */
function stripcleantohtml($s)
{
    return htmlentities(trim(strip_tags(stripslashes(cleanInput($s)))), ENT_NOQUOTES, "UTF-8");
}

/**
 * cleantohtml
 *
 * @param  mixed $s
 *
 * @return void
 */
function cleantohtml($s)
{
    // Use: For input fields that may contain html, like a textarea
    return strip_tags(htmlentities(trim(stripslashes($s))), ENT_NOQUOTES, "UTF-8");
}

/**
 * forceDownload
 *
 * @param  mixed $file
 *
 * @return void
 */
function forceDownload($file)
{
    if ((isset($file)) && (file_exists($file))) {
        header("Content-length: " . filesize($file));
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file . '"');
        readfile("$file");
    } //(isset($file)) && (file_exists($file))
    else {
        echo "No file selected";
    }
}

/**
 * getDistanceBetweenPointsNew
 *
 * @param  mixed $latitude1
 * @param  mixed $longitude1
 * @param  mixed $latitude2
 * @param  mixed $longitude2
 *
 * @return void
 */
function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2)
{
    $theta      = $longitude1 - $longitude2;
    $miles      = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
    $miles      = acos($miles);
    $miles      = rad2deg($miles);
    $miles      = $miles * 60 * 1.1515;
    $feet       = $miles * 5280;
    $yards      = $feet / 3;
    $kilometers = $miles * 1.609344;
    $meters     = $kilometers * 1000;
    return compact('miles', 'feet', 'yards', 'kilometers', 'meters');
}

/**
 * getClientLanguage
 *
 * @param  mixed $availableLanguages
 * @param  mixed $default
 *
 * @return void
 */
function getClientLanguage($availableLanguages, $default = 'en')
{
    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        
        foreach ($langs as $value) {
            $choice = substr($value, 0, 2);
            if (in_array($choice, $availableLanguages)) {
                return $choice;
            } //in_array($choice, $availableLanguages)
        } //$langs as $value
    } //isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])
    return $default;
}

/**
 * append superscript of a numbers
 *
 * @param  mixed $cdnl
 *
 * @return void
 */
function ordinal($cdnl)
{
    $test_c = abs($cdnl) % 10;
    $ext    = ((abs($cdnl) % 100 < 21 && abs($cdnl) % 100 > 4) ? 'th' : (($test_c < 4) ? ($test_c < 3) ? ($test_c < 2) ? ($test_c < 1) ? 'th' : 'st' : 'nd' : 'rd' : 'th'));
    return $cdnl . $ext;
}
/**
 * isvalidURL
 *
 * @param  mixed $url
 *
 * @return void
 */
function isvalidURL($url)
{
    $check = 0;
    if (filter_var($url, FILTER_VALIDATE_URL) !== false) {
        $check = 1;
    } //filter_var($url, FILTER_VALIDATE_URL) !== false
    return $check;
}

/**
 * groupBy
 *
 * @param  mixed $key
 * @param  mixed $data
 *
 * @return void
 */
function groupBy($key, $data)
{
    $result = array();
    
    foreach ($data as $val) {
        if (array_key_exists($key, $val)) {
            $result[$val[$key]][] = $val;
        } //array_key_exists($key, $val)
        else {
            $result[""][] = $val;
        }
    } //$data as $val
    return $result;
}

/**
 * Bootstrap display Alert based on error ,success etc.
 *
 * @author    Unknown
 * @since    v0.0.1
 * @version    v1.0.0    Thursday, February 21st, 2019.
 * @global
 * @param    mixed    $msg
 * @param    mixed    $type
 * @return    void
 */
function displayAlert($msg, $type)
{
    switch ($type) {
        case 'error':
            $alert_color = 'bg-danger alert-danger text-white';
            break;
        case 'success':
            $alert_color = 'bg-success alert-success text-white';
            break;
        case 'info':
            $alert_color = 'bg-info alert-info text-white';
            break;
        default:
            $alert_color = 'bg-light alert-light';
            break;
    } //$type
    echo '<div class="alert ' . $alert_color . '" role="alert">' . $msg . '</div>';
}

/**
 * getUIAvatar.
 *
 * @author    Unknown
 * @since    v0.0.1
 * @version    v1.0.0    Thursday, February 21st, 2019.
 * @global
 * @param    mixed    $name
 * @return    mixed
 */
function getUIAvatar($name)
{
    $url = 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&rounded=true&color=ff0000';
    return $url;
}

/**
 * show the time display as ago time eg. 10 mins ago
 *
 * @param      <type>  $date   The date
 *
 * @return     string  ( description_of_the_return_value )
 */
function timeAgo($date)
{
    $timestamp = strtotime($date);
    
    $strTime          = array(
        "second",
        "minute",
        "hour",
        "day",
        "month",
        "year"
    );
    $length           = array(
        "60",
        "60",
        "24",
        "30",
        "12",
        "10"
    );
    $showtimeagoarray = array(
        "second",
        "minute",
        "hour"
    );
    
    $currentTime = time();
    if ($currentTime >= $timestamp) {
        $diff = time() - $timestamp;
        for ($i = 0; $diff >= $length[$i] && $i < count($length) - 1; $i++) {
            $diff = $diff / $length[$i];
        } //$i = 0; $diff >= $length[$i] && $i < count($length) - 1; $i++
        
        $diff = round($diff);
        
        if (in_array($strTime[$i], $showtimeagoarray)) {
            return $diff . " " . $strTime[$i] . "(s) ago ";
        } //in_array($strTime[$i], $showtimeagoarray)
        else {
            return date("F j, Y", strtotime($date));
        }
    } //$currentTime >= $timestamp
}

/**
 * shows unique elements from two arrays
 *
 * @param      <type>  $array1  The array 1
 * @param      <type>  $array2  The array 2
 *
 * @return     <type>  ( description_of_the_return_value )
 */
function array_unique_diff($array1, $array2)
{
    return array_merge(array_diff_key($array1, $array2), array_diff_key($array2, $array1));
}

/**
 * shows different elements from two arrays
 *
 * @param      <type>  $old    The old
 * @param      <type>  $new    The new
 *
 * @return     <type>  ( description_of_the_return_value )
 */
function arrayDiffValue($old, $new)
{
    
    $updated = array_merge(array_diff_assoc($old, $new), array_diff_assoc($new, $old));
    
    foreach ($updated as $key => $value) {
        if ($value !== $old[$key]) {
            $a[$key] = $new[$key];
        } //$value !== $old[$key]
    } //$updated as $key => $value
    return $a;
}

/**
 * redirect url function in php using js
 *
 * @param      string  $url    The url
 */
function redirectURL($url)
{
    if (headers_sent()) {
        die('<script type="text/javascript">window.location=\'' . $url . '\';</script>');
    } //headers_sent()
    else {
        header('Location: ' . $url);
        die();
    }
}

/**
 * convert a string into underscore separated string
 *
 * @param      <type>  $fname  The filename
 *
 * @return     <type>  ( description_of_the_return_value )
 */
function hyphenCase($fname)
{
    $name     = strtolower($fname);
    $mod_name = explode(' ', $name);
    $m_name   = implode('_', $mod_name);
    return $m_name;
}

/**
 * convert a string into camel case string
 *
 * @param      <type>  $camelCaseString  The camel case string
 *
 * @return     <type>  ( description_of_the_return_value )
 */
function fromCamelCase($camelCaseString)
{
    $re = '/(?<=[a-z])(?=[A-Z])/x';
    $a  = preg_split($re, $camelCaseString);
    return join($a, " ");
}

/**
 * Gets the lat long.
 *
 * @param      <type>   $address  The address
 *
 * @return     boolean  The lat long.
 */
function getLatLong($address, $key)
{
    if (!empty($address)) {
        //Formatted address
        $formattedAddr     = urlencode($address);
        //Send request and receive json data by address
        $geocodeFromAddr   = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address=' . $formattedAddr . '&key="' . $key . '"');
        $output            = json_decode($geocodeFromAddr);
        //Get latitude and longitute from json data
        $data['latitude']  = $output->results[0]->geometry->location->lat;
        $data['longitude'] = $output->results[0]->geometry->location->lng;
        //Return latitude and longitude of the given address
        if (!empty($data)) {
            return $data;
        } //!empty($data)
        else {
            return false;
        }
    } //!empty($address)
    else {
        return false;
    }
    
}

/**
 * Sends an email.
 * @param      <type>   $subject       The subject
 * @param      <type>   $to_email      To email
 * @param      string   $from_email    The from email
 * @param      string   $from_name     The from name
 * @param      string   $emailcontent  The emailcontent
 * @return     boolean  ( description_of_the_return_value )
 */
function sendEmail($subject, $to_email, $from_email, $from_name = '', $emailcontent = '')
{
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: ' . $from_name . '<' . $from_email . '>' . "\r\n";
    $headers .= 'Bcc: abc@gmail.com' . "\r\n";
    $returnpath = '-abc@gmail.com';
    if (mail($to_email, $subject, $emailcontent, $headers, $returnpath)):
        return true;
    else:
        return false;
    endif;
}

/**
 * Sends an email as attachment.
 *
 * @param      <type>   $subject       The subject
 * @param      <type>   $to_email      To email
 * @param      string   $from_email    The from email
 * @param      string   $from_name     The from name
 * @param      string   $emailcontent  The emailcontent
 * @param      <type>   $filepath      The filepath
 *
 * @return     boolean  ( description_of_the_return_value )
 */
function sendEmailAsAttachment($subject, $to_email, $from_email, $from_name = '', $emailcontent = '', $filepath)
{
    
    //attachment file path
    $file = $filepath;
    
    $headers = 'From: ' . $from_name . '<' . $from_email . '>' . "\r\n";
    $headers .= 'Bcc: abc@gmail.com' . "\r\n";
    $returnpath = '-abc@gmail.com';
    
    //boundary
    $semi_rand     = md5(time());
    $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
    
    //headers for attachment
    $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";
    
    //multipart boundary
    $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $emailcontent . "\n\n";
    
    //preparing attachment
    if (!empty($file) > 0) {
        if (is_file($file)) {
            $message .= "--{$mime_boundary}\n";
            $fp   = @fopen($file, "rb");
            $data = @fread($fp, filesize($file));
            
            @fclose($fp);
            $data = chunk_split(base64_encode($data));
            $message .= "Content-Type: application/octet-stream; name=\"" . basename($file) . "\"\n" . "Content-Description: " . basename($file) . "\n" . "Content-Disposition: attachment;\n" . " filename=\"" . basename($file) . "\"; size=" . filesize($file) . ";\n" . "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
        } //is_file($file)
    } //!empty($file) > 0
    $message .= "--{$mime_boundary}--";
    
    //send email
    if (mail($to_email, $subject, $message, $headers, $returnpath)):
        return true;
    else:
        return false;
    endif;
}

/**
 * create a slug from a string
 *
 * @param      <type>  $string  The string
 *
 * @return     string  ( description_of_the_return_value )
 */
function slugify($string)
{
    $string = utf8_encode($string);
    $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
    $string = preg_replace('/[^a-z0-9- ]/i', '', $string);
    $string = str_replace(' ', '-', $string);
    $string = trim($string, '-');
    $string = strtolower($string);
    if (empty($string)) {
        return 'n-a';
    } //empty($string)
    return $string;
}

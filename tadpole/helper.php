<?php
/*
 * Sometime too hot the eye of heaven shines
 */

if (! function_exists('str_random')) {

    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param  int  $length
     * @return string
     */
    function str_random($length = 16)
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }
}


if (! function_exists('config')) {

    /**
     * get config value by key, you can parse Two-dimension array by '.'
     *
     * @param  string key
     * @return string value
     */
    function config($key)
    {
        $config_file = require __DIR__ . '/../config.php';

        $config_key = explode('.', $key);
        $config_key_first = $config_file[array_shift($config_key)];

        $config_parser = function ($array, $stack) {
            return $array[$stack];
        };

        return array_reduce($config_key, $config_parser, $config_key_first);
    }
}



if (! function_exists('fire')) {

    /**
     * @param \Pad\Foundation\Interfaces\EventInterface $event
     * @return mixed
     */
    function fire(\Pad\Foundation\Interfaces\EventInterface $event)
    {
        return $event->fire();
    }
}

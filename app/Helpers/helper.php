<?php

if (! function_exists('request')) {
    /**
     * Get an instance of the current request or an input item from the request.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return \Illuminate\Http\Request|string|array
     */
    function request($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('request');
        }
        return app('request')->input($key, $default);
    }
}

if (! function_exists('auth')) {
    /**
     * Get an instance of Auth Facade
     */
    function auth()
    {
        return app('auth');
    }
}


if (! function_exists('bcrypt')) {
    /**
    * Hash the given value.
    *
    * @param  string  $value
    * @param  array   $options
    * @return string
    */
    function bcrypt($value, $options = [])
    {
        return app('hash')->make($value, $options);
    }
}

if ( ! function_exists('config_path'))
{
    /**
     * Get the configuration path.
     *
     * @param  string $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}

if (! function_exists('uni_output')) {
    function uni_output($content)
    {
        app('output')->writeln($content);
    }
}

if (! function_exists('uni_table')) {
    function uni_table($table)
    {
        return app('swoole.table')->{$table};
    }
}
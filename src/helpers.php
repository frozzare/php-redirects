<?php

if (!function_exists('redirects')) {
    /**
     * Parse Netlify's _redirects file.
     *
     * @param  string $file
     *
     * @return \Frozzare\Redirects\Redirects
     */
    function redirects($file = null)
    {
        return new \Frozzare\Redirects\Redirects($file);
    }
}

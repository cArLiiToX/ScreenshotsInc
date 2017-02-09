<?php
html_session_open();
ini_set("soap.wsdl_cache", "0");
ini_set("soap.wsdl_cache_enabled", "0");

$GLOBALS['params'] = array();
$GLOBALS['params']['apisessId'] = html_session_get('apisessId');

function html_session_open()
{
    @session_start();
    if (session_id() == '') {
        $message = 'Failed to start session.';
        die($message);
    }
}

function html_session_get($name)
{
    if (!$name || $name == '') {
        return $_SESSION;
    }
    return isset($_SESSION[$name]) ? $_SESSION[$name] : false;
}

function html_session_set($name, $value)
{
    if (!$name || $name == '') {
        return false;
    }
    $_SESSION[$name] = $value;
    return $value;
}

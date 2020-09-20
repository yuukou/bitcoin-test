<?php

$match = $router->match();

if( is_array($match) && is_callable( $match['target'] ) ) {
    $params = explode("::", $match['target']);
    $action = new $params[0]();
    call_user_func_array(array($action, $params[1]) , $match['params']);
} else {
    header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}

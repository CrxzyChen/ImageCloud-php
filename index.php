<?php

$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

define("LOCAL_ROOT", $_SERVER["DOCUMENT_ROOT"] . dirname($_SERVER["SCRIPT_NAME"]));
define("HTTP_ROOT", $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']));
define("SIMPLEPHP_DIR", LOCAL_ROOT . DIRECTORY_SEPARATOR . "SimplePhp");
define("DRIVERS_DIR", LOCAL_ROOT . DIRECTORY_SEPARATOR . "Drivers");
//set timezone
date_default_timezone_set('Asia/Shanghai');

//start session
if (!isset($_SESSION)) {
    session_start();
}
//load class Exception
if (!file_exists(SIMPLEPHP_DIR . DIRECTORY_SEPARATOR . "Exception.php")) {
    die("Exception.php is not exist! Exception.php:" . SIMPLEPHP_DIR . DIRECTORY_SEPARATOR . "Exception.php");
} else {
    require_once SIMPLEPHP_DIR . DIRECTORY_SEPARATOR . "Exception.php";
}

//load handle method of exception
set_error_handler("\SimplePhp\Exception::error_handler", E_ALL);
set_exception_handler("\SimplePhp\Exception::exception_handler");
spl_autoload_register("\SimplePhp\Exception::autoload_register");

//load config.json
if (!file_exists(LOCAL_ROOT . DIRECTORY_SEPARATOR . "config.json")) {
    throw new \SimplePhp\Exception("config.json is not exist! config.json: " . LOCAL_ROOT . DIRECTORY_SEPARATOR . "config.json");
} else {
    define("CONFIG_FILE", LOCAL_ROOT . DIRECTORY_SEPARATOR . "config.json");
    require_once SIMPLEPHP_DIR . DIRECTORY_SEPARATOR . "Config.php";
}

$default_controller = \SimplePhp\Config::get("default.controller", true);
$default_method = \SimplePhp\Config::get("default.method");

if (!isset($_GET["controller"])) {
    $controller = "Controllers\\$default_controller";
} else {
    $controller = "Controllers\\" . $_GET["controller"];
}

if (isset($_GET["method"])) {
    $method = $_GET["method"];
} else {
    $method = $default_method;
}

try {
    $reflection_class = new ReflectionClass($controller);
    $reflection_method = $reflection_class->getMethod($method);
    $reflection_params = $reflection_method->getParameters();
    $reflection_param_modifier = [];
    foreach ($reflection_params as $param) {
        if ($param->isDefaultValueAvailable()) {
            $default_value = $param->getDefaultValue();
        } else {
            $default_value = null;
        }
        if ($param->hasType()) {
            $type = $param->getType()->getName();
        } else {
            $type = null;
        }
        $reflection_param_modifier[] = [$default_value, $type];
    }

    $instance = $reflection_class->newInstance();
    $view = $reflection_method->invoke($instance);
} catch (ReflectionException $e) {
    die("Welcome SimplePhp Vol.0.0.0.1!");
}

ob_start();

if (is_array($view) || is_object($view)) {
    header("content-type:text/json");
    echo json_encode($view);
} else if (is_resource($view) && get_resource_type($view) == "gd") {
    switch ($_GET["image_form"]) {
        case "jpg":
            header("content-type:image/jpg");
            imagejpeg($view);
            imagedestroy($view);
            break;
        case "png":
            header("content-type:image/png");
            imagepng($view);
            imagedestroy($view);
            break;
        case "gif":
            header("content-type:image/gif");
            imagegif($view);
            imagedestroy($view);
            break;
    }
} else {
    header("content-type:text/html");
    echo $view;
}

ob_end_flush();

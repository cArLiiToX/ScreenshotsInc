<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}
$reqMethod = '';
if(isset($_GET['service']) && $_GET['service'] != ''){
	$reqMethod = $_GET['service'];
}else if(isset($_POST['service']) && $_POST['service'] != ''){
	$reqMethod = $_POST['service'];
}else if(isset($_GET['reqmethod']) && $_GET['reqmethod'] != ''){
	$reqMethod = $_GET['reqmethod'];
}else if(isset($_POST['reqmethod']) && $_POST['reqmethod'] != ''){
	$reqMethod = $_POST['reqmethod'];
}

$moduleName = '';
$restrictedModuleName = '';

/* Loop Available modules and get the module name */
/*foreach($defineModules as $module => $methods){
if(in_array($reqMethod,$methods)){
$moduleName = $module;
break;
}
}*/

foreach ($defineModules as $method => $module) {
    if ($reqMethod == $method) {
        $moduleName = $module;
        break;
    }
}
/*Loop for getting the restricted module*/
foreach ($restrictedModules as $restrictedMethod => $restrictedModule) {
    if ($reqMethod == $restrictedMethod) {
        $restrictedModuleName = $restrictedModule;
        break;
    }
}

/* If no module founds, return invalid service */
if ($moduleName == '') {
    header("HTTP/1.1 406 Not Acceptable");
    header("Content-Type:application/json");
    echo "invalid Service";
    exit;
}

/* Check debug is true or false and show module and method names */
if (DEBUG == true) {
    echo "Module Name: " . $moduleName;
    echo "<br/>";
    echo "Method Name: " . $reqMethod;
    echo "<br/>";
}

/* Initialize module class and create object of that method */
$obj = Flight::$moduleName();
if ($restrictedModuleName == '') {	
	$obj->$reqMethod();
} else {
	$auth = Flight::authentication();
	$isAuthenticate = $auth->authenticateUser();
	if ($isAuthenticate) {
		$isAthorize = $auth->authorizeUser($moduleName);
		if ($isAthorize) {			
			$obj->$reqMethod();
		} else {
			header("HTTP/1.1 406 Not Acceptable");
			header("Content-Type:application/json");			
			echo json_encode(array("status"=>"failed","Error_Code"=>"4006","Description"=>"Unauthorized Access"));
			exit;
		}
	} else {
		header("HTTP/1.1 401 Access denied");
		header("Content-Type:application/json");
		echo json_encode(array("status"=>"failed","Error_Code"=>"4001","Description"=>"Access Denied"));
		exit;
	}		
}


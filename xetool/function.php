<?php
error_reporting(0);
//error_reporting(E_ALL & ~E_NOTICE);

#######################################################
############ check The tool Installed #################
#######################################################
$app_setting = 'store_details.json';
$arr = array();
if (file_exists($app_setting)) {
    @chmod($app_setting, 0777);
    $app_str = @file_get_contents($app_setting);
    $json_app = json_decode($app_str, true);
    $arr = $json_app['folder_name'];
}
$res = 0;
foreach ($arr as $v) {
    if (file_exists('../app/' . $v . '_xeconfig.xml')) {
        $res += 1;
    }
}
if ($res > 0 && $json_app['install_status'] == '1') {
    header('Location: ../xetool/installed.php');die();
}

#######################################################
################ check PHP version ####################
#######################################################
$phpversion = phpversion();
$varr = explode('.', $phpversion);
if ($varr[0] < 5 || ($varr[0] == 5 && $varr[1] < 4)) {
    xe_log("\n" . date("Y-m-d H:i:s") . 'Error In 1st Step: PHP version is ' . $phpversion . "\n");
    $msg = "PHP does not compatible";
    header('Location: ../xetool/index.php?msg=' . $msg);die();
}

#######################################################
############## check Mysql enable #####################
#######################################################
if (!extension_loaded('mysqli')) {
    $msg = "Mysql is not Enable";
    xe_log("\n" . date("Y-m-d H:i:s") . 'Error In 1st Step: ' . $msg . "\n");
    header('Location: ../xetool/index.php?msg=' . $msg);die();
}

#######################################################
#################### check SOAP #######################
#######################################################
if (!extension_loaded('soap') || !class_exists('SOAPClient') || !class_exists('SOAPServer')) {
    $msg = "Enable SOAP, SOAPClient and SOAPServer";
    xe_log("\n" . date("Y-m-d H:i:s") . 'Error In 1st Step: ' . $msg . "\n");
    header('Location: ../xetool/index.php?msg=' . $msg);die();
}

#######################################################
#################### check PDO ########################
#######################################################
if (!extension_loaded('PDO')) {
    $msg = "Enable PDO";
    xe_log("\n" . date("Y-m-d H:i:s") . 'Error In 1st Step: ' . $msg . "\n");
    header('Location: ../xetool/index.php?msg=' . $msg);die();
}

#######################################################
################## check mbstring #####################
#######################################################
if (!extension_loaded('mbstring')) {
    $msg = "Enable mbstring";
    xe_log("\n" . date("Y-m-d H:i:s") . 'Error In 1st Step: ' . $msg . "\n");
    header('Location: ../xetool/index.php?msg=' . $msg);die();
}

#######################################################
################## check iconv ########################
#######################################################
if (!extension_loaded('iconv')) {
    $msg = "Enable iconv";
    xe_log("\n" . date("Y-m-d H:i:s") . 'Error In 1st Step: ' . $msg . "\n");
    header('Location: ../xetool/index.php?msg=' . $msg);die();
}

#######################################################
#################### check hash #######################
#######################################################
if (!extension_loaded('hash')) {
    $msg = "Enable hash";
    xe_log("\n" . date("Y-m-d H:i:s") . 'Error In 1st Step: ' . $msg . "\n");
    header('Location: ../xetool/index.php?msg=' . $msg);die();
}

########################################################
#################### check hash ########################
########################################################
if (!extension_loaded('gd')) {
    $msg = "Enable gd";
    xe_log("\n" . date("Y-m-d H:i:s") . 'Error In 1st Step: ' . $msg . "\n");
    header('Location: ../xetool/index.php?msg=' . $msg);die();
}

########################################################
########### check File Writable Permission #############
########################################################
$str = 'check writable permission'; //get_current_user();
$data = '';
try {
    $data = @file_put_contents('../test1.php', $str);
    if (!$data) {
        $msg = 'The user under which the PHP is running must have the permission to modify';
        xe_log("\n" . date("Y-m-d H:i:s") . 'Error In 1st Step: File Permission Issue.' . "\n");
        header('Location: ../xetool/index.php?msg=' . $msg);die();
    }
} catch (Exception $e) {
    xe_log("\n" . date("Y-m-d H:i:s") . 'Error In 1st Step: File Permission Issue.' . $e->getMessage() . "\n");
}

#########################################################
######### Recursively copy all the files & folders ######
#########################################################
function recurse_copy($src, $dst)
{
    $dir = opendir($src);
    @mkdir($dst);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                @recurse_copy($src . '/' . $file, $dst . '/' . $file);
            } else {
                @copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

#########################################################
##### Function use to log errors during installation ####
#########################################################
function xe_log($text, $append = true, $fileName = '')
{
    $file = '../xetool_log.log';
    if ($fileName) {
        $file = $fileName;
    }

    // Append the contents to the file to the end of the file
    // and the LOCK_EX flag to prevent anyone else writing to the file at the same time
    if ($append) {
        @file_put_contents($file, $text . PHP_EOL, FILE_APPEND | LOCK_EX);
    } else {
        @file_put_contents($file, $text);
    }

}

#########################################################
######### Recursively copy all the files & folders ######
#########################################################
function run_sql_file($filename, $conn)
{
    $commands = @file_get_contents($filename); //load file
    //delete comments
    $lines = explode("\n", $commands);
    $commands = '';
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line && !startsWith($line, '--')) {
            $commands .= $line . "\n";
        }
    }
    $commands = explode(";", $commands); //convert to array
    //run commands
    $total = $success = 0;
    foreach ($commands as $command) {
        if (trim($command)) {
            //$success += (@mysql_query($command)==false ? 0 : 1);
            $success += (@$conn->query($command) == false ? 0 : 1);
            $total += 1;
        }
    }
}
function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

<?php
if (!empty($_POST) && $_POST['chk']) {
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
    echo $res;die();
}

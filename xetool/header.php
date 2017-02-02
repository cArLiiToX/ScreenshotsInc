<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex, nofollow">
	<title>inkXE Installation Wizard</title>

	<link href="wizard/css/bootstrap.min.css" rel="stylesheet">
	<link href="wizard/css/style.css" rel="stylesheet">
	<link href="wizard/css/font-awesome.css" rel="stylesheet">
</head>
<body>	    
    <div class="container">
        <div class="row">
            <div class="col-sm-12 text-center"><h2 class="text-red text-uppercase">Installation Wizard</h2></div>
        </div>
    </div>
    <div class="container stage-wrapper">
    
    <div class="loader" id="myLoader" style="display:none;"><img class="loader-img" title="Loading" src="wizard/images/loader.gif" /></div>
    
    <div class="row stage-container">
        <div id ='step1' class="stage col-md-2 col-sm-2 col-xs-12">
			<div class="stage-header head-icon active">1</div>
			<div class="stage-content"><h3 class="stage-title active">Step-1</h3></div>
        </div>
    
        <div id="attr" class="stage col-md-2 col-sm-2 col-xs-12">
			<div class="stage-header head-icon">2</div>
			<div class="stage-content"><h3 class="stage-title">Step-2</h3></div>
        </div>
    
        <div id="cms" class="stage col-md-2 col-sm-2 col-xs-12">
			<div class="stage-header head-icon">3</div>
			<div class="stage-content"><h3 class="stage-title">Step-3</h3></div>
        </div>
    
        <div id="soap" class="stage col-md-2 col-sm-2 col-xs-12">
			<div class="stage-header head-icon">4</div>
			<div class="stage-content"><h3 class="stage-title">Step-4</h3></div>
        </div>
    
        <div id="setdb" class="stage col-md-2 col-sm-2 col-xs-12">
			<div class="stage-header head-icon">5</div>
			<div class="stage-content"><h3 class="stage-title">Step-5</h3></div>
        </div>
    
        <div id="finish" class="stage col-md-2 col-sm-2 col-xs-12">
			<div class="stage-header head-icon">6</div>
			<div class="stage-content"><h3 class="stage-title">Step-6</h3></div>
        </div>
    </div>

	<?php if(isset($_GET['msg'])){?>
	<div class="row">
         <div class="col-sm-12 m-t-lg">
             <div class="message-label">
                <label id='msg'>
                    <?php echo $_GET['msg'];?>
                </label>
            </div>
        </div>
	</div>
    <?php }?>
    <?php $action = isset($_GET['action'])?$_GET['action']:'';?>
    <input type='hidden' id='did' value="<?php echo $action;?>">
    <?php $t = isset($_GET['t'])?$_GET['t']:'n';?>
    <input type='hidden' id='tid' value="<?php echo $t;?>">
	<?php 
		error_reporting(0);
		//error_reporting(E_ALL);

		$app_setting = 'store_details.json'; $arr = array();
		if (file_exists($app_setting)) {
			@chmod($app_setting, 0777); 
			$app_str = @file_get_contents($app_setting);
			$json_app = json_decode($app_str,true);
			$arr = $json_app['folder_name'];
		}
		$res = 0;
		foreach($arr as $v){
			if (file_exists('../app/'.$v.'_xeconfig.xml')) {
				$res += 1;
			}
		}
		if($res > 0 && isset($json_app['install_status']) && $json_app['install_status'] == '1'){
			$url = 'installed.php';
			echo("<script>location.href = '".$url."';</script>");exit();	
		}
	?>
	
	<div id="myModal" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-sm m-t-270">
			<div class="modal-content">
			<div class="modal-header b-none">
				<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
				<h4 id="myModalLabel" class="modal-title"></h4>
			</div>
			<div class="modal-body" id="msgdiv"></div>
			<div class="modal-footer b-none">
				<button data-dismiss="modal" class="btn btn-default" type="button">Okay</button>
			</div>
			</div>
		</div>
	</div>	   

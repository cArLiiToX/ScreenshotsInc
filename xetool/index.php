<?php
	require_once 'header.php';
	$path = 'wizard/images/install_image/';

	if (!function_exists('apache_request_headers')) {
		function apache_request_headers() {
			foreach ($_SERVER as $key => $value) {
				if (substr($key, 0, 5) == "HTTP_") {
					$key = str_replace(" ", "-", ucwords(strtolower(str_replace("_", " ", substr($key, 5)))));
					$out[$key] = $value;
				} else {
					$out[$key] = $value;
				}
			}
			return $out;
		}
	}
?>

<?php if ($action == '') {
    $arr = array();
    $warning = array();?>
		<div class="content-box">
            <div class="row"><div class="col-sm-12"><h3>Environment Compatibility Test</h3></div></div>
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-striped table-bordered info-border">
						<tr>
							<th class="b-r-none" width="10"></th>
							<th class="b-l-none">Settings</th>
							<th>Recommended</th>
							<th>Current Settings</th>
						</tr>
						<tr>
							<th class="b-r-none"></th>
							<th class="b-l-none">PHP Version</th>
							<td>5.4+</td>
							<td>
								<?php 
									echo $phpversion = phpversion();
									$varr = explode('.', $phpversion);
									if ($varr[0] > 5) {
										$img = 'approve.png';
										array_push($arr, 1);
									} else if ($varr[0] == 5) {
										if ($varr[1] == 4 || $varr[1] > 4) {
											$img = 'approve.png';
											array_push($arr, 1);
										} else {
											$img = 'dis-approve.png';
										}
									} else {
										$img = 'dis-approve.png';
									}
									echo '&nbsp;&nbsp;<img src="' . $path . $img . '">';
								?>
							</td>
						</tr>
						<tr>
							<th class="b-r-none"></th>
							<th class="b-l-none">MySQLi</th>
							<td>Enabled</td>
							<td>
								<?php
									if (extension_loaded('mysqli')) {
										echo 'Enabled';
										$img = 'approve.png';
										array_push($arr, 2);
									} else {
										echo 'Disabled';
										$img = 'dis-approve.png';
									}
									echo '&nbsp;&nbsp;<img src="' . $path . $img . '">';
								?>
							</td>
						</tr>
						<tr>
							<th class="b-r-none"><i class="fa fa-info-circle pull-right text-warning" data-toggle="tooltip" data-placement="left" title="We reccommend Magento version 2.0.7 onwards to run our tool smoothly."></i> </th>
							<th class="b-l-none">Magento</th>
							<td>2.0.7 +</td>
							<td>
								<?php 
									require_once "getVersion.php";
									$marr = explode('.', $mversion);
									if ($marr[0] == 2) {
										if ($marr[1] == 0) {
											if ($marr[2] >= 7) {
												array_push($arr, 3);
												$img = 'approve.png';
											} else {
												$img = 'dis-approve.png';
											}
										} elseif ($marr[1] >= 1) {
											array_push($arr, 3);
											$img = 'approve.png';
										} else {
											array_push($arr, 3);
											$img = 'approve.png';
										}
									} else {
										$img = 'dis-approve.png';
									}
									echo '&nbsp;&nbsp;<img src="' . $path . $img . '">';
								?>
							</td>
						</tr>
						<tr>
							<th class="b-r-none"></th>
							<th class="b-l-none">File Permission</th>
							<td>Allowed To Modify</td>
							<td>
								<?php 
									$str = 'Check Writable Permission'; //get_current_user();
									$data = '';
									try {
										$data = file_put_contents('../test.php', $str);
										if ($data) {
											array_push($arr, 4);
											$img = 'approve.png';
											echo 'Allowed';
										} else {
											$img = 'dis-approve.png';
											echo 'Dis-Allowed';
										}
									} catch (Exception $e) {
										$img = 'dis-approve.png';
										echo 'Dis-Allowed.';
									}
									echo '&nbsp;&nbsp;<img src="' . $path . $img . '">';
								?>
							</td>
						</tr>
						<tr>
						    <th class="b-r-none"><i class="fa fa-info-circle pull-right text-warning" data-toggle="tooltip" data-placement="left" title="It is kind of a bridge (web-service) that communicates between your store and inkXE designer tool."></i></th>
						    <th class="b-l-none">SOAP</th>
							<td>Enabled</td>
							<td>
								<?php
									if (extension_loaded('soap')) {
										echo 'Enabled';
										$img = 'approve.png';
										array_push($arr, 5);
									} else {
										echo 'Disabled';
										$img = 'dis-approve.png';
									}
									echo '&nbsp;&nbsp;<img src="' . $path . $img . '">';
								?>
							</td>
						</tr>
						<tr>
							<th class="b-r-none"></th>
							<th class="b-l-none">SOAP Client</th>
							<td>Present</td>
							<td>
								<?php
									if (extension_loaded('soap') && class_exists('SOAPClient')) {
										echo 'Present';
										$img = 'approve.png';
										array_push($arr, 6);
									} else {
										echo 'Absent';
										$img = 'dis-approve.png';
									}
									echo '&nbsp;&nbsp;<img src="' . $path . $img . '">';
								?>
							</td>
						</tr>
						<tr>
							<th class="b-r-none"></th>
							<th class="b-l-none">SOAP Server</th>
							<td>Present</td>
							<td>
								<?php
									if (extension_loaded('soap') && class_exists('SOAPServer')) {
										echo 'Present';
										$img = 'approve.png';
										array_push($arr, 7);
									} else {
										echo 'Absent';
										$img = 'dis-approve.png';
									}
									echo '&nbsp;&nbsp;<img src="' . $path . $img . '">';
								?>
							</td>
						</tr>
						<tr>
							<th class="b-r-none"></th>
							<th class="b-l-none">PDO</th>
							<td>Enabled</td>
							<td>
								<?php
									if (class_exists('PDO')) {
										echo 'Enabled';
										$img = 'approve.png';
										array_push($arr, 8);
									} else {
										echo 'Disabled';
										$img = 'dis-approve.png';
									}
									echo '&nbsp;&nbsp;<img src="' . $path . $img . '">';
								?>
							</td>
						</tr>
						<tr>
							<th class="b-r-none"></th>
							<th class="b-l-none">Mbstring</th>
							<td>Enabled</td>
							<td>
								<?php
									if (extension_loaded('mbstring')) {
										echo 'Enabled';
										$img = 'approve.png';
										array_push($arr, 9);
									} else {
										$img = 'dis-approve.png';
										echo 'Disabled';
									}
									echo '&nbsp;&nbsp;<img src="' . $path . $img . '">';
								?>
							</td>
						</tr>
						<tr>
							<th class="b-r-none"></th>
							<th class="b-l-none">Iconv</th>
							<td>Enabled</td>
							<td><?php
									if (extension_loaded('iconv')) {
										echo 'Enabled';
										$img = 'approve.png';
										array_push($arr, 10);
									} else {
										$img = 'dis-approve.png';
										echo 'Disabled';
									}
									echo '&nbsp;&nbsp;<img src="' . $path . $img . '">';
								?>
							</td>
						</tr>
						<tr>
							<th class="b-r-none"></th>
							<th class="b-l-none">Hash</th>
							<td>Enabled</td>
							<td>
								<?php
									if (extension_loaded('hash')) {
										$img = 'approve.png';
										array_push($arr, 11);
										echo 'Enabled';
									} else {
										$img = 'dis-approve.png';
										echo 'Disabled';
									}
									echo '&nbsp;&nbsp;<img src="' . $path . $img . '">';
								?>
							</td>
						</tr>
						<tr>
							<th class="b-r-none"></th>
							<th class="b-l-none">GD Library</th>
							<td>Enabled</td>
							<td>
								<?php
									if (extension_loaded('gd')) {
										$img = 'approve.png';
										array_push($arr, 12);
										echo 'Enabled';
									} else {
										$img = 'dis-approve.png';
										echo 'Disabled';
									}
									echo '&nbsp;&nbsp;<img src="' . $path . $img . '">';
								?>
								<input type="hidden" value="<?php echo array_sum($arr); ?>" id="bitwiseValue">
                            </td>
						</tr>
						<tr>
							<th class="b-r-none"><i class="fa fa-info-circle pull-right text-warning" data-toggle="tooltip" data-placement="left" title="If NOT enabled, it might create problems while upgrading the tool and order download might not work as described."></i></th>
							<th class="b-l-none">ZIP</th>
							<td>Enabled</td>
							<td>
							<?php 
								if (extension_loaded('zip')) {
									echo 'Enabled';
									array_push($arr, 13);
									$img = 'approve.png';
								} else {
									array_push($warning, 13);
									echo 'Enabled';
									$img = 'Warning.png';
								}
								echo '&nbsp;&nbsp;<img src="' . $path . $img . '">';
							?>
                            </td>
						</tr>
						<tr>
							<th class="b-r-none"><i class="fa fa-info-circle pull-right text-warning" data-toggle="tooltip" data-placement="left" title="If NOT enabled, it might create problems while fetching data from your ecommerce stores. Some functions of inkXE might not work as described."></i></th>
							<th class="b-l-none">Connection</th>
							<td>Keep-Alive</td>
							<td>
								<?php 
									$apache_settings = apache_request_headers();
									$conType = $apache_settings['Connection'];
									if ($conType == 'keep-alive' || $conType == 'Keep-Alive') {
										echo 'Keep-Alive';
										array_push($arr, 14);
										$img = 'approve.png';
									} else {
										array_push($warning, 14);
										echo 'Not-Alive';
										$img = 'Warning.png';
									}
									echo '&nbsp;&nbsp;<img src="' . $path . $img . '">';
								?>
                            </td>
						</tr>
						<tr>
							<th class="b-r-none"><i class="fa fa-info-circle pull-right text-warning" data-toggle="tooltip" data-placement="left" title="This sets the maximum amount of memory in bytes that a script is allowed to allocate. To upload large files, memory_limit should be larger than post_max_size and post_max_size must be larger than upload_max_filesize."></i></th>
							<th class="b-l-none">Memory Limit</th>
							<td>256M</td>
							<td>
								<?php
									echo $mlv = ini_get('memory_limit');
									$pmsov = ini_get('post_max_size');
									$pmsv = (int) substr($pmsov, 0, -1);
									$umfov = ini_get('upload_max_filesize');
									$umfv = (int) substr($umfov, 0, -1);

									$mlv = (int) substr($mlv, 0, -1);
									if ($mlv > 255 && $mlv > $pmsv && $pmsv > $umfv) {
										array_push($arr, 15);
										$img = 'approve.png';
									} else {
										array_push($warning, 15);
										$img = 'Warning.png';
									}
									echo '&nbsp;&nbsp;<img src="' . $path . $img . '">'; 
								?>
                            </td>
						</tr>
						<tr>
							<th class="b-r-none"><i class="fa fa-info-circle pull-right text-warning" data-toggle="tooltip" data-placement="left" title="Sets max size of post data allowed."></i></th>
							<th class="b-l-none">Post Max Size</th>
							<td>60M</td>
							<td>
								<?php 
									echo $pmsov;
									if ($pmsov == 0) {
										$img = 'dis-approve.png';
									} elseif ($pmsv > 59 && $pmsv > $umfv) {
										array_push($arr, 16);
										$img = 'approve.png';
									} else {
										array_push($warning, 16);
										$img = 'Warning.png';
									}
									echo '&nbsp;&nbsp;<img src="' . $path . $img . '">'; 
								?>
							</td>
						</tr>
						<tr>
							<th class="b-r-none"><i class="fa fa-info-circle pull-right text-warning" data-toggle="tooltip" data-placement="left" title="The maximum size of an uploaded file measured in bytes."></i></th>
							<th class="b-l-none">Upload Max Filesize</th>
							<td>10M</td>
							<td>
								<?php 
									echo $umfov;
									if ($umfv == 0) {
										$img = 'dis-approve.png';
									} elseif ($umfv > 9) {
										array_push($arr, 17);
										$img = 'approve.png';
									} else {
										array_push($warning, 17);
										$img = 'Warning.png';
									}
									echo '&nbsp;&nbsp;<img src="' . $path . $img . '">'; 
								?>
							</td>
						</tr>
						<tr>
							<th class="b-r-none"><i class="fa fa-info-circle pull-right text-warning" data-toggle="tooltip" data-placement="left" title="The maximum time in seconds a script is allowed to run before it is terminated by the parser."></i></th>
							<th class="b-l-none">Max Execution Time (In Second)</th>
							<td>18000</td>
							<td>
								<?php 
									echo $metv = ini_get('max_execution_time');
									$metv = (int) $metv;
									if ($metv < 500) {
										$img = 'dis-approve.png';
									} else if (500 <= $metv && $metv < 1800) {
										array_push($warning, 18);
										$img = 'Warning.png';
									} else {
										array_push($arr, 18);
										$img = 'approve.png';
									}
									echo '&nbsp;&nbsp;<img src="' . $path . $img . '">'; 
								?>
							</td>
						</tr>
						<tr>
							<th class="b-r-none"><i class="fa fa-info-circle pull-right text-warning" data-toggle="tooltip" data-placement="left" title="The maximum time in seconds a script is allowed to run before it is terminated by the parser."></i></th>
							<th class="b-l-none">Max Input Time (In Second)</th>
							<td>60</td>
							<td>
								<?php 
									echo $mit = ini_get('max_input_time');
									$mit = (int) $mit;
									if ($mit == 0) {
										$img = 'Warning.png';
									} elseif ($mit == 60 || $mit > 60) {
										array_push($arr, 19);
										$img = 'approve.png';
									} else {
										array_push($warning, 19);
										$img = 'Warning.png';
									}
									echo '&nbsp;&nbsp;<img src="' . $path . $img . '">'; 
								?>
							</td>
						</tr>
						<tr>
							<th class="b-r-none"><i class="fa fa-info-circle pull-right text-warning" data-toggle="tooltip" data-placement="left" title="The maximum number of files allowed to be uploaded simultaneously."></i></th>
							<th class="b-l-none">Default Socket Timeout (In Second)</th>
							<td>60</td>
							<td>
								<?php 
									echo $dsto = ini_get('default_socket_timeout');
									$dsto = (int) $dsto;
									if ($dsto == 0) {
										$img = 'Warning.png';
									} elseif ($dsto == 60 || $dsto > 60) {
										array_push($arr, 20);
										$img = 'approve.png';
									} else {
										array_push($warning, 20);
										$img = 'Warning.png';
									}
									echo '&nbsp;&nbsp;<img src="' . $path . $img . '">';  
								?>
						   </td>
						</tr>
					</table>
                </div>
            </div>

			<div class="row" id='compatibility_msg_div' style='display:none;'>
				 <div class="col-sm-12">
					 <div class="alert alert-danger" role="alert">
						Your hosting server does not meet the minimum requirements to run inkXE. <?php if (in_array(1, $arr) && in_array(2, $arr) && in_array(3, $arr) && in_array(4, $arr) && in_array(5, $arr) && in_array(6, $arr) && in_array(7, $arr) && in_array(8, $arr) && in_array(9, $arr) && in_array(10, $arr) && in_array(11, $arr) && in_array(12, $arr) && (in_array(13, $warning) || in_array(14, $warning) || in_array(15, $warning) || in_array(16, $warning) || in_array(17, $warning))) {$allow = 1;?>However, you may wish to Proceed Anyway and finish the installation.<?php } else { $allow = 0;}?> Some features of inkXE might not work as described. Please <a href="http://inkxe.com/support/kb/faq.php?id=169" target="_blank">click here</a> for more information.
					 </div>
				</div>
			</div>

        </div>

		<input type='hidden' id='ap' value="<?php echo $allow; ?>" />
        <div class="row m-t-sm">
		<?php if (array_sum($arr) == 210) { ?>
			<div class="col-sm-12">
				<a onClick="loadLoader();" href="start_install.php" class="btn btn-lg btn-aqua m-w-150 pull-right">Next</a>
				<span class="text-right pull-right m-t-sm m-r-md text-danger">This process will take some time. Please don't close the window until you see the next step of the installation.</span>
			</div>
		<?php } else {?>
			<div class="col-sm-12" id="disallow">
				<a id="disAllowLink" onClick="return checkCompatible();" class="btn btn-lg btn-aqua m-w-150 pull-right">Next</a>
			</div>
			<?php if (in_array(1, $arr) && in_array(2, $arr) && in_array(3, $arr) && in_array(4, $arr) && in_array(5, $arr) && in_array(6, $arr) && in_array(7, $arr) && in_array(8, $arr) && in_array(9, $arr) && in_array(10, $arr) && in_array(11, $arr) && in_array(12, $arr) && (in_array(13, $warning) || in_array(14, $warning) || in_array(15, $warning) || in_array(16, $warning) || in_array(17, $warning) || in_array(18, $warning))) {?>
				<div class="col-sm-12" id="allow">
					<a onClick="loadLoader();" href="start_install.php" style='padding:10px 8px;' class="btn btn-lg btn-aqua m-w-200 pull-right" >Proceed Anyway</a>
					<span class="text-right pull-right m-t-sm m-r-md text-danger">This process will take some time. Please don't close the window until you see the next step of the installation.</span>
				</div>
			<?php }?>
		<?php }?>
        </div>
	<?php } elseif ($action == 'attr') {?>
        <div class="content-box">
            <div class="row">
                <div class="col-sm-12">
					<p>This will create required attribute set and attributes for inkXE.</p>
                </div>
            </div>
        </div>
        <div class="row m-t-sm">
            <div class="col-sm-12">
                <a onClick="loadLoader();" href="add_product_attribute.php" class="btn btn-lg btn-aqua m-w-150 pull-right">Next</a>
            </div>
        </div>
    <?php } elseif ($action == 'cms') {?>
		<div class="content-box">
			<div class="row">
				<div class="col-sm-12">
					<p>A page will be created to display the designer tool in an iFrame.<br />
					A dummy product named 'Inkxe test product' is created.<br />
					You may delete this product and product category from your store admin after installation.</p>
				</div>
			</div>
		</div>
		<div class="row m-t-sm">
			<div class="col-sm-12">
				<a onClick="loadLoader();" href="add_cms.php" class="btn btn-lg btn-aqua m-w-150 pull-right">Next</a>
			</div>
		</div>
    <?php } elseif ($action == 'soap') {
    try {
        require_once 'getRoleList.php';
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    ?>
    <div class="content-box">
        <div class="row">
            <div class="col-sm-12"><h3>Integration details</h3></div>
            <div class="col-sm-12">
                <form name='soap_form' id='soap_form' onSubmit="return validate_soap();" action="add_api_user_role.php" method="post" role="form" class="">
					<div class="row m-b-sm">
						<div class="col-sm-6">
							<div class="radio radio-success">
								<input type="radio" name="type" value="n" onClick="checkType(this);" id="yes" checked="checked">
								<label for="yes">CREATE NEW INTEGRATION</label>
								<input type="radio" name="type" value="e" onClick="checkType(this);" id="no">
								<label for="no">EXISTING INTEGRATION</label>
							</div>
						</div>
						<div class="col-sm-6"></div>
					</div>

                    <div id='new'>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group form-group-default required">
                                    <label>Integration Name</label>
                                    <input type="text" name="name" id="name" autocomplete="off" class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group form-group-default required">
									<label>Email</label>
									<input type="text" id="email" name="email" autocomplete="off" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id='existing' style='display:none;'>
						<input type='hidden' id='ul' value='<?php echo $ul = count($integrationList); ?>'>
						<?php if ($ul) {?>
							<div class="row m-b-sm">
								<div class="col-sm-6 p-l-0" id="ulDiv">
										<select name='integration_id' id='integration_id' onchange='setRole(this);' class="cs-select" data-init-plugin="cs-select">
											<option value="0">SELECT INTEGRATION</option>

											<?php foreach ($integrationList as $v) {?>
											<option value="<?php echo $v['integration_id'] ?>"><?php echo $v['name']; ?></option>
											<?php }?>
										</select>
								</div>
							</div>
						<?php } else {?>
							<div class="row m-b-sm alert alert-danger text-primary">
								NO EXISTING INTEGRATION PRESENT CURRENTLY. PLEASE CREATE A NEW ONE.
							</div>
						<?php }?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row m-t-sm">
			<div class="col-sm-12">
				<input type="submit" value="Next" class="btn btn-lg btn-aqua m-w-150 pull-right" />
				<span class="text-right pull-right m-t-sm m-r-md text-danger"><a href="http://inkxe.com/support/scp/faq.php?cid=6" target="_blank">Click here</a> to know more about Integration.</span>
			</div>
		</div>
	</form>
    </div>
    <?php } elseif ($action == 'setdb') { ?>
		<div class="panel-group" id="accordion">
		<?php
		$app_setting = 'store_details.json';
		$arr = array();
		if (file_exists($app_setting)) {
			@chmod($app_setting, 0777);
			$app_str = @file_get_contents($app_setting);
			$json_app = json_decode($app_str, true);
			$arr = $json_app['folder_name'];
			$url = $json_app['domain_url'];
		}?>
		<input id="total_stores" value="<?php echo count($arr) ?>" type="hidden">
		<?php foreach ($arr as $k => $v) {?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#panel<?php echo $k ?>" title="Click Here to Expand or Collapse"><?php echo $url[$k] ?></a>
					</h3>
				</div>
				<div id="panel<?php echo $k ?>" class="panel-collapse collapse">
					<div class="panel-body padd-a pos-rlt">
						<div class="row">
							<div class="col-sm-12"><h3>Database details for inkXE</h3></div>
							<form class="setdb_form" id="<?php echo $v ?>" type="post">
								<input name="folder_name" value="<?php echo $v ?>" type="hidden">
								<div class="col-sm-12 m-t-sm">
									<div class="row">
										<div class="col-sm-6">
											<div class="form-group form-group-default required">
												<label>Database Host Name</label>
												<input type="text" name="db[host]" autocomplete="off" id="<?php echo $v ?>_host" required="" class="form-control">
											</div>
										</div>
										<div class="col-sm-6">
											<div class="form-group form-group-default required">
												<label>Database Name</label>
												<input type="text" name="db[dbname]" autocomplete="off" id="<?php echo $v ?>_dbname" required="" class="form-control">
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-6">
											<div class="form-group form-group-default required">
												<label>Database Username</label>
												<input type="text" autocomplete="off" name="db[uid]" id="<?php echo $v ?>_uid" required="" class="form-control">
											</div>
										</div>
										<div class="col-sm-6">
											<div class="form-group form-group-default required">
												<label>Database Password</label>
												<input type="password" autocomplete="off" name="db[pwd]" id="<?php echo $v ?>_pwd" class="form-control">
											</div>
										</div>
									</div>
								</div>
								<div class="col-sm-12 m-t-lg"><h3>Create inkXE admin login</h3></div>
								<div class="col-sm-12 m-t-sm">
									<div class="form-group form-group-default required">
										<label>inkXE Login Id (Email ID)</label>
										<input type="text" autocomplete="off" name="user[uname]" id="<?php echo $v ?>_uname" required="" class="form-control">
									</div>
									<div class="row">
										<div class="col-sm-6">
											<div class="form-group form-group-default required">
												<label>inkXE Login Password</label>
												<input type="password" autocomplete="off" name="user[upwd]" id="<?php echo $v ?>_upwd" required="" class="form-control">
											</div>
										</div>
										<div class="col-sm-6">
											<div class="form-group form-group-default required">
												<label>Confirm Password</label>
												<input type="password" autocomplete="off" name="user[cupwd]" id="<?php echo $v ?>_cupwd" required="" class="form-control">
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-sm-6">
											<div class="form-group form-group-default required">
												<label>Security Question for Forget Password</label>
												<input type="text" autocomplete="off" name="security[question]" id="<?php echo $v ?>_que" required="" class="form-control">
											</div>
										</div>
										<div class="col-sm-6">
											<div class="form-group form-group-default required">
												<label>Security Answer</label>
												<input type="text" name="security[answer]" id="<?php echo $v ?>_ans" required="" class="form-control">
											</div>
										</div>
									</div>
								</div>
								<div class="loader" id="myLoader_<?php echo $k ?>" style="display:none;"><img class="loader-img" title="Loading" src="wizard/images/loader.gif" /></div>

								<div class="row">
									<div class="col-sm-12 text-right">
										<button type="submit" class="btn btn-lg btn-aqua m-w-150" onclick="return submitData('<?php echo $v ?>','<?php echo $k ?>','<?php echo $url[$k] ?>');">Submit</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		<?php }?>
		<div class="row m-t-sm" id="next" style='display:none;'>
			<div class="col-sm-12">
				<button class="btn btn-lg btn-aqua m-w-150 pull-right" type="button" onclick="checkSubmits(0);">Next</button>
			</div>
		</div>
    <?php } elseif ($action == 'finish') {
    require_once 'xeconfig.php';
    $upgradeUrl = XEPATH . 'xetool/setupUpgrade.php';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $upgradeUrl);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_exec($ch);
    curl_close($ch);

    $upath = '../inkxe-test-product.html'; // XEPATH.'index.php/inkxe-test-product.html';
    $apath = '../designer-tool/designer-admin/index.html'; // XEPATH.'designer-tool/designer-admin/index.html';

    $app_setting = 'store_details.json';
    $arr = array();
    $json_app = array();
    if (file_exists($app_setting)) {
        @chmod($app_setting, 0777);
        $app_str = @file_get_contents($app_setting);
        $json_app = json_decode($app_str, true);
        $arr = $json_app['folder_name'];

        $res = 0;
        foreach ($arr as $v) {
            $fPath = '../app/' . $v . '_xeconfig.xml';
            if (file_exists($fPath)) {
                $res += 1;
            }
        }
        if ($res > 0) {
            $json_app['install_status'] = '1';
            $json_app_str = json_encode($json_app);
            file_put_contents($app_setting, $json_app_str);
        }
    }
    ?>


	<div class="content-box">
		<div class="row">
		   <div class="col-sm-12">
				<h3>Congratulations !! inkXE is successfully installed in your store. Navigate to <a target="_blank" href="<?php echo $upath ?>" class="text-u-l">inkXE designer tool</a></h3>
			</div>
			<div class="col-sm-12">
				<h3> <a target="_blank" href="<?php echo $apath ?>" class="text-u-l">Login to inkXE admin</a>. </h3>
			</div>
			<div class="col-sm-12">
				<h3> Thank you!! </h3>
			</div>
		</div>
		<hr />
		<div class="row">
			<div class="col-sm-12">
				<h3> For security purpose we recommend you to delete the current xetool package, after a successful installation in your store. </h3>
			</div>
		</div>
	</div>
<?php }?>

    </div>
</body>
</html>
<?php require_once 'footer.php';?>
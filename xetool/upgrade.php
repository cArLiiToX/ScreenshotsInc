<?php
	
	if(!empty($_GET) && $_GET['newVersion']){
		require_once "../xeconfig.php";
								
		
		mysqli_report(MYSQLI_REPORT_STRICT);
		$conn = new mysqli(SERVER, USER, PASSWORD, DBNAME);
				
				
		if ($conn->connect_error) {
			echo $msg = "Connection failed. Please provide proper Database Credentials.".$conn->connect_error;
			die();
		}else{ 
			if($conn->select_db(DBNAME)){
				updateLocalSettingsJs();
				updateThemeColor($conn);
				updateLanguageJson($conn);
				updateCodeBase();
				updateDatabase($conn, $_GET['newVersion']);
				
				$upgradeUrl = XEPATH.$_GET['newVersion'].'/setupUpgrade.php';
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $upgradeUrl);
				curl_setopt($ch, CURLOPT_HEADER, 0);		
				$x = curl_exec($ch);
				curl_close($ch);

			}
		}
	}
	
				
	
	
	/*
		@Prupose : Update theme
		@Param : Database connection object
	*/
	function updateThemeColor($conn){
		$file = 'designer-tool/designer-app/allless.css';
		if(file_exists($file)){
			$sql = "SELECT theme_name, brand_primary, border_color, panel_color, stage_color, text_color FROM ".TABLE_PREFIX."theme WHERE is_default='1' LIMIT 1";
			$exe = $conn->query($sql);
			$newRec = $exe->fetch_assoc();

			if(!empty($newRec) && $newRec['theme_name'] != 'Default'){
				unset($newRec['theme_name']);
				$sql = "SELECT brand_primary,border_color,panel_color,stage_color,text_color FROM theme WHERE theme_name = 'Default' LIMIT 1";
				$exe = $conn->query($sql);
				$oldRec = $exe->fetch_assoc();
				
				$cssContent = file_get_contents($file);
				$updatedContent = str_ireplace($oldRec[0],$newRec,$cssContent);
				file_put_contents($file,$updatedContent);
			}
			@copy($file, '../designer-tool/designer-app/'.FOLDER_NAME.'/allless.css');
		}
	}

	

	/*
		@Prupose : Update Localsettings.js
	*/
	function updateLocalSettingsJs(){
		$newFile = 'designer-tool/localsettings.js';		
		$clientFile = '../'.$newFile;
			
		if(file_exists($newFile) && file_exists($clientFile)){			
			$newData = file_get_contents($newFile);
			$pos = strpos($newData,'"');$newData = substr($newData,$pos);$newData = '{'.str_replace(';','',$newData);
			$newData = json_decode($newData,true);			
			
			
			$clientDataStr = file_get_contents($clientFile);
			$pos = strpos($clientDataStr,'"');$clientDataStr = substr($clientDataStr,$pos);$clientDataStr = '{'.str_replace(';','',$clientDataStr);
			$clientData = json_decode($clientDataStr,true);
			
			
			$new_lables = array_diff_key($newData,$clientData);
			$updatedData = json_encode(array_merge($clientData,$new_lables));			
			$updatedData = 'var RIAXEAPP = {};RIAXEAPP.localSettings = '.$updatedData.';';
			file_put_contents($newFile,$updatedData);
		}
	}
	
	
	
	/*
		@Prupose : Update Language Json
		@Param : Database connection object
	*/
	function updateLanguageJson($conn){
		$app_language = "designer-tool/designer-app/languages";			
		$admin_language = "designer-tool/designer-admin/languages";			
		if(!file_exists($app_language) && !file_exists($admin_language)){return;}

		$sql = "SELECT value FROM ".TABLE_PREFIX."app_language WHERE status='1' LIMIT 1";		
		$exe = $conn->query($sql);
		$result = $exe->fetch_assoc();		
		$language = $result['value']; 		

		$admin_language_file = $admin_language.'/locale-'.$language.'.json'; 		
		if(file_exists($admin_language_file)){
			updateJson($admin_language_file);
		}
		
		$app_language_file = $app_language.'/locale-'.$language.'.json';
		if(file_exists($app_language_file)){
			updateJson($app_language_file);
		}
	}
	
	
	
	/*
		@Prupose : helper function to updateLanguageJson()
		@Param : file with path, which has new updated data
	*/
	function updateJson($newFile){
		$newData = file_get_contents($newFile);
		$newData = json_decode($newData, true);		
		
		$clientFile = '../'.$newFile;
		$clientData = file_get_contents($clientFile);
		$clientData = json_decode($clientData, true);
		
		$new_lables = array_diff_key($newData,$clientData);
		$jsondata_modified = json_encode(array_merge($clientData,$new_lables));
		file_put_contents($newFile,$jsondata_modified);
	}

	

	/*
		@Prupose : Copy and paste corresponding directories/files
	*/
	function updateCodeBase(){
		if (file_exists("frontendlc.php")) @copy("frontendlc.php", "../frontendlc.php");
		if (file_exists("designer-tool")) recurse_copy("designer-tool", "../designer-tool");
		if (file_exists("magento")) {
			if (file_exists("magento/app"))recurse_copy("magento/app", "../app");
		}
	}
	
	
	
	/*
		@Prupose : Recursively copy all the files & folders
		@Param : (string)source directory/file
				 (string)destination directory/file
	*/
	function recurse_copy($src,$dst) { 
		$dir = opendir($src); 
		@mkdir($dst); 
		while(false !== ( $file = readdir($dir)) ) { 
			if (( $file != '.' ) && ( $file != '..' )) { 
				if ( is_dir($src . '/' . $file) ) { 
					@recurse_copy($src . '/' . $file,$dst . '/' . $file); 
				} else { 
					if($file != 'app333.js'){
						@copy($src . '/' . $file, $dst . '/' . $file);
					}
				} 
			} 
		} 
		closedir($dir); 
	} 
	
	
	
	/*
		@Prupose : Update database script
		@Param : Database connection object
		@Param : Current version
	*/
	function updateDatabase($conn, $current_version){
		$version = "SELECT COUNT(*) AS nos FROM ".TABLE_PREFIX."version_manage WHERE current_version = '".$current_version."'";
		$handler = $conn->query($version);
		$row = $handler->fetch_assoc();
			
		if($row['nos'] == 0){
			$schemaVersion = "SELECT schema_version	FROM ".TABLE_PREFIX."version_manage 
				WHERE REPLACE( current_version,'.','') = (SELECT MAX( REPLACE( current_version,'.','' )) FROM ".TABLE_PREFIX."version_manage)";
			$handler = $conn->query($schemaVersion);
			$row = $handler->fetch_assoc();

			
			$updatedSchemaversion = 0;	
			if($row['schema_version']){
				$updatedSchemaversion = $row['schema_version'];
				$sqlFilespath = 'sql/schema';
				if (is_dir($sqlFilespath) === true){
					$files = array_diff(scandir($sqlFilespath), array('.', '..'));
					foreach ($files as $file){
						$arr = explode('-',$file);
						$existingSchemaVersion = intval($arr[0],10);
						if($existingSchemaVersion > $row['schema_version']){
							run_sql_file($file,$conn);
							$updatedSchemaversion = $existingSchemaVersion;
						}
					}
				}
				
				$sql = "INSERT INTO ".TABLE_PREFIX."version_manage SET current_version='".$current_version."',schema_version='".$updatedSchemaversion."', updated_on=CURDATE()";
				$status = $conn->query($sql);
			}
		}else{
			xe_log("\n".date("Y-m-d H:i:s").': Database has not updated.'."\n");
		}
	}

	
	
	/*
		@ Purpose : To run the basic script required for our designer tool
		@ Param : sqlFileName with path, dbConnectionObject
	*/
	function run_sql_file($filename,$conn){
		$commands = @file_get_contents($filename);//load file
		//delete comments
		$lines = explode("\n",$commands);
		$commands = '';
		foreach($lines as $line){
			$line = trim($line);
			if( $line && !startsWith($line,'--') ){
				$commands .= $line . "\n";
			}
		}
		$commands = explode(";", $commands);//convert to array
		//run commands
		$total = $success = 0;
		foreach($commands as $command){
			if(trim($command)){
				$success += (@$conn->query($command)==false ? 0 : 1);
				$total += 1;
			}
		}
	}
	

	
	/*
		@ This is a helper function to the above function
	*/
	function startsWith($haystack, $needle){
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}

	
	
	/*
		@ Purpose : To log errors during installation
		@ Param : Text(what to log),append(whether append or replace),fileName(where to log)
	*/
	function xe_log($text, $append=true, $fileName=''){
		$file = '../xetool_log.log'; 
		if($fileName) $file = $fileName;

		// Append the contents to the file to the end of the file and the LOCK_EX flag to prevent anyone else writing to the file at the same time
		if($append) @file_put_contents($file, $text.PHP_EOL, FILE_APPEND | LOCK_EX);
		else @file_put_contents($file, $text);
	}
<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class Upgrade extends UTIL{
	public $portalPath = 'http://inkxe.com/product_licence_php/api.php';

    /**
     *
     * It transfer the zip file from inkxe server to client's through CURL
     * @param (String) $sourceFileUrl : source zip location
     * @param (String) $destinationFileUrl : destination zip path
     * @return status
     */
    private function curlTransfer($sourceFileUrl, $destinationFileUrl){
        $ch = curl_init();
        $data = '';

		try {
            curl_setopt($ch, CURLOPT_URL, $sourceFileUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($ch);

            /* check if the zip file exists in the remote server */
            $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($retcode != 200) {
                $msg = 'Package not found';
                $status = 'failed';
                $response = array('status' => $status, 'msg' => $msg);
                $this->response($this->json($response), 200);
            }
            curl_close($ch);
        } catch (Exception $e) {
            $msg = 'Package not found';
            $status = 'failed';
            $response = array('status' => $status, 'msg' => $msg);
            $this->response($this->json($response), 200);
        }

        if ($data) {
            $file = fopen($destinationFileUrl, "w+");
            fputs($file, $data);
            fclose($file);
        }

        if (file_exists($destinationFileUrl)) {
            return 1;
        } else {
            return 'Updated Package is unable to transfer.';
        }

    }

    /**
     *
     * It unzip the upgrade zip package in client's server
     * @param (String) $destinationFileUrl : destination zip file path
     * @param (String) $upgradePackage : destination unzip path
     * @return status
     */
    private function unzipUpgradePacakge($destinationFileUrl){
        $zip = new ZipArchive;
        try {
            $zip->open($destinationFileUrl.'.zip');
            $zip->extractTo($destinationFileUrl);
            $zip->close();
        } catch (Exception $e) {
            $msg = 'File unzip failed';
            $status = 'failed';
            $response = array('status' => $status, 'msg' => $msg);
            $this->response($this->json($response), 200);
        }

        if (file_exists($destinationFileUrl . $upgradePackage)) {
            return 1;
        } else {
            return 'File unzip failed';
        }
    }

    /**
     *
     * @return string $currentToolVersion of client's server
     */
	private function getCurrentToolVersion(){
		$sql = "SELECT current_version FROM ".TABLE_PREFIX."version_manage WHERE installed_on IS NULL AND REPLACE( current_version,'.','') = (SELECT MAX( REPLACE( current_version,'.','' )) FROM ".TABLE_PREFIX."version_manage)";
        $toolVersion = $this->executeFetchAssocQuery($sql);
		return (!empty($toolVersion) && $toolVersion[0]['current_version']) ? $toolVersion[0]['current_version'] : '5.0.3'; // As default is 5.0.3
	}
	
    /**
     * @return string $storeVersionId of client's server
     */
	private function getStoreVersionId(){
        $sql = 'SELECT store_version_id FROM ' . TABLE_PREFIX . 'api_data LIMIT 1';
        $storeVersion = $this->executeFetchAssocQuery($sql);
		return $storeVersion[0]['store_version_id'];
	}

    /**
     * Replacement of file_get_contents
	 * @return url $url of client's server
     */
	private function fileGetContentsCurl($url){
		try{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);  
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 3);     
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			$data = curl_exec($ch);
			$retCode = curl_getinfo($ch);
			curl_close($ch);
			
			if($retCode['http_code'] != 200)$data = 0;
		} catch (Exception $e) {
			$data = 0;
		}
		return $data;
	}

    /**
     *
     * Sends storeVersionId and currentToolVersion to inkxe site to check whether any new built avilable or not
     * @return JSON data (new_tool_version and it's release_date if a new built is available)
     */
    public function getInkxeUpdate(){
        $storeVersionId = $this->getStoreVersionId();
		$result = array();

		if (!empty($storeVersionId)) {
			$currentToolVersion = $this->getCurrentToolVersion();
			$path = $this->portalPath . '?reqmethod=getInkxeUpdate&tool_version=' . $currentToolVersion . '&store_version_id=' . $storeVersionId;

			set_time_limit(0);
			$updateVersion = $this->fileGetContentsCurl($path);
			$result = $this->formatJSONToArray($updateVersion);
		} else {
			$result = array('status' => 'Invalid store version id.');
		}
		$this->response($this->json($result), 200);
    }

    /**
     *
     * Sends storeVersionId and newToolVersion to inkxe site to get the updated zip ready
     * @param string $newToolVersion 
     * @return string $tempPath for zip 
     */
    public function getUpgradeZip() {
		$status = 'failed';
		
		if (!empty($this->_request) && $this->_request['new_tool_version']) {
			$newToolVersion = $this->_request['new_tool_version'];
			$storeVersionId = $this->getStoreVersionId();
            $path = $this->portalPath . '?reqmethod=getUpgradeZip&tool_version=' . $newToolVersion . '&store_version_id=' . $storeVersionId;

            try {
                set_time_limit(0);
				$updateVersion = $this->fileGetContentsCurl($path);
				$result = $this->formatJSONToArray($updateVersion);
				
				if (!empty($result) && isset($result['status']) && $result['status'] == 'success') {
					$str = 'Check writable permission';
					$file = 'test_' . uniqid() . '.php';
					@file_put_contents($file, $str);

					if (file_exists($file)) {
						@chmod($file, 0777); 
						@unlink($file);
						
						$basePath = $this->getBasePath();
						$destinationFileUrl = str_replace('designer-tool', '', $basePath);
						$zipTransferStatus = $this->curlTransfer($result['zipDownloadPath'], $destinationFileUrl . $newToolVersion . '.zip');

						if ($zipTransferStatus == 1) {
						    $unzipStatus = $this->unzipUpgradePacakge($destinationFileUrl . $newToolVersion);
							
							if ($unzipStatus == 1) {
								$tempArr = explode('/', $result['zipDownloadPath']);
								$tempPath = $tempArr[count($tempArr)-2];
								$path = $this->portalPath . '?reqmethod=removeTempUpgrade&tempPath=' . $tempPath;
								$this->fileGetContentsCurl($path);
								
								$currentToolVersion = $this->getCurrentToolVersion();
								$url = $this->getCurrentUrl() . $newToolVersion . '/xetool/upgrade.php?currentVersion=' . $currentToolVersion . '&newVersion=' . $newToolVersion;
								$response = $this->fileGetContentsCurl($url);
								
								$sql = "SELECT COUNT(*) AS nos FROM " . TABLE_PREFIX . "version_manage WHERE current_version = '" . $newToolVersion . "'";
								$response = $this->executeFetchAssocQuery($sql);
								$status = (!empty($response) && isset($response[0]['nos'])) ? 'success' : 'failed';
								$result['current_tool_version'] = $newToolVersion;
							} else {
								$result['msg'] = 'Unzip could not happen.';
							}
						} else {
							$result['msg'] = 'Zip transfer failed.';
						}
					} else {
						$result['msg'] = 'The user under which your PHP is running does not have the permission to update.';
					}
				}
            } catch (Exception $e) {
                $result['msg'] = 'Caught exception in file get content:' . $e->getMessage();
            }
        }
		$result['status'] = $status;
        $this->response($this->json($result), 200);
    }
}

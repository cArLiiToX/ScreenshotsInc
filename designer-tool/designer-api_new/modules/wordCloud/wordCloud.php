<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class WordCloud extends UTIL
{

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *Get word cloud details data
     *
     *@param (String)apikey
     *@param (Int)srtIndex
     *@param (Int)range
     *@param (String)fileExtensions
     *@param (String)data
     *@return json data
     *
     */
    public function getWordcloudDetails()
    {
        $apiKey = $this->_request['apikey'];
        if ($this->isValidCall($apiKey)) {
            try {
                $srtIndex = $this->_request['srtIndex'];
                $range = $this->_request['range'];
                $sql = "SELECT * FROM " . TABLE_PREFIX . "wordcloud LIMIT $srtIndex,$range";
                $dataFromValue = $this->executeGenericDQLQuery($sql);
                $dataArray['wordcloud'] = array();
                foreach ($dataFromValue as $i => $row) {
                    $dataArray['wordcloud'][$i]['id'] = $row['id'];
                    $dataArray['wordcloud'][$i]['name'] = $row['name'];
                    $dataArray['wordcloud'][$i]['price'] = $row['price'];
                    $dataArray['wordcloud'][$i]['filename'] = $row['file_name'];
                }
                $this->closeConnection();
                $this->response($this->json($dataArray), 200);
            } catch (Exception $e) {
                $result = array('Caught exception:' => $e->getMessage());
                $this->response($this->json($result), 200);
            }
        } else {
            $msg = array("status" => "invalid");
            $this->response($this->json($msg), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Update World Cloud by id
     *
     *@param (String)apikey
     *@param (String)name
     *@param (Float)price
     *@param (int)id
     *@return json data
     *
     */
    public function updateWordcloudData()
    {
        try {
            $status = 0;
            if (!empty($this->_request) && isset($this->_request['name'])) {
                extract($this->_request);
                $id_str = implode(',', $id);
				$price = (isset($price) & $price)?$price:0.00;

				$sql = "UPDATE " . TABLE_PREFIX . "wordcloud SET name = '" . $name . "', price = '" . $price . "' WHERE id IN(" . $id_str . ")";
                $status = $this->executeGenericDMLQuery($sql);
            }
            $msg['status'] = ($status) ? 'Success' : 'Failure';
            $this->response($this->json($msg), 200);
        } catch (Exception $e) {
            $result = array('Caught exception:' => $e->getMessage());
            $this->response($this->json($result), 200);
        }
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016(dd-mm-yy)
     *Add World Cloud
     *
     *@param (String)apikey
     *@param (String)name
     *@param (Float)price
     *@param (Array)files
     *@return json data
     *
     */
    //TRUNCATE wordcloud;
    public function addBulkWordcloud()
    {
        try {
            $status = 0;
            if (!empty($this->_request) && isset($this->_request['name'])) {
                if (!empty($this->_request['files'])) {
                    $sql = array();
                    $usql = '';
                    $rsql = '';
                    $shape_id = array();
                    $fname = array();
                    $thumbBase64Data = array();
                    $isql = "INSERT INTO " . TABLE_PREFIX . "wordcloud (name, price) VALUES";
                    $usql = "UPDATE " . TABLE_PREFIX . "wordcloud SET file_name = CASE id";
                    $usql1 = '';
                    $usql2 = '';

                    $dir = $this->getWordcloudSvgPath();
                    if (!$dir) {
                        $this->response('', 204);
                    }
                    //204 - immediately termiante this request
                    if (!file_exists($dir)) {
                        mkdir($dir, 0777, true);
                    }
					
                    $this->_request['price'] = (isset($this->_request['price']) && $this->_request['price'] != '')?$this->_request['price']:0.00;
                    foreach ($this->_request['files'] as $k => $v) {
                        $sql[$k] = $isql . "('" . $this->_request['name'] . "','" . $this->_request['price'] . "')";
                        $shape_id[$k] = $this->executeGenericInsertQuery($sql[$k]);
                        $fname[$k] = 'w_' . $shape_id[$k] . '.' . $v['type'];

                        $thumbBase64Data[$k] = base64_decode($v['base64']);
                        file_put_contents($dir . $fname[$k], $thumbBase64Data[$k]);

                        $usql1 .= ' WHEN ' . $shape_id[$k] . " THEN '" . $fname[$k] . "'";
                        $usql2 .= ',' . $shape_id[$k];
                    }

                    $usql = $usql . $usql1 . ' END WHERE id IN(' . substr($usql2, 1) . ')';
                    $status = $this->executeGenericDMLQuery($usql);
                }
            }
        } catch (Exception $e) {
            $msg = array('Caught exception:' => $e->getMessage());
        }
	$msg['status'] = ($status) ? 'Success' : 'Failure';
	$this->response($this->json($msg), 200);
    }

    /**
     *
     *date created (dd-mm-yy)
     *date modified 15-4-2016 (dd-mm-yy)
     *remove word cloud
     *
     *@param (String)apikey
     *@param (String)fileNames
     *@param (Array)fileIds
     *@return json data
     *
     */
    public function removeWordcloud()
    {
        $apiKey = $this->_request['apikey'];
        $fileNamesArray = $this->_request['fileNames'];
        $fileIdsArray = $this->_request['fileIds'];
        $status = 0;
	if ($this->isValidCall($apiKey)) {
            try {
                $ids = implode("','", $fileIdsArray);
                $sql = "DELETE FROM " . TABLE_PREFIX . "wordcloud WHERE id in ('" . $ids . "')";
                $status = $this->executeGenericDMLQuery($sql);
                if ($status) {
                    $dir = '';
                    $dir = $this->getWordcloudImagePath();
                    if (!$dir) {
                        $this->response('', 204);
                    }
                    //204 - immediately termiante this request
                    for ($j = 0; $j < sizeof($fileNamesArray); $j++) {
                        $filePath = $dir . $fileNamesArray[$j];
                        $msg = '';
                        if (file_exists($filePath) && is_file($filePath)) {
                            @chmod($filePath, 0777);
                            @unlink($filePath);
                        }
                    }
                }

                $this->closeConnection();
            } catch (Exception $e) {
                $msg = array('Caught exception:' => $e->getMessage());
            }
            $msg['status'] = ($status)?'success':'failed';
        } else {
            $msg['status'] = 'invalid';
        }
	$this->response($this->json($msg), 200);
    }

    /**
     *
     *date created 7-07-2016(dd-mm-yy)
     *date modified (dd-mm-yy)
     *save word cloud image data
     *
     *@param  (String)svgData
     *
     *return svg url array
     */
    public function saveWordcloudImage()
    {
        $result = array();
        if (!empty($this->_request) && !empty($this->_request['svgData'])) {
            /* && $this->isValidCall($this->_request['apiKey'])){*/
            $dir = $this->setUserWordCloudSvgPath();
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }

            $fileUrl = $this->getUserWordCloudSvgUrl();
            $type = 'svg';
            extract($this->_request);
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }

            $fname = uniqid('ci_', true) . '.' . $type;
            $file = $dir . $fname;
            $status = file_put_contents($file, stripslashes($svgData));
            if ($status) {
                $result = $fileUrl . $fname;
            }

        }
        $msg['status'] = ($status) ? 'success' : 'failed';
        $msg['result'] = $result;
        $this->response($this->json($msg), 200);
    }
}

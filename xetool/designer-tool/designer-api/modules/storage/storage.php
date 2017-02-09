<?php
/* Check Un-authorize Access */
if (!defined('accessUser')) {
    die("Error");
}

class Storage extends StorageStore
{

    /**
     * @Purpose: clears unused order zip files downloaded through order app
     * @param string $path
     * @return string status message
     */
    public function clearOrderZipApp()
    {
        $path = $this->getOrdersPath();
        if (is_dir($path) === true) {
            $clearZipDurationMinute = self::CLEAR_ZIP_DURATION * 24 * 60; // 1 day
            $files = array_diff(scandir($path), array('.', '..'));
            foreach ($files as $file) {
                $extension = pathinfo($file, PATHINFO_EXTENSION);

                if ($extension == 'zip') {
                    $timeStamp = str_replace(array('.zip', 'orders_'), '', $file);
                    $date1 = new DateTime();
                    $date1->setTimestamp($timeStamp);

                    $date2 = new DateTime();

                    $interval = $date1->diff($date2);
                    $minuteDiff = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;

                    if ($minuteDiff > $clearZipDurationMinute) {
                        $fileToDownload = realpath($path) . DIRECTORY_SEPARATOR . $file;
                        @chmod($fileToDownload, 0777);
                        @unlink($fileToDownload);
                    }
                }
            }
        }
    }

    /**
     *
     * @Purpose: clears unused previewImages created before addToCart
     *
     */
    public function clearPreviewImage()
    {
        set_time_limit(0); /* unlimited max execution time*/
        $res = $this->getLiveQuoteRefIds();

        if (!empty($res)) {
            extract($res);
            $sql = 'SELECT refid FROM ' . TABLE_PREFIX . 'decorated_product'; // WHERE TIMESTAMPDIFF(HOUR, date_created, now())<'.$duration;//keep sql
            $rows = $this->executeFetchAssocQuery($sql);
            $refIdDecoArr = array();
            foreach ($rows as $row) {
                $refIdDecoArr[] = $row['refid'];
            }

            $refIdArr = array_merge($refIdCartArr, $refIdDecoArr);
            $path = $this->getPreviewImagePath();
            if (is_dir($path) === true && !empty($refIdArr)) {
                $dirs = array_diff(scandir($path), array('.', '..'));
                foreach ($dirs as $dir) {
                    if (!in_array($dir, $refIdArr)) {
                        $this->rrmdir($path . $dir);
                    }
                }
                $sql = 'DELETE FROM ' . TABLE_PREFIX . 'design_state WHERE id NOT IN(' . implode(',', $refIdCartArr) . ')';
                $result = $this->executeGenericDMLQuery($sql);
            }
        }
    }

    /**
     *
     * @Purpose: clears abandoned design images
     *
     */
    public function clearDesigns()
    {
        $sql = "SELECT file_name FROM " . TABLE_PREFIX . "designs";
        $rows = $this->executeFetchAssocQuery($sql);
        $fileArr = array();
        foreach ($rows as $row) {
            $fileArr[] = $row['file_name'];
        }

        $path = $this->getDesignImagePath();
        if (is_dir($path) === true && !empty($fileArr)) {
            $dirs = array_diff(scandir($path), array('.', '..'));
            foreach ($dirs as $dir) {
                if (!in_array($dir, $fileArr)) {
                    if (file_exists($path . $dir)) {
                        @chmod($path . $dir, 0777);
                        @unlink($path . $dir);
                    }
                }
            }
        }

    }

    /**
     *
     * @Purpose: clears abandoned background design images and it's thumb
     *
     */
    public function clearBackgroundDesign()
    {
        $sql = "SELECT file_name FROM " . TABLE_PREFIX . "design_background WHERE is_image='1'";
        $rows = $this->executeFetchAssocQuery($sql);
        $fileArr = array();
        $thumbArr = array();
        foreach ($rows as $row) {
            $fileArr[] = $row['file_name'];
            $thumbArr[] = 'thumb_' . $row['file_name'];
        }
        $fileArr = array_merge($fileArr, $thumbArr);

        $path = $this->getBackgroundDesignImagePath();
        if (is_dir($path) === true && !empty($fileArr)) {
            $dirs = array_diff(scandir($path), array('.', '..'));
            foreach ($dirs as $dir) {
                if (!in_array($dir, $fileArr)) {
                    if (file_exists($path . $dir)) {
                        @chmod($path . $dir, 0777);
                        @unlink($path . $dir);
                    }
                }
            }
        }
    }

    /**
     *
     * @Purpose: clears abandoned shape images
     *
     */
    public function clearShapes()
    {
        $sql = 'SELECT file_name FROM ' . TABLE_PREFIX . 'shapes';
        $rows = $this->executeFetchAssocQuery($sql);
        $fileArr = array();
        foreach ($rows as $row) {
            $fileArr[] = $row['file_name'] . '.svg';
        }

        $path = $this->getShapeImagePath();
        if (is_dir($path) === true && !empty($fileArr)) {
            $dirs = array_diff(scandir($path), array('.', '..'));
            foreach ($dirs as $dir) {
                if (!in_array($dir, $fileArr)) {
                    if (file_exists($path . $dir)) {
                        @chmod($path . $dir, 0777);
                        @unlink($path . $dir);
                    }
                }
            }
        }
    }

    /**
     *
     * @Purpose: clears abandoned distress images and it's thumb images
     *
     */
    public function clearDistress()
    {
        $sql = 'SELECT file_name FROM ' . TABLE_PREFIX . 'distress';
        $rows = $this->executeFetchAssocQuery($sql);
        $fileArr = array();
        foreach ($rows as $row) {
            $fileArr[] = $row['file_name'];
        }

        $path = $this->getDistressImagePath();
        if (is_dir($path) === true && !empty($fileArr)) {
            $dirs = array_diff(scandir($path), array('.', '..'));
            foreach ($dirs as $dir) {
                if (!in_array($dir, $fileArr)) {
                    if (file_exists($path . $dir)) {
                        @chmod($path . $dir, 0777);
                        @unlink($path . $dir);
                    }
                }
            }
        }
        $thumbPath = $path . 'thumbs' . DIRECTORY_SEPARATOR;
        if (is_dir($thumbPath) === true && !empty($fileArr)) {
            $dirs = array_diff(scandir($thumbPath), array('.', '..'));
            foreach ($dirs as $dir) {
                if (!in_array($dir, $fileArr)) {
                    if (file_exists($thumbPath . $dir)) {
                        @chmod($thumbPath . $dir, 0777);
                        @unlink($thumbPath . $dir);
                    }
                }
            }
        }
    }

    /**
     *
     * @Purpose: clears abandoned pattern images
     *
     */
    public function clearPattern()
    {
        $sql = "SELECT value FROM " . TABLE_PREFIX . "palettes WHERE is_pattern='1'";
        $rows = $this->executeFetchAssocQuery($sql);
        $fileArr = array();
        foreach ($rows as $row) {
            $fileArr[] = $row['value'];
        }

        $path = $this->getPalettePath(); //$this->getPaletteImagePath();
        if (is_dir($path) === true && !empty($fileArr)) {
            $dirs = array_diff(scandir($path), array('.', '..'));
            foreach ($dirs as $dir) {
                if (!in_array($dir, $fileArr)) {
                    if (file_exists($path . $dir)) {
                        @chmod($path . $dir, 0777);
                        @unlink($path . $dir);
                    }
                }
            }
        }
    }

    /**
     *
     * @Purpose: clears abandoned pattern images
     *
     */
    public function clearWordcloud()
    {
        $sql = "SELECT file_name FROM " . TABLE_PREFIX . "wordcloud";
        $rows = $this->executeFetchAssocQuery($sql);
        $fileArr = array();
        foreach ($rows as $row) {
            $fileArr[] = $row['file_name'];
        }

        $path = $this->getWordcloudImagePath();
        if (is_dir($path) === true && !empty($fileArr)) {
            $dirs = array_diff(scandir($path), array('.', '..'));
            foreach ($dirs as $dir) {
                if (!in_array($dir, $fileArr)) {
                    if (file_exists($path . $dir)) {
                        @chmod($path . $dir, 0777);
                        @unlink($path . $dir);
                    }
                }
            }
        }
    }

    /**
     *
     * @Purpose: clears abandoned mask svg, thumb and product mask images
     *
     */
    public function clearCustomMaskData()
    {
        $sql = "SELECT file_name FROM " . TABLE_PREFIX . "custom_maskdata";
        $rows = $this->executeFetchAssocQuery($sql);
        $fileArrProductMask = array();
        foreach ($rows as $row) {
            $fileArrProductMask[] = $row['file_name'];
        }

        $sql = "SELECT svg_image,thumb_image FROM " . TABLE_PREFIX . "mask_paths";
        $rows = $this->executeFetchAssocQuery($sql);
        $fileArrMaskSvg = array();
        $fileArrMaskThumb = array();
        foreach ($rows as $row) {
            $fileArrMaskSvg[] = $row['svg_image'];
            $fileArrMaskThumb[] = $row['thumb_image'];
        }

        $fileArr = array();
        $fileArr = array_merge($fileArrProductMask, $fileArrMaskSvg, $fileArrMaskThumb);

        $path = $this->getMaskImagePath();
        if (is_dir($path) === true && !empty($fileArr)) {
            $dirs = array_diff(scandir($path), array('.', '..'));
            foreach ($dirs as $k => $dir) {
                if (!in_array($dir, $fileArr)) {
                    if (file_exists($path . $dir)) {
                        @chmod($path . $dir, 0777);
                        @unlink($path . $dir);
                    }
                }
            }
        }
    }

    /**
     *
     * @Purpose: clears abandoned user_slots
     *
     */
    public function clearUserSlot()
    {
        $duration = self::CLEAR_USERSLOT_DURATION * 24;
        $keepSql = "SELECT id, slot_image, IF(user_id = '0', uid, user_id) AS dir FROM " . TABLE_PREFIX . "user_slot
            WHERE user_id=1 OR (user_id='0' AND TIMESTAMPDIFF(HOUR, date_created, now())<" . $duration . ")"; // Keep these
        $result = $this->executeFetchAssocQuery($keepSql);

        if (!empty($result)) {
            $keepDirs = array();
            $keepFiles = array();
            $keepIds = array();
            foreach ($result as $rows) {
                $keepDirs[$rows['dir']] = $rows['dir'];
                $keepFiles[$rows['slot_image']] = $rows['slot_image'];
                $keepIds[$rows['id']] = $rows['id'];
            }

            $path = $this->getSlotsPreviewPath();
            $ds = DIRECTORY_SEPARATOR;
            if (is_dir($path) === true) {
                $dirs = array_diff(scandir($path), array('.', '..'));
                foreach ($dirs as $k => $dir) {
                    if (!array_key_exists($dir, $keepDirs)) {
                        if (file_exists($path . $dir)) {
                            @chmod($path . $dir, 0777);
                            $this->rrmdir($path . $dir);
                        }
                    } else {
                        $innerDirs = array_diff(scandir($path . $dir), array('.', '..'));
                        foreach ($innerDirs as $file) {
                            if (!in_array($file, $keepFiles)) {
                                if (file_exists($path . $dir . $ds . $file)) {
                                    @chmod($path . $dir . $ds . $file, 0777);
                                    @unlink($path . $dir . $ds . $file);
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($keepIds)) {
                $sql = 'DELETE FROM ' . TABLE_PREFIX . 'user_slot WHERE id NOT IN(' . implode(',', $keepIds) . ')';
                $status = $this->executeGenericDMLQuery($sql);
            }
        }
    }

    /**
     *
     * @Purpose: clears abandoned product templates images
     *
     */
    public function clearProductTemplate()
    {
        $sql = "SELECT pk_id FROM " . TABLE_PREFIX . "product_template";
        $rows = $this->executeFetchAssocQuery($sql);
        $fileArr = array();
        foreach ($rows as $row) {
            $fileArr[] = $row['pk_id'];
        }

        $path = $this->setProductTemplatePath();
        if (is_dir($path) === true && !empty($fileArr)) {
            $dirs = array_diff(scandir($path), array('.', '..'));
            foreach ($dirs as $dir) {
                if (!in_array($dir, $fileArr)) {
                    if (file_exists($path . $dir)) {
                        $this->rrmdir($path . $dir);
                    }
                }
            }
        }
    }
    //getSlotsPreviewURL

    /**
     *
     * @Purpose: clears all the unmapped, unused, abandoned files saved by inkXE
     * @param : (String) $apiKey
     * @return : json data
     */
    public function clearTrash()
    {
        if (!empty($this->_request) && isset($this->_request['apiKey']) && $this->isValidCall($this->_request['apiKey'])) {
            $this->clearPreviewImage();
            $this->clearOrderZipApp();
            $this->clearDesigns();
            $this->clearBackgroundDesign();
            $this->clearShapes();
            $this->clearDistress();
            $this->clearPattern();
            $this->clearWordcloud();
            $this->clearCustomMaskData();
            $this->clearUserSlot();
            $this->clearProductTemplate();
            $status = 'success';
        } else {
            $status = 'invalid apiKey';
        }

        $msg = array('status' => $status);
        $this->response($this->json($msg), 200);
    }
}

<?php
require_once "../app/Mage.php";
// @@ Reindex for the installation @@ //
try {
    $indexCollection = Mage::getModel('index/process')->getCollection();
    foreach ($indexCollection as $index) {
        $index->reindexAll();
    }
} catch (Exception $e) {
    xe_log("\n" . date("Y-m-d H:i:s") . ': Error In 1st Step: Re-indexing Data Failed.' . $e->getMessage() . "\n");
}
/*
$process->setMode(Mage_Index_Model_Process::MODE_MANUAL)->save();//Switching off Indexes
$process->setMode(Mage_Index_Model_Process::MODE_REAL_TIME)->save();//Switching on Indexes
//Reindex All
$indexingProcesses = Mage::getSingleton('index/indexer')->getProcessesCollection();
foreach ($indexingProcesses as $process) {
$process->reindexEverything();
}*/

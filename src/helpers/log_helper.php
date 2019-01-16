<?php

function logThis($msg='', $type='app', $fileName='', $method='', $msg_type='') {
    $logName = strtolower(SITENAME) . "_$type.log";
    $fullFileName = APPROOT . "/../logs/$logName";
    if (!file_exists($fullFileName)) {

    }
    $handle = fopen($fullFileName, 'a');
    $timestamp = date('Y-m-d\TH:i:sP');
    $fullMsg = "$timestamp | $msg_type | $fileName | $method | '$msg'\n";
    fwrite($handle, $fullMsg);
    fclose($handle);
}
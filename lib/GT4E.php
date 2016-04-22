<?php // Goto4ever.com
function GT4Elog($log = NULL, $file = "logs.log") //Add a logfile in root directory and write logs
{
    if ($log == NULL) {
        $log = generateCallTrace();
    }
    $fileopen=(fopen("$file",'a'));
    fwrite($fileopen, $today = date("Y-m-d H:i:s")." -> ".$log."\r\n"); // DATETIME + LOG
    fclose($fileopen);
}

function generateCallTrace() // From jurchiks101 at gmail dot com = Return something like that : 1) X:\www\backend\index.php(15): include('X:\\Tools\\wamp\\w...')
{
    $e = new Exception();
    $trace = explode("\n", $e->getTraceAsString());
    $trace = array_reverse($trace); // reverse array to make steps line up chronologically
    array_shift($trace); // remove {main}
    array_pop($trace); // remove call to this method
    $length = count($trace);
    $result = array();
    for ($i = 0; $i < $length; $i++)
    {
        $result[] = ($i + 1)  . ')' . substr($trace[$i], strpos($trace[$i], ' ')); // replace '#someNum' with '$i)', set the right ordering
    }
    return "\n\t" . implode("\n\t", $result);
}

function GT4Esitemap()
{

}
?>
<?php
//
// Puts debug traces in trace log file

function traceLog($message, $includeRequest = false, $includeStackTrace = false) 
{
    if (PHP2Go::getConfigVal('DEBUG_TRACE', false)) 
    {
            $logFile =& PHP2Go::getConfigVal('DEBUG_TRACE_FILE', false);
            if ($logFile != '')
            {
                $dateFormat = '%Y-%m-%d %H:%M:%S';
                $logFormat = "{nl}[%s] File = %s Line = %d Function = %s{nl}%s{nl}";
                $requestFormat = "\tRequest:{nl}%s{nl}";
                $stackFormat = "\tStack Trace:{nl}%s{nl}";
                
                $arTrace = debug_backtrace();
                $caller = $arTrace[0]; 
                $functionName = (isset($caller['class']) ? $caller['class'] . $caller['type'] : '') . $caller['function'];
                
                $traceString = sprintf(
				$logFormat, strftime($dateFormat), $caller['file'], 
                                $caller['line'], $functionName, $message
                            );
                
                $traceString = (substr(PHP_OS, 0, 3) == 'WIN' ? str_replace("{nl}", "\r\n", $traceString) : str_replace("{nl}", "\n", $traceString));	
                
                if (@touch($logFile))
                    @error_log($traceString, 3, $logFile);
            }
    }
}



?>
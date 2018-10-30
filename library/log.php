<?php

    require_once ($_SERVER['DOCUMENT_ROOT'] . '/credentials/error.php');

    class Log {
	
        public static function Error($message, $error) {

			$MAX_LINE_LENGTH = 200;
			$MAX_FILE_COUNT = 10;
			$MAX_FILE_SIZE = 5 * 1024 * 1024;

			$errorFilePath = _Error::ErrorPath . "GatriexLog";

			// Get Trace info
			$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);

			$file = isset($trace[0]["file"]) ? $trace[0]["file"] : "";
			$linenum = isset($trace[0]["line"]) ? $trace[0]["line"] : "";

			$file = substr($file, strlen($_SERVER['DOCUMENT_ROOT']) + 1);
			if (!$file) {
				$file = "";
			}			

			///	===========
			///	{date}
			///	{file} on line {X}
			///	
			/// {$message}
			///
			/// {$error}
			///			
			$errorContent = str_repeat("=", $MAX_LINE_LENGTH) . PHP_EOL . 
							gmdate("Y-m-d H:i:s") . PHP_EOL . 
							$file . " on line " . $linenum . str_repeat(PHP_EOL, 2) . 
							wordwrap($message, $MAX_LINE_LENGTH, PHP_EOL) . str_repeat(PHP_EOL, 2) . 
							wordwrap($error, $MAX_LINE_LENGTH, PHP_EOL) . str_repeat(PHP_EOL, 2);

			// Roll
			if (file_exists($errorFilePath . ".log") && (filesize($errorFilePath . ".log") + strlen($errorContent) > $MAX_FILE_SIZE)) {
				for ($i = $MAX_FILE_COUNT - 1; $i > 0; $i--) {
					if (file_exists($errorFilePath . $i . ".log")) {
						if ($i + 1 == $MAX_FILE_COUNT) {
							unlink($errorFilePath . $i . ".log");
						}
						else {
							rename($errorFilePath . $i . ".log", $errorFilePath . ($i + 1) . ".log");
						}
					}
				}
				rename($errorFilePath . ".log", $errorFilePath . "1.log");
			}

			$result = file_put_contents($errorFilePath . ".log", $errorContent, FILE_APPEND);
            return $result !== false;
        }
    }
    
?>
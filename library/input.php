<?php

	require_once($_SERVER['DOCUMENT_ROOT'] . '/library/response.php');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/library/log.php');

    class Input {
	        
        public static function GetCookie($cookie) {
			$result = filter_input(INPUT_COOKIE, $cookie, FILTER_SANITIZE_STRING);
            if ($result === NULL) {
                $result = "";
            }
            return $result;
        }
        
        public static function GetEnv($param) {
			$result = filter_input(INPUT_ENV, $param, FILTER_SANITIZE_NUMBER_INT);
            if ($result === NULL) {
                $result = "";
            }
            return $result;
        }

		public static function CheckMethod($method) {
			if (strtolower($_SERVER['REQUEST_METHOD']) !== strtolower($method)) {
				$response = new Response();
				$response->valid = false;
				$response->data["Error"] = "Method Not Allowed.";
				echo json_encode($response);
				die();
			}
		}

		public static function GetDataFromURL($filter = NULL, array $optional = Array()) : Array {
			return Input::FilterData($_REQUEST, $filter, $optional);
		}

		public static function GetDataFromBody($filter = NULL, array $optional = Array()) : Array {
			$input = file_get_contents("php://input");
			
			if (empty($input)) {
				$response = new Response();
				$response->valid = false;
				$response->data["Error"] = "Error handling request.";
				echo json_encode($response);
				die();
			}

			$result = json_decode($input, true);

			if (json_last_error() !== JSON_ERROR_NONE) {
				$response = new Response();
				$response->valid = false;
				$response->data["Error"] = "Error handling request.";

				Log::Error("Error parsing JSON in Input.php", "File Contents:\r\n\r\n" . $input . "\r\n\r\nError: " . json_last_error_msg());

				echo json_encode($response);
				die();
			}

			return Input::FilterData($result, $filter, $optional);
		}

		public static function FilterData(array $data, $filter, array $optional, $parent = ""): Array {
			$response = new Response();

			if ($filter !== NULL) {
				$filteredData = filter_var_array($data, $filter);

				if ($filteredData === false) {
					$response = new Response();
					$response->valid = false;
					$response->data["Error"] = "Error handling request.";
					echo json_encode($response);
					die();
				}

				$response->valid = true;

				$result = $filteredData;
				foreach($filteredData as $key => $val) {
					$invalid = false;

					if (array_key_exists($key, $data) && is_array($data[$key])) {
						$arrFilter = $filter;
						$arrOptional = array();

						if (is_array($filter)) {
							$arrFilter = $filter[$key];
						}
						if (array_key_exists($key, $optional)) {
							$arrOptional = $optional[$key];
						}
						
						$result[$key] = Input::FilterData($data[$key], $arrFilter, $arrOptional, empty($parent) ? $key : $parent . "::" . $key);
					}
					else if ($filter === FILTER_VALIDATE_BOOLEAN || (is_array($filter) && $filter[$key] === FILTER_VALIDATE_BOOLEAN)) {
						$invalid = $val === NULL && !in_array($key, $optional);
					}
					else {
						$invalid = ($val === false) || ($val === NULL && !in_array($key, $optional));
					}

					if ($invalid) {
						$error = empty($parent) ? $key : $parent . "::" . $key;
						if ($response->valid) {
							$response->valid = false;
							$response->data["Error"] = "The following parameters are invalid: " . $error;
						}
						else {
							$response->data["Error"] .= ", " . $error;
						}
					}
				}
							
				if ($response->valid) {
					$response->data = $result;
				}
			}
			else {
				$response->valid = true;
				$response->data = $data;
			}

			if (!$response->valid) {
				echo json_encode($response);
				die();
			}

			return $response->data;
		}

    }
    
?>
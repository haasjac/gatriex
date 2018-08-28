<?php

	require_once($_SERVER['DOCUMENT_ROOT'] . '/library/response.php');

	class Request {
        
		public static function CheckMethod($method) : Response {
			$response = new Response();

			if (strtolower($_SERVER['REQUEST_METHOD']) !== strtolower($method)) {
				$response->data["Error"] = "Method Not Allowed.";
				$response->valid = false;
			}
			else {
				$response->valid = true;
			}

			return $response;
		}

		public static function GetDataFromURL() : Array {
			return $_GET;
		}

		public static function GetDataFromBody() : Array {
			$data = file_get_contents("php://input");

			$result = json_decode($data, true);

			if (json_last_error() !== JSON_ERROR_NONE) {
				return Array();
			}

			return $result;
		}

	}

?>
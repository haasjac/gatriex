<?php
    declare(strict_types=1);
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/library/authentication.php');

	class Twig {
    		
		private static $twig = NULL;
    
		public static function GetTwigOptions(): Array {        
			$twigOptions = array();
			$user = Authentication::GetCurrentUser();
        
			$twigOptions["Username"] = $user;
			$twigOptions["year"] = date("Y");
        
			return $twigOptions;
		}

		public static function GetTwig() {
			if (Twig::$twig === NULL) {
				$loader = new Twig_Loader_Filesystem($_SERVER['DOCUMENT_ROOT'] . '/pages');
				Twig::$twig = new Twig_Environment($loader, array(
					//'cache' => 'compilation_cache',
				));
			}

			return Twig::$twig;
		}

	}

?>
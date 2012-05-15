<?php
/**
 * PHP file wsp\class\NewException.class.php
 */
/**
 * Class NewException
 *
 * WebSite-PHP : PHP Framework 100% object (http://www.website-php.com)
 * Copyright (c) 2009-2012 WebSite-PHP.com
 * PHP versions >= 5.2
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @author      Emilien MOREL <admin@website-php.com>
 * @link        http://www.website-php.com
 * @copyright   WebSite-PHP.com 26/05/2011
 * @version     1.1.3
 * @access      public
 * @since       1.0.15
 */

function getDebugBacktrace($remove_nb_level=0) {
	$output = "";
    $backtrace = debug_backtrace();
	$ind = 0;
    foreach ($backtrace as $bt) {
		if ($ind >= $remove_nb_level) {
	        $args = '';
	        if (isset($bt['args'])) {
		        foreach ($bt['args'] as $a) {
		            if (!empty($args)) {
		                $args .= ', ';
		            }
		            switch (gettype($a)) {
		            case 'integer':
		            case 'double':
		                $args .= $a;
		                break;
		            case 'string':
		                $a = htmlspecialchars(substr($a, 0, 64)).((strlen($a) > 64) ? '...' : '');
		                $args .= "\"$a\"";
		                break;
		            case 'array':
		                $args .= 'Array('.count($a).')';
		                break;
		            case 'object':
		                $args .= 'Object('.get_class($a).')';
		                break;
		            case 'resource':
		                $args .= 'Resource('.strstr($a, '#').')';
		                break;
		            case 'boolean':
		                $args .= $a ? 'True' : 'False';
		                break;
		            case 'NULL':
		                $args .= 'Null';
		                break;
		            default:
		                $args .= 'Unknown';
		            }
		        }
	        }
	        $output .= "\n";
	        if (isset($bt['file'])) { $output .= "file: {$bt['line']} - {$bt['file']}\n"; }
	        $output .= "call: ";
	        if (isset($bt['class'])) { $output .= $bt['class']; }
	        if (isset($bt['type'])) { $output .= $bt['type']; }
	        if (isset($bt['function'])) { $output .= $bt['function']."(".$args.")"; }
	        $output .= "\n";
    	}
        $ind++;
    }
    return $output;
}

class NewException extends Exception
{
	private static $trace = "";
	
	/**
	 * Constructor NewException
	 * @param string $message 
	 * @param mixed $code [default value: NULL]
	 * @param string $trace 
	 */
    public function __construct($message, $code=NULL, $trace='') {
        parent::__construct($message, $code);
        self::$trace = $trace;
    }
   
	/**
	 * Method __toString
	 * @access public
	 * @return string
	 * @since 1.0.35
	 */
    public function __toString() {
    	try {
			return NewException::generateErrorMessage($this->getCode(), $this->getMessage(), $this->getFile(), $this->getLine(), (isset($this->_class)?$this->_class:""), (isset($this->_method)?$this->_method:""), (self::$trace==""?$this->getTraceAsString():self::$trace));
		} catch (Exception $e) {
			echo $e->getMessage();
			exit;
		}
    }
    
	/**
	 * Method generateErrorMessage
	 * @access static
	 * @param string $code 
	 * @param string $message 
	 * @param string $file 
	 * @param string $line 
	 * @param string $class_name 
	 * @param string $method 
	 * @param string $trace 
	 * @return string
	 * @since 1.0.35
	 */
    public static function generateErrorMessage($code, $message, $file, $line, $class_name='', $method='', $trace='') {
    	if ($message == "Class 'WebSitePhpSoapClient' not found") {
    		$message = "You must activate PHP extension php_soap in php.ini";
    	}
    	
    	$str = "";
		$str .= "<b>Message:</b> ".htmlentities(html_entity_decode($message))."<br/>";
		if (self::$trace !== false) {
	        $str .= "<b>File:</b> ".$file."<br/>";
	        $str .= "<b>Line:</b> ".$line."<br/>";
	        $str .= "<b>Class :</b> ".$class_name."<br/>\n";
			$str .= "<b>Method :</b> ".$method."<br/><br/>\n";
		}
		if ($trace == "" && self::$trace !== false) {
			if (self::$trace != "") {
				$trace = self::$trace;
			} else {
				$trace = getDebugBacktrace(2);
			}
		}
		if ($trace != "") {
        	$str .= "<b>Trace:</b><br/>".str_replace("\n", "<br/>", htmlentities($trace))."<br/>";
        }
        
        return $str;
    }

	/**
	 * Method getException
	 * @access public
	 * @return NewException
	 * @since 1.0.35
	 */
    public function getException() {
        return $this; // This will print the return from the above method __toString()
    }
   
	/**
	 * Method getStaticException
	 * @access static
	 * @param string|Exception $exception 
	 * @since 1.0.59
	 */
	public static function getStaticException($exception) {
		if (method_exists($exception, "getException")) {
         	print $exception->getException(); // $exception is an instance of this class
        } else {
        	print $exception;
        }
    }
    
	/**
	 * Method printStaticException
	 * @access static
	 * @param string|Exception $exception 
	 * @since 1.0.59
	 */
	public static function printStaticException($exception) {
		if (method_exists($exception, "getException")) {
			NewException::printStaticDebugMessage($exception->getException());
		} else {
			NewException::printStaticDebugMessage($exception);
		}
    }
    
	/**
	 * Method redirectOnError
	 * @access static
	 * @param string $buffer 
	 * @return mixed
	 * @since 1.0.35
	 */
    public static function redirectOnError($buffer) {  
		$lastError = error_get_last();  
		if(!is_null($lastError) && ($lastError['type'] === E_ERROR || $lastError['type'] === E_PARSE)) {
			$debug_msg = NewException::generateErrorMessage($lastError['type'], $lastError['message'], $lastError['file'], $lastError['line']);
			NewException::printStaticDebugMessage($debug_msg);
		}  
		return $buffer;  
	}
	
	/**
	 * Method printStaticDebugMessage
	 * @access static
	 * @param string|object $debug_msg 
	 * @since 1.0.59
	 */
	public static function printStaticDebugMessage($debug_msg) {
		// print the debug
		$GLOBALS['__ERROR_DEBUG_PAGE__'] = true;
		if ($GLOBALS['__DEBUG_PAGE_IS_PRINTING__'] == false) {
			$GLOBALS['__DEBUG_PAGE_IS_PRINTING__'] = true;
			if (gettype($debug_msg) == "object") {
				if (get_class($debug_msg) == "NewException") {
					$debug_msg = NewException::generateErrorMessage($debug_msg->code, $debug_msg->message, $debug_msg->file, $debug_msg->line);
				} else {
					$debug_msg = echo_r($debug_msg);
				}
			}
			
			if ($GLOBALS['__AJAX_PAGE__'] == false || ($GLOBALS['__AJAX_LOAD_PAGE__'] == true && $_GET['mime'] == "text/html")) {
				$_POST['debug'] = $debug_msg;
				$_GET['p'] = "error-debug";
				
				try {
					if (!defined('FORCE_SERVER_NAME') || FORCE_SERVER_NAME == "") {
						if ($_SERVER['SERVER_PORT'] == 443) {
							$from_url = "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
						} else {
							$port = "";
							if ($_SERVER['SERVER_PORT'] != 80 &&  $_SERVER['SERVER_PORT'] != "") {
								$port = ":".$_SERVER['SERVER_PORT'];
							}
							$from_url = "http://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
						}
					} else {
						$from_url = "http://".FORCE_SERVER_NAME.$_SERVER['REQUEST_URI'];
					}
					$_GET['from_url'] = $from_url;
					
					require_once(dirname(__FILE__)."/../../pages/error/error-debug.php");
					$debug_page = new ErrorDebug(self::$trace !== false?true:false);
					if (method_exists($debug_page, "InitializeComponent")) {
						$debug_page->InitializeComponent();
					}
					if (method_exists($debug_page, "Load")) {
						$debug_page->Load();
					}
					
					$debug_page->loadAllVariables();
					$__PAGE_IS_INIT__ = true;
					$debug_page->executeCallback();
					
					if (method_exists($debug_page, "Loaded")) {
						$debug_page->Loaded();
					}
					
					$split_request_uri = explode("\?", $_SERVER['REQUEST_URI']);
					if (!defined('FORCE_SERVER_NAME') || FORCE_SERVER_NAME == "") {
						if ($_SERVER['SERVER_PORT'] == 443) {
							$my_site_base_url = "https://".str_replace("//", "/", $_SERVER['SERVER_NAME'].substr($split_request_uri[0], 0, strrpos($split_request_uri[0], "/"))."/");
						} else {
							$port = "";
							if ($_SERVER['SERVER_PORT'] != 80 &&  $_SERVER['SERVER_PORT'] != "") {
								$port = ":".$_SERVER['SERVER_PORT'];
							}
							$my_site_base_url = "http://".str_replace("//", "/", $_SERVER['SERVER_NAME'].$port.substr($split_request_uri[0], 0, strrpos($split_request_uri[0], "/"))."/");
						}
					} else {
						$my_site_base_url = "http://".str_replace("//", "/", FORCE_SERVER_NAME.substr($split_request_uri[0], 0, strrpos($split_request_uri[0], "/"))."/");
					}
					if (isset($_GET['folder_level']) && $_GET['folder_level'] > 0) { // when URL rewriting with folders
						if ($my_site_base_url[strlen($my_site_base_url) - 1] == "/") {
							$my_site_base_url = substr($my_site_base_url, 0, strlen($my_site_base_url) - 1);
						}
						for ($i=0; $i < $_GET['folder_level']; $i++) {
							$pos = strrpos($my_site_base_url, "/");
							if ($pos != false && $my_site_base_url[$pos-1] != "/") {
								$my_site_base_url = substr($my_site_base_url, 0, $pos);
							} else {
								break;
							}
						}
						$my_site_base_url .= "/";
					}
					if ($my_site_base_url[strlen($my_site_base_url) - 4] == "/") {
						$my_site_base_url = substr($my_site_base_url, 0, strlen($my_site_base_url) - 3);
					}
					
					header("Content-Type: text/html");
					
					echo "<html><head><title>Debug Error - ".SITE_NAME."</title>\n";
					echo "<link type=\"text/css\" rel=\"StyleSheet\" href=\"".$my_site_base_url."combine-css/styles.php.css,angle.php.css";
					if (trim(CssInclude::getInstance()->getCssConfigFile()) != "") {
						echo "?conf_file=".CssInclude::getInstance()->getCssConfigFile();
					}
					echo "\" media=\"screen\" />\n";
					echo "<script type=\"text/javascript\" src=\"".$my_site_base_url."wsp/js/jquery/jquery-".JQUERY_VERSION.".min.js\"></script>\n";
					echo "<script type=\"text/javascript\" src=\"".$my_site_base_url."combine-js/jquery.backstretch.min.js,jquery.cookie.js\"></script>\n";
					echo "<meta name=\"Robots\" content=\"noindex, nofollow\" />\n";
					echo "<base href=\"".$my_site_base_url."\" />\n";
					echo "</head><body>\n";
					echo $debug_page->render();
					if ($GLOBALS['__AJAX_LOAD_PAGE__'] == true && $GLOBALS['__AJAX_LOAD_PAGE_ID__'] != "") {
						echo "<script type=\"text/javascript\">\n";
						echo "lauchJavascriptPage_".$GLOBALS['__AJAX_LOAD_PAGE_ID__']." = function() {\n";
						echo "	$('#idLoadPageLoadingPicture".$GLOBALS['__AJAX_LOAD_PAGE_ID__']."').attr('style', 'display:none;');\n";
						echo "	$('#idLoadPageContent".$GLOBALS['__AJAX_LOAD_PAGE_ID__']."').attr('style', 'display:block;');\n";
						echo "};\n";
						echo "	waitForJsScripts(".$GLOBALS['__AJAX_LOAD_PAGE_ID__'].");\n";
						echo "	LoadPngPicture();\n";
						echo "</script>\n";
					}
					echo "</body></html>\n";
				} catch (Exception $e) {
					echo $e->getMessage();
					NewException::sendErrorByMail($debug_msg);
				}
			} else {
				header('HTTP/1.1 500 Internal Server Error');
				if (defined('SEND_ERROR_BY_MAIL') && SEND_ERROR_BY_MAIL == true &&
					find(BASE_URL, "127.0.0.1/", 0, 0) == 0 && find(BASE_URL, "localhost/", 0, 0) == 0) {
						if (self::$trace !== false) { // standard msg "administrator is notified"
							echo __(ERROR_DEBUG_MAIL_SENT);
						} else { // no trace in the debug information
							echo $debug_msg;
						}
				} else {
					echo $debug_msg;
				}
				NewException::sendErrorByMail($debug_msg);
				exit;
			}
		}
    }
    
	/**
	 * Method sendErrorByMail
	 * @access static
	 * @param mixed $debug_msg 
	 * @param string $attachment_file 
	 * @since 1.0.100
	 */
    public static function sendErrorByMail($debug_msg, $attachment_file="") {
    	if (defined('SEND_ERROR_BY_MAIL') && SEND_ERROR_BY_MAIL == true &&
			find(BASE_URL, "127.0.0.1/", 0, 0) == 0 && find(BASE_URL, "localhost/", 0, 0) == 0) {
				$caching_file = new CacheFile(dirname(__FILE__)."/../cache/error_send_by_mail.log", CacheFile::CACHE_TIME_2MIN);
				if (($caching_file->readCache()) == false) {
					$debug_mail = $debug_msg;
					$page_object = Page::getInstance($_GET['p']);
					$debug_mail .= "<br/><b>General information:</b><br/>";
					$debug_mail .= "URL : ".$page_object->getCurrentUrl()."<br/>";
					$debug_mail .= "Referer : ".$page_object->getRefererURL()."<br/>";
					$debug_mail .= "IP : <a href='http://www.infosniper.net/index.php?ip_address=".$page_object->getRemoteIP()."' target='_blank'>".$page_object->getRemoteIP()."</a><br/>";
					$debug_mail .= "Browser : ";
					if ($page_object->getBrowserName() == "Default Browser") {
						$debug_mail .= $page_object->getBrowserUserAgent();
					} else {
						$debug_mail .= $page_object->getBrowserName()." (version: ".$page_object->getBrowserVersion().")";
					}
					$debug_mail .= "<br/>";
					$debug_mail .= "Crawler : ".($page_object->isCrawlerBot()?"true":"false")."<br/>";
					
					$caching_file->writeCache($debug_mail);
					
					try {
						$mail = new SmtpMail(SEND_ERROR_BY_MAIL_TO, SEND_ERROR_BY_MAIL_TO, "ERROR on ".SITE_NAME." !!!", $debug_mail, SMTP_MAIL, SMTP_NAME);
						$mail->setPriority(SmtpMail::PRIORITY_HIGH);
						if ($attachment_file != "" && is_file($attachment_file)) {
							$mail->addAttachment($attachment_file, basename(str_replace(".cache", ".txt", $attachment_file)));
						}
						if (($send_result = $mail->send()) != true) {
							//echo $send_result;
						}
					} catch (Exception $e) {
						$caching_file->writeCache("\n\nMail sending error:\n".$e);
					}
					$caching_file->close();
				}
		}
    }
} 
?>

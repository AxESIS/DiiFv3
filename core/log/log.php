<?php
/* ***************************************************************************
                        AxESIS PHP ENGINE (APE)
APE is a core project for the AxESIS toolset and design framework. This project
incorporates AxESIS Drop-it-in-Framework Version 2 (ADiiFv2). This project is
released under the GNU General Public License:

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <https://www.gnu.org/licenses/>.

More information can be viewed in the documentation folder.

© 2020 Mitchell Reynolds (https://mtr.id.au)
© 2020 AxESIS (https://www.axesis.com.au)

ADiiF Version: 2.0.0.0
APE Version: 3.0.0.0
Application Version: 1.0.0.0

Starting Build Date: 09 May 2020
*************************************************************************** */
namespace AXESIS\APE\V3;

require __DIR__ . '/vendor/autoload.php';

use MonologLogger;
use MonologHandlerSwiftMailerHandler;
use MonologFormatterHtmlFormatter;
use MonologProcessorWebProcessor;
use \Monolog\Handler;
use \Monolog\Formatter;

class Log{
	private $mail_transporter;
	private $logger;
	private $config;
	
	public function __construct($config)
	{
		//setup the config
		$this->config = $config;
		
		//setup and push the transporters first.
		$this->transporter();
		
		//create a new logger channel
		$this->logger = new \Monolog\Logger('core');
	}
	
	private function transporter()
	{
		if($this->config["email"]["tls"] !== "none")
			$transporter = new \Swift_SmtpTransport($this->config["email"]["smtp"], $this->config["email"]["port"], $this->config["email"]["tls"]);
		else
			$transporter = new \Swift_SmtpTransport($this->config["email"]["smtp"], $this->config["email"]["port"]);
		
		if($this->config["email"]["username"] != "")
			$transporter->setUsername($this->config["email"]["username"]);
			
		if($this->config["email"]["password"] != "")
			$transporter->setUsername($this->config["email"]["password"]);
		
		$this->mail_transporter = new \Swift_Mailer($transporter);
	}
	
	public function debug($title, $message, $data = array(), $email = false)
	{
		if($email){
			$mailmessage = new \Swift_Message($title);
			$mailmessage->setFrom([$this->config["email"]["from"]["email"] => $this->config["email"]["from"]["name"]]);

			//cycle through each to person.
			foreach($this->config["email"]["addresses"] as $e){
				$mailmessage->setTo([$e['email'] => $e['name']]);
			}

			$mailmessage->setContentType("text/html");
			
			$mailHandler = new \Monolog\Handler\SwiftMailerHandler($this->mail_transporter, $mailmessage, \Monolog\Logger::DEBUG);
			$mailHandler->setFormatter(new \Monolog\Formatter\HtmlFormatter());
			
			$this->logger->pushHandler($mailHandler);
		}
		
		//now set the handlers.
		$this->logger->pushHandler(new \Monolog\Handler\StreamHandler(__LOG_DIR__ . '/' .  date("Ymd") . ".log", \Monolog\Logger::DEBUG));
		
		$this->logger->pushProcessor(new \Monolog\Processor\WebProcessor());
		
		$this->logger->addDebug($message, $data);
	}
	
	public function info($title, $message, $data = array(), $email = false)
	{
		if($email){
			$mailmessage = new \Swift_Message($title);
			$mailmessage->setFrom(
				[
					$this->config["email"]["from"]["email"] 
						=> $this->config["email"]["from"]["name"]
				]);

			//cycle through each to person.
			foreach($this->config["email"]["addresses"] as $e){
				$mailmessage->setTo([$e['email'] => $e['name']]);
			}

			$mailmessage->setContentType("text/html");
			
			$mailHandler = new \Monolog\Handler\SwiftMailerHandler($this->mail_transporter, $mailmessage, \Monolog\Logger::INFO);
			$mailHandler->setFormatter(new \Monolog\Formatter\HtmlFormatter());
			
			$this->logger->pushHandler($mailHandler);
		}
		
		//now set the handlers. Log Dir is defined in the core class.
		$this->logger->pushHandler(new \Monolog\Handler\StreamHandler(__LOG_DIR__ . '/' . date("Ymd") . ".log", \Monolog\Logger::DEBUG));
		
		$this->logger->pushProcessor(new \Monolog\Processor\WebProcessor());
		
		$this->logger->addInfo($message, $data);
	}
	
	public function notice($title, $message, $data = array(), $email = false)
	{
		if($email){
			$mailmessage = new \Swift_Message($title);
			$mailmessage->setFrom(
				[
					$this->config["email"]["from"]["email"] 
						=> $this->config["email"]["from"]["name"]
				]);

			//cycle through each to person.
			foreach($this->config["email"]["addresses"] as $e){
				$mailmessage->setTo([$e['email'] => $e['name']]);
			}

			$mailmessage->setContentType("text/html");
			
			$mailHandler = new \Monolog\Handler\SwiftMailerHandler($this->mail_transporter, $mailmessage, \Monolog\Logger::NOTICE);
			$mailHandler->setFormatter(new \Monolog\Formatter\HtmlFormatter());
			
			$this->logger->pushHandler($mailHandler);
		}
		
		//now set the handlers.
		$this->logger->pushHandler(new \Monolog\Handler\StreamHandler(__LOG_DIR__ . '/' . date("Ymd") . ".log", \Monolog\Logger::DEBUG));
		
		$this->logger->pushProcessor(new \Monolog\Processor\WebProcessor());
		
		$this->logger->addNotice($message, $data);
	}
	
	public function warning($title, $message, $data = array(), $email = false)
	{
		if($email){
			$mailmessage = new \Swift_Message($title);
			$mailmessage->setFrom(
				[
					$this->config["email"]["from"]["email"] 
						=> $this->config["email"]["from"]["name"]
				]);

			//cycle through each to person.
			foreach($this->config["email"]["addresses"] as $e){
				$mailmessage->setTo([$e['email'] => $e['name']]);
			}

			$mailmessage->setContentType("text/html");
			
			$mailHandler = new \Monolog\Handler\SwiftMailerHandler($this->mail_transporter, $mailmessage, \Monolog\Logger::WARNING);
			$mailHandler->setFormatter(new \Monolog\Formatter\HtmlFormatter());
			
			$this->logger->pushHandler($mailHandler);
		}
		
		//now set the handlers.
		$this->logger->pushHandler(new \Monolog\Handler\StreamHandler(__LOG_DIR__ . '/' . date("Ymd") . ".log", \Monolog\Logger::DEBUG));
		
		$this->logger->pushProcessor(new \Monolog\Processor\WebProcessor());
		
		$this->logger->addWarning($message, $data);
	}
	
	public function error($title, $message, $data = array(), $email = false)
	{
		if($email){
			$mailmessage = new \Swift_Message($title);
			$mailmessage->setFrom(
				[
					$this->config["email"]["from"]["email"] 
						=> $this->config["email"]["from"]["name"]
				]);

			//cycle through each to person.
			foreach($this->config["email"]["addresses"] as $e){
				$mailmessage->setTo([$e['email'] => $e['name']]);
			}

			$mailmessage->setContentType("text/html");
			
			$mailHandler = new \Monolog\Handler\SwiftMailerHandler($this->mail_transporter, $mailmessage, \Monolog\Logger::ERROR);
			$mailHandler->setFormatter(new \Monolog\Formatter\HtmlFormatter());
			
			$this->logger->pushHandler($mailHandler);
		}
		
		//now set the handlers.
		$this->logger->pushHandler(new \Monolog\Handler\StreamHandler(__LOG_DIR__ . '/' . date("Ymd") . ".log", \Monolog\Logger::DEBUG));
		
		$this->logger->pushProcessor(new \Monolog\Processor\WebProcessor());
		
		$this->logger->addError($message, $data);
	}
	
	public function critical($title, $message, $data = array(), $email = false)
	{
		if($email){
			$mailmessage = new \Swift_Message($title);
			$mailmessage->setFrom(
				[
					$this->config["email"]["from"]["email"] 
						=> $this->config["email"]["from"]["name"]
				]);

			//cycle through each to person.
			foreach($this->config["email"]["addresses"] as $e){
				$mailmessage->setTo([$e['email'] => $e['name']]);
			}

			$mailmessage->setContentType("text/html");
			
			$mailHandler = new \Monolog\Handler\SwiftMailerHandler($this->mail_transporter, $mailmessage, \Monolog\Logger::CRITICAL);
			$mailHandler->setFormatter(new \Monolog\Formatter\HtmlFormatter());
			
			$this->logger->pushHandler($mailHandler);
		}
		
		//now set the handlers.
		$this->logger->pushHandler(new \Monolog\Handler\StreamHandler(__LOG_DIR__ . '/' . date("Ymd") . ".log", \Monolog\Logger::DEBUG));
		
		$this->logger->pushProcessor(new \Monolog\Processor\WebProcessor());
		
		$this->logger->addCritical($message, $data);
	}
	
	public function alert($title, $message, $data = array(), $email = false)
	{
		if($email){
			$mailmessage = new \Swift_Message($title);
			$mailmessage->setFrom(
				[
					$this->config["email"]["from"]["email"] 
						=> $this->config["email"]["from"]["name"]
				]);

			//cycle through each to person.
			foreach($this->config["email"]["addresses"] as $e){
				$mailmessage->setTo([$e['email'] => $e['name']]);
			}

			$mailmessage->setContentType("text/html");
			
			$mailHandler = new \Monolog\Handler\SwiftMailerHandler($this->mail_transporter, $mailmessage, \Monolog\Logger::ALERT);
			$mailHandler->setFormatter(new \Monolog\Formatter\HtmlFormatter());
			
			$this->logger->pushHandler($mailHandler);
		}
		
		//now set the handlers.
		$this->logger->pushHandler(new \Monolog\Handler\StreamHandler(__LOG_DIR__ . '/' . date("Ymd") . ".log", \Monolog\Logger::DEBUG));
		
		$this->logger->pushProcessor(new \Monolog\Processor\WebProcessor());
		
		$this->logger->addAlert($message, $data);
	}
	
	public function emergency($title, $message, $data = array(), $email = false)
	{
		if($email){
			$mailmessage = new \Swift_Message($title);
			$mailmessage->setFrom(
				[
					$this->config["email"]["from"]["email"] 
						=> $this->config["email"]["from"]["name"]
				]);

			//cycle through each to person.
			foreach($this->config["email"]["addresses"] as $e){
				$mailmessage->setTo([$e['email'] => $e['name']]);
			}

			$mailmessage->setContentType("text/html");
			
			$mailHandler = new \Monolog\Handler\SwiftMailerHandler($this->mail_transporter, $mailmessage, \Monolog\Logger::EMERGENCY);
			$mailHandler->setFormatter(new \Monolog\Formatter\HtmlFormatter());
			
			$this->logger->pushHandler($mailHandler);
		}
		
		//now set the handlers.
		$this->logger->pushHandler(new \Monolog\Handler\StreamHandler(__LOG_DIR__ . '/' . date("Ymd") . ".log", \Monolog\Logger::DEBUG));
		
		$this->logger->pushProcessor(new \Monolog\Processor\WebProcessor());
		
		$this->logger->addEmergency($message, $data);
	}
}


?>
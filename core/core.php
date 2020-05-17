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
use \Exception;

//load the log components.
require __DIR__ . "/log/log.php";

//load the bundler components.
require __DIR__ . "/bundler/absbundler.php";
require __DIR__ . "/bundler/bundler.php";

//load the module components.
require __DIR__ . "/module_information/abs_module_information.php";
require __DIR__ . "/module_information/module_information.php";

//load the cache
require __DIR__ . "/cache/cache.php";

/**
* Core Class
*
* @copyright  2020 AxESIS
* @license    GNU General Public License (https://www.gnu.org/licenses/)
* @version    Alpha: 3.0.0.0
*/ 
class core
{
	//push this into the original loader.
	static public $modules = array();
	static public $config  = array();
	static public $dependancies = array();
	static public $log = null;
	static public $db = null;
	static public $page_start_time;
	static public $page_end_time;
	static public $cache = null;
	static public $endpoints = array();
	
	
	/**
     * Page Load Time Function
	 * 
	 * returns the value for how long the page took to load
	 *
	 * @return float | null Seconds the page took to load.
	 */ 
	static function page_time_load()
	{
		return self::$page_end_time - self::$page_start_time;
	}
	
	
	/**
     * Check Directory Function
	 * 
	 * Helper Function to see if a directory exists and then creates it with the current mode. 
	 *
	 * @param string The directory path to check.
	 * @param integer | null The mode to set the new directory to. [Default: 0755]
	 * @return void
	 */ 
	private static function chkdir(string $rp, int $mode = 0755)
	{
		if(!realpath($rp))
		{
			//set the dir
			mkdir($rp);
			//set the chmod for the dir
			chmod($rp, $mode);
		}
	}
	
	/**
     * Setup Function
	 * 
	 * Does the base setup function
	 *
	 * @return void
	 */ 
	static function setup()
	{
		//load the default configurations.
		self::LoadConfigs();
		
		//set locale. 
		setlocale(LC_ALL, self::$config["default"]["site"]["locale"]);

		//set datetime. 
		date_default_timezone_set(self::$config["default"]["site"]["timezone"]);
		
		//set the rootpaths.
		define("__SITE__", self::$config["default"]["site"]["url_root"]);
		define("__URL_SITE__", self::$config["default"]["site"]["url_path"]);
		define("__ROOT_DIR__", realpath(__DIR__ . "/../"));
		
		//determine if the data directorty structure exists and all the sub folders.
		$rp = __ROOT_DIR__ . self::$config["default"]["site"]["data_dir"];
		self::chkdir($rp, 0755);
		
		$rp = __ROOT_DIR__ . self::$config["default"]["site"]["upload_dir"];
		self::chkdir($rp, 0755);
		
		$rp = __ROOT_DIR__ . self::$config["default"]["site"]["backup_dir"];
		self::chkdir($rp, 0755);
		
		$rp = __ROOT_DIR__ . self::$config["default"]["site"]["download_dir"];
		self::chkdir($rp, 0755);
		
		$rp = __ROOT_DIR__ . self::$config["default"]["site"]["log_dir"];
		self::chkdir($rp, 0777);
		
		$rp = __ROOT_DIR__ . self::$config["default"]["site"]["cache_dir"];
		self::chkdir($rp, 0777);
		
		$rp = __ROOT_DIR__ . self::$config["default"]["site"]["data_files_dir"];
		self::chkdir($rp, 0777);
		
		define("__LOG_DIR__",      realpath(__ROOT_DIR__ . self::$config["default"]["site"]["log_dir"]));
		define("__DATA_DIR__",     realpath(__ROOT_DIR__ . self::$config["default"]["site"]["data_dir"]));
		define("__CACHE_DIR__",    realpath(__ROOT_DIR__ . self::$config["default"]["site"]["cache_dir"]));
		define("__UPLOAD_DIR__",   realpath(__ROOT_DIR__ . self::$config["default"]["site"]["upload_dir"]));
		define("__BACKUP_DIR__",   realpath(__ROOT_DIR__ . self::$config["default"]["site"]["backup_dir"]));
		define("__DOWNLOAD_DIR__", realpath(__ROOT_DIR__ . self::$config["default"]["site"]["download_dir"]));
		define("__DATA_FILES_DIR__", realpath(__ROOT_DIR__ . self::$config["default"]["site"]["data_files_dir"]));
		
		//load the log module
		self::$log = new Log(self::$config["log"]);
		
		//add a debug entry
		self::$log->debug("APE V3", "Loaded Configurations");
		
		//load the modules...
		self::LoadModules();
		
		//add a debug entry
		self::$log->debug("APE V3", "Loaded Modules", self::$modules);
		
		//add a debug entry
		self::$log->debug("APE V3", "Data File Paths checked");
		
		//let's load the cache.
		self::$cache = new cache();
		
		//add a debug entry
		self::$log->debug("APE V3", "Setup Complete!");
	}
	
	/**
     * Load Configs Function
	 * 
	 * Initilizes configurations and loads them into the $config array.
	 *
	 * @param string $dir Directory of where the configuration files are. 
	 * @return void
	 */ 
	static function LoadConfigs($dir = __DIR__ . "/configurations/*.json")
	{		
		//setup a basic array full of config files.
		$configs = bundler::get_files($dir);
		
		//fix up the directory strings.
		foreach($configs as $k => $config)
			$configs[$k] = realpath($config);
		
		//loop through each configuration file.
		foreach($configs as $config)
		{
			$j = json_decode(file_get_contents($config), true);
			foreach($j as $k => $v)
				self::$config[$k] = $v;
		}
	}
	
	/**
     * Load Modules Function
	 * 
	 * Initilizes Modules and loads them into the $modules array.
	 *
	 * @param string $dir Directory of where the module files are. 
	 * @return void
	 */ 
	static function LoadModules($dir = __DIR__ . "/../modules/*.mod.json")
	{
		//get all the module files 
		$module_files = bundler::get_files($dir);
		
		//just gotta do a little bit of a cleanup...
		foreach($module_files as $k=>$module_file)
			$module_files[$k] = realpath($module_file);
		
		//cycle through to register each file
		foreach($module_files as $module_file)
		{
			//there is a bit of processing involved. Let's go step by step.
			//first let's see if the corresponding php is included with the mod.json file.
			$d = explode('/',dirname($module_file));
			$c = count($d);
			$n = $d[$c - 1];
			$fe = file_exists(dirname($module_file) . '/' . $n . '.module.php');
			
			if($fe)
			{
				//collect the JSON from the module file.
				$json = json_decode(file_get_contents($module_file), true)["mod"];
				
				$pi = new module_information($json["name"], $json["version"], $json["call_name"], $json["type"]);
				//load the module file.
				require dirname($module_file) . '/' . $n . '.module.php';
				
				//check if the class exists.
				if(!class_exists($pi->call_name)) throw new Exception("Class doesn't exist.", 500);
				
				//now let's register this module.
				foreach(self::$modules as $p)
					if($p->name == $pi->name) throw new Exception("Duplicate module detected.", 500);
				
				self::$modules[] = $pi;
			}
			else
				//no possible way for this file to exist.
				throw new Exception("Module file doesn't exist.", 404);
			
		}
	}
	
	
}
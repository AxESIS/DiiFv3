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

class cache
{
	//this contains all the cache information.
	public $store = array();
	private $file;
	
	//do the basic setup and checks
	public function __construct()
	{
		//define the storage file
		$this->file = __CACHE_DIR__ . "/" . core::$config["default"]["cache"]["storage"];
		
		//check to see if the storage file exists.
		if(!file_exists($this->file))
		{
			$fh = fopen($this->file, "w") or die("Unable to write cache storage file");
			fwrite($fh, json_encode(array()));
			fclose($fh);
		}
		
		//now we need to read any information we have here back into the $store array.
		$fh = fopen($this->file, "r") or die("Unable to read cache storage file");
		$this->store = json_decode(fread($fh, filesize($this->file)), true);
		fclose($fh);
	}
	
	public function GetQuery($query)
	{
		//convert to seconds.
		$expire = core::$config["default"]["cache"]["expire"] * 60;
		$query = md5($query); //this will also become our filename.
		
		//let's see if this hash is already in the DB...
		foreach($this->store as $k => $q)
			if($q["hash"] == $query)
			{
				$cache = __CACHE_DIR__ . "/" . $q["hash"] . ".cache";
				
				//right let's quickly check if the expire time isn't exceeded.
				if(time() > ($q["time"] + $expire))
				{
					//we now need to unlink this file and remove this from the array.
					unlink($cache);
					unset($this->store[$k]);
					
					$na = array();
					//we will now need to cleanup the array properly (remove dead keys).
					foreach($this->store as $j)
						$na[] = $j;
					
					$this->store = $na;
					
					//now update the cache information.
					$fh = fopen($this->file, "w") or die("Unable to write cache storage file");
					fwrite($fh, json_encode($this->store));
					fclose($fh);
					
					return null;
				} 
				
				//otherwise, let's return the actual object/array.
				$fh = fopen($cache, "r");
				$return = json_decode(fread($fh, filesize($cache)), true);
				fclose($fh);
				
				return $return;
			}
		//otherwise return null again.
		return null;
	}
	
	//let's do a simple cache.
	public function StoreQuery($query, $results)
	{
		//first thing we have to do is hash the query so we can store it.
		$query = md5($query); //this will also become our filename.
		$timestamp = time();
		
		$found = false;
		//let's see if this hash is already in the DB...
		foreach($this->store as $k => $q)
			if($q["hash"] == $query)
			{
				$this->store[$k]["time"] = $timestamp;
				$found = true;
			}
		
		//if it wasn't found, update the store.
		if(!$found)
			//add this to the array.
			$this->store[] = array("hash" => $query, "time" => $timestamp);
		
		//we will now write the 2 files.
		$cache = __CACHE_DIR__ . "/$query.cache";
		
		//write the new query.
		$fh = fopen($this->file, "w") or die("Unable to write cache storage file");
		fwrite($fh, json_encode($this->store));
		fclose($fh);
		
		$fh = fopen($cache, "w") or die("Unable to write cache file");
		fwrite($fh, json_encode($results));
		fclose($fh);
	}
}
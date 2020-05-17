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

/**
* Bundler Class
*
* Additional helper functions for the framework engine
*
* @copyright  2020 AxESIS
* @license    GNU General Public License (https://www.gnu.org/licenses/)
* @version    Alpha: 3.0.0.0
*/ 
class utility
{
	/**
	 * Generate Random Hex
	 * 
	 * Generates a random 10-bit hex string
	 *
	 * 
	 * @author Mitchell Reynolds
	 * @return String - 10 bit hex string
	 */ 
	static function GenerateRandomHex() : string 
	{
		return bin2hex(openssl_random_pseudo_bytes(mt_rand(10,20)));
	}
	
	/**
	 * Get Client IP
	 * 
	 * Returns the IP Address of the remote machine
	 *
	 * @param string  $file A file path of the module
	 * 
	 * @throws "UNKNOWN" if IP Address is not specified.
	 * @author Mitchell Reynolds
	 * @return String - The IP Address of the client.
	 */ 
	static function GetClientIP() {
		$ipaddress = '';
		if (getenv('HTTP_CLIENT_IP'))
			$ipaddress = getenv('HTTP_CLIENT_IP');
		else if(getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		else if(getenv('HTTP_X_FORWARDED'))
			$ipaddress = getenv('HTTP_X_FORWARDED');
		else if(getenv('HTTP_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		else if(getenv('HTTP_FORWARDED'))
		   $ipaddress = getenv('HTTP_FORWARDED');
		else if(getenv('REMOTE_ADDR'))
			$ipaddress = getenv('REMOTE_ADDR');
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}
	
	/**
	 * Remove Empty Array Values Helper Function
	 * 
	 * Removes any empty values from a specified array
	 *
	 * @param array  $array Array to filter
	 * 
	 * @author Mitchell Reynolds
	 * @return array - array filtered.
	 */ 
	static function RemoveEmptyArrayValues(array $array) : array
	{
		return array_filter($array, function($value){return !empty($value) || $value === "0";});
	}
	
	/**
	 * Copy a file, or recursively copy a folder and its contents
	 * @author      Aidan Lister <aidan@php.net>
	 * @version     1.0.1
	 * @link        http://aidanlister.com/2004/04/recursively-copying-directories-in-php/
	 * @param       string   $source    Source path
	 * @param       string   $dest      Destination path
	 * @param       int      $permissions New folder creation permissions
	 * @return      bool     Returns true on success, false on failure
	 */
	static public function xcopy($source, $dest, $permissions = 0755)
	{
		// Check for symlinks
		if (is_link($source)) 
			return symlink(readlink($source), $dest);

		// Simple copy for a file
		if (is_file($source)) 
			return copy($source, $dest);
		

		// Make destination directory
		if (!is_dir($dest)) 
			mkdir($dest, $permissions);

		// Loop through the folder
		$dir = dir($source);
		while (false !== $entry = $dir->read()) {
			// Skip pointers
			if ($entry == '.' || $entry == '..')
				continue;
			
			// Deep copy directories
			self::xcopy("$source/$entry", "$dest/$entry", $permissions);
		}

		// Clean up
		$dir->close();
		return true;
	}
	
	static public function createZip($zipfile, $toZip, $delete = false)
	{
		$zip = new \ZipArchive();
		$zip->open($zipfile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

		$files = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($toZip),
			\RecursiveIteratorIterator::LEAVES_ONLY
		);

		//now time to add them to the zip.
		foreach($files as $n => $file){
			//skip dirs, they'll be added.
			if(!$file->isDir())
			{
				$filePath = $file->getRealPath();
				$relativePath = substr($filePath, strlen($toZip) + 1);

				//add to zip
				$zip->addFile($filePath, $relativePath);
			}
		}

		$zip->close();

		//now handle the rest.
		if($delete)
			foreach($files as $n => $file)
				if(!$file->isDir())
					unlink($file);

		return true;
	}
}



?>

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
* Abstract Bundler Class
*
* @copyright  2020 AxESIS
* @license    GNU General Public License (https://www.gnu.org/licenses/)
* @version    Alpha: 3.0.0.0
*/ 
abstract class abstract_bundler
{
	/**
	 * Glob Recursive Function
	 * 
	 * Gathers an array of files within a specific pattern (directory)
	 *
	 * @param string  $pattern A directory path / file and folder names
	 * 
	 * @throws Nil. Returns empty array.
	 * @author Mitchell Reynolds
	 * @return Array of full file directory paths
	 */ 
	protected static function glob_recursive($pattern)
	{
		$files = glob($pattern);

		foreach(glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
			$files = array_merge($files, self::glob_recursive($dir.'/'.basename($pattern)));

		return $files;
	}
}
?>
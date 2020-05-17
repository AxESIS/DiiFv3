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

abstract class abstract_module_information
{
	public $module_information;
	
	public function __construct(string $module_name, string $module_version, string $class_name, string $type)
	{
		$module_name    = filter_var($module_name, FILTER_SANITIZE_STRING);
		$module_version = filter_var($module_version, FILTER_SANITIZE_STRING);
		$class_name     = filter_var($class_name, FILTER_SANITIZE_STRING);
		$type           = filter_var($type, FILTER_SANITIZE_STRING);
		
		$this->module_information = new module_information($module_name,$module_version,$class_name,$type);
	}
}
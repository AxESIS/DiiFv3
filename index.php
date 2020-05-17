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

//Class Definitions. 
use APE\V3\modules\database\mysql as mysql;

//start the page loadtime
$st = microtime(true);

//start a session.
session_start();

//load the core.
require __DIR__ . "/core/core.php";

//copy the page start time
core::$page_start_time = $st;

//run the setup
core::setup();

//mysql database connection
core::$db = new mysql(core::$config["mysql"]["hostname"], core::$config["mysql"]["username"], core::$config["mysql"]["password"], core::$config["mysql"]["database"]);

//check to see if the default site page exists...
$rp = realpath(__ROOT_DIR__ . "/site/" . core::$config["default"]["site"]["default"]);

//if the site doesn't exist, throw an exception.
if(!$rp) throw new \Exception("default site doesn't exist.", 404);

//define the default site variable.
define("__DEFAULT_SITE__", $rp);

$site = str_replace($_SERVER['DOCUMENT_ROOT'],'', realpath(pathinfo($rp)["dirname"]));
define("__DEFAULT_SITE_DIR__", realpath(pathinfo($rp)["dirname"]));
define("__DEFAULT_SITE_URL__", $site);

//only if the __DIRECT__ var is not defined.
if(!defined("__DIRECT__"))
{
	//load the default site.
	require __DEFAULT_SITE__;
}

//finish the page page load time.
core::$page_end_time = microtime(true);

//return the results from the page load time. 
if(filter_input(INPUT_GET, "page_load"))
	printf("Page loaded in %f seconds", core::page_time_load());
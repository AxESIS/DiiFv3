<?php
/*
		AxESIS PHP ENGINE (APE) - Core installation for most AxESIS Projects (This is a closed sourced project)
		Copyright (C) 2020 Mitchell Reynolds & AxESIS

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
		
		File: rest.module.php
		File Version: 1.0.0.1
		Application Version: 3.0.0.0
*/

namespace APE\V3\modules;

class rest
{
	//set class level schematics.
	private $_url;
	private $_connection;
	private $data;
	private $method;
	private $proxy;
	
	public function __construct($method, $url, $data, $proxy = null){
		$this->_connection = curl_init();
		$this->_url		   = $url;
		$this->data		   = $data;
		$this->method	   = $method;
		$this->proxy	   = $proxy;
	}//close the construct method.
	
	public function _call(){
		switch(strtolower($this->method)){
			case "delete":
			//set options
				$data = json_encode($this->data);
				curl_setopt($this->_connection, CURLOPT_URL, $this->_url);
				curl_setopt($this->_connection, CURLOPT_POSTFIELDS, $data);
				curl_setopt($this->_connection, CURLOPT_CUSTOMREQUEST, "DELETE");
		 		curl_setopt($this->_connection, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($this->_connection, CURLOPT_PROXY, $this->proxy);
				$result = curl_exec($this->_connection);
				curl_close($this->_connection);
			break;
			case "put":
				//set options
				$data = json_encode($this->data);
				curl_setopt($this->_connection, CURLOPT_URL,  $this->_url);
				curl_setopt($this->_connection, CURLOPT_CUSTOMREQUEST, "PUT");
				curl_setopt($this->_connection, CURLOPT_POSTFIELDS, $data);
		 		curl_setopt($this->_connection, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($this->_connection, CURLOPT_PROXY, $this->proxy);
				$result = curl_exec($this->_connection);
				curl_close($this->_connection);
			break;
			case "patch":
				//set options
				$data = json_encode($this->data);
				curl_setopt($this->_connection, CURLOPT_URL,  $this->_url);
				curl_setopt($this->_connection, CURLOPT_CUSTOMREQUEST, "PATCH");
				curl_setopt($this->_connection, CURLOPT_POSTFIELDS, $data);
		 		curl_setopt($this->_connection, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($this->_connection, CURLOPT_PROXY, $this->proxy);
				$result = curl_exec($this->_connection);
				curl_close($this->_connection);
			break;
			case "post":
			 //set options
				curl_setopt($this->_connection, CURLOPT_URL,  $this->_url);
				curl_setopt($this->_connection, CURLOPT_POSTFIELDS, $this->data);
		 		curl_setopt($this->_connection, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($this->_connection, CURLOPT_PROXY, $this->proxy);
				$result = curl_exec($this->_connection);
				curl_close($this->_connection);
			break;
			case "get":
				curl_setopt($this->_connection, CURLOPT_URL, $this->_url . "?" . http_build_query($this->data));
				curl_setopt($this->_connection, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($this->_connection, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($this->_connection, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($this->_connection, CURLOPT_PROXY, $this->proxy);
				$result = curl_exec($this->_connection);
				curl_close($this->_connection);
			break;
		}//close the switch
		return $result;
	}//close the call function.
}
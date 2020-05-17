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
		
		File: mysql.module.php
		File Version: 1.0.1.1
		Application Version: 3.0.0.0
*/

namespace APE\V3\modules\database;
use APE\V3\core;

class mysql
{
	private $_c;
	private $_s;
	private $sql;
	
	public function __construct(string $host = "localhost", string $username = "", string $password = "", string $database = "")
	{
		$host = filter_var($host);
		$user = filter_var($username);
		$pass = filter_var($password);
		$name = filter_var($database);
		
		$this->_c = new \PDO("mysql:dbname=$name;host=$host;", $user, $pass);
		$this->_c->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	}
	
	public function beginTransaction()
	{
		return $this->_c->beginTransaction();	
	}
	
	public function commitTransaction()
	{
		return $this->_c->commit();
	}
	
	public function failTransaction()
	{
		return $this->_c->rollBack();
	}
	
	private function has_string_keys(array $array) {
	  return count(array_filter(array_keys($array), 'is_string')) > 0;
	}
	
	public function query(string $sql, array $bind = array())
	{
		$this->sql = $sql;
		
		//we need to store this query properly later.
		//check if the binds are associative
		$assoc_array = $this->has_string_keys($bind);
		
		//loop through the binds and add them to the query.
		foreach($bind as $k => $b)
			if($assoc_array){
				//we now need to replace each keyed statement.
				$this->sql = str_replace($k, $b, $this->sql);
			}else
				$this->sql = str_replace("?", $b, $this->sql);	
		
		$this->_s = $this->_c->prepare($sql);
		if(empty($bind) or is_null($bind))
			return $this->_s->execute();
		else
			if($assoc_array)
			{
				foreach($bind as $k => $b)
				{
					switch(true){
						case is_bool($b):
							$type = \PDO::PARAM_BOOL;
							break;
						case is_null($b):
							$type = \PDO::PARAM_NULL;
							break;
						case is_int($b):
							$type = \PDO::PARAM_INT;
							break;	
						default:
							$type = \PDO::PARAM_STR;
					}
					
					//now we know the type. Let's bind this to the statement.
					$this->_s->bindValue($k, $b, $type);
				}
				return $this->_s->execute();
			}else{
				for($i = 0; $i < count($bind); $i++)
				{
					$b = $bind[$i];
					switch(true){
						case is_bool($b):
							$type = \PDO::PARAM_BOOL;
							break;
						case is_null($b):
							$type = \PDO::PARAM_NULL;
							break;
						case is_int($b):
							$type = \PDO::PARAM_INT;
							break;	
						default:
							$type = \PDO::PARAM_STR;
					}
					
					//now we know the type. Let's bind this to the statement.
					$this->_s->bindValue(($i + 1), $b, $type);
				}
				
				return $this->_s->execute();
			}
	}
	
	public function fetch($cache = false)
	{
		$response = $this->_s->fetch(\PDO::FETCH_ASSOC);
		
		//if the cache module is active and we have working cache then let's do it!
		if($cache)
		{
			if(core::$cache == null) die("Cache module is not loaded correcly!");
			
			//let's check the cache.
			$cache = core::$cache->GetQuery($this->sql);
			if($cache !== null)
				return array("count" => 1, "data" => $cache);
			else
			{
				core::$cache->StoreQuery($this->sql, $response);
				if(is_null($response) || $response === false) return array("count" => 0, "data" => null);
				return array("count" => 1, "data" => $response);
			}
		}else{
			if(is_null($response) || $response === false) return array("count" => 0, "data" => null);
			return array("count" => 1, "data" => $response);
		}
	}
	
	public function fetchAll($cache = false)
	{
		$response = $this->_s->fetchAll(\PDO::FETCH_ASSOC);

		//if the cache module is active and we have working cache then let's do it!
		if($cache)
		{
			if(core::$cache == null) die("Cache module is not loaded correcly!");
			
			//let's check the cache.
			$cache = core::$cache->GetQuery($this->sql);
			if($cache !== null)
				return array("count" => count($cache), "data" => $cache);
			else
			{
				core::$cache->StoreQuery($this->sql, $response);
				if(is_null($response) || $response === false || count($response) < 1) return json_encode(array("count" => 0, "data" => null));
				return array("count" => count($response), "data" => $response);
			}
		}else{
			if(is_null($response) || $response === false || count($response) < 1) return json_encode(array("count" => 0, "data" => null));
			return array("count" => count($response), "data" => $response);
		}
	}
	
}

?>
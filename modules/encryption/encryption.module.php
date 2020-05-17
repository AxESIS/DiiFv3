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
namespace AXESIS\APE\V3\modules\encryption;
use AXESIS\APE\V3\core;
use AXESIS\APE\V3\utility;

use Laminas\Crypt\PublicKey\RsaOptions;
use Laminas\Crypt\PublicKey\Rsa;
use Laminas\Crypt\FileCipher;
use Laminas\Crypt\BlockCipher;
use Laminas\Crypt\Symmetric\Openssl;

require __DIR__ . '/vendor/autoload.php';

/**
* API Auth Class
*
* Authenticates API calls, tracks tokens and generates tokens.
*
* @copyright  2020 AxESIS
* @license    GNU General Public License (https://www.gnu.org/licenses/)
* @version    Alpha: 2.0.0.0
*/ 

class crypt
{	
	public function __construct()
	{
		define("__CRYPT_KEY_STORE__", __DATA_FILES_DIR__ . "/crypt/");
		
		//let's do the public key encryption file stuff.
		$size = core::$config["crypt"]["key_size"];
		
		if(!file_exists(__CRYPT_KEY_STORE__ . core::$config["crypt"]["file_name"] . ".pub") || 
		   !file_exists(__CRYPT_KEY_STORE__ . core::$config["crypt"]["file_name"] . ".key") )
		{
			$rsaOptions = new RsaOptions([
				'pass_phrase' => core::$config["crypt"]["file_key"]
			]);

			$rsaOptions->generateKeys([
				'private_key_bits' => $size
			]);
			
			//let's make sure that the keys exists. 
			if(!file_exists(__CRYPT_KEY_STORE__)) mkdir(__CRYPT_KEY_STORE__, 0755);
			if(!file_exists(__CRYPT_KEY_STORE__ . core::$config["crypt"]["file_name"] . ".pub"))
				file_put_contents(__CRYPT_KEY_STORE__ . core::$config["crypt"]["file_name"] . ".pub", $rsaOptions->getPublicKey());

			if(!file_exists(__CRYPT_KEY_STORE__ . core::$config["crypt"]["file_name"] . ".key"))
				file_put_contents(__CRYPT_KEY_STORE__ . core::$config["crypt"]["file_name"] . ".key", $rsaOptions->getPrivateKey());
		}
	}
	
	public function EncryptFile($filePath, $destPath)
	{
		$fileCipher = new FileCipher;
		$fileCipher->setKey(core::$config["crypt"]["secret_key"]);
		
		return $fileCipher->encrypt($filePath, $destPath);
	}
	
	public function DecryptFile($filePath, $destPath)
	{
		$fileCipher = new FileCipher;
		$fileCipher->setKey(core::$config["crypt"]["secret_key"]);
		
		return $fileCipher->decrypt($filePath, $destPath);
	}
	
	public function PubKey_Encrypt($message)
	{
		$message      = filter_var($message, FILTER_SANITIZE_STRING);
		
		$rsa = Rsa::factory([
			'public_key'    => __CRYPT_KEY_STORE__ . core::$config["crypt"]["file_name"] . ".pub",
			'binary_output' => false
		]);
		
		return $rsa->encrypt($message);
	}
	
	public function PubKey_Decrypt($message)
	{
		$message = filter_var($message, FILTER_SANITIZE_STRING);
		
		$rsa = Rsa::factory([
			'private_key'   => __CRYPT_KEY_STORE__ . core::$config["crypt"]["file_name"] . ".key",
			'pass_phrase'   => core::$config["crypt"]["file_key"],
			'binary_output' => false
		]);
		
		return $rsa->decrypt($message);
	}
	
	public function GenerateDEK() : array
	{
		$info["time"] = time();
		$info["ip"]   = utility::GetClientIP();
		$info["key"]  = utility::GenerateRandomHex() . utility::GenerateRandomHex() .utility::GenerateRandomHex() . utility::GenerateRandomHex();
		
		return array("KEY" => $info["key"], "DEK" => base64_encode(json_encode($info)));
	}
	
	public function GenerateKEK($dek) : array
	{
		return array("RID" => utility::GenerateRandomHex(), "KEK" => $this->PubKey_Encrypt($dek));
	}
	
	public function DecipherKEK($kek) : array
	{
		return json_decode(base64_decode($this->PubKey_Decrypt($kek)),true);
	}
	
	public function BlockCipher_Encrypt($message, $key)
	{
		$blockCipher = BlockCipher::factory(
			'openssl',
			[
				'algo' => 'aes',
				'mode' => 'gcm'
			]
		);
		
		$blockCipher->setKey($key);
		return $blockCipher->encrypt($message);
	}
	
	public function BlockCipher_Decrypt($message, $key)
	{
		$blockCipher = BlockCipher::factory(
			'openssl',
			[
				'algo' => 'aes',
				'mode' => 'gcm'
			]
		);
		
		$blockCipher->setKey($key);
		return $blockCipher->decrypt($message);
	}
}
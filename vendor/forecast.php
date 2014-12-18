<?php

class WuForecast
{
	protected $_args;
	protected $_key_bank;
	protected $_api_key;
	const ERROR_MSG = 'Something Wrong';
	const BASE_LANG = 'ID';
	const BASE_CITY = 'Kuningan';

	public function __construct($args)
	{
		$this->_args = is_array($args) ? $args : null;

		$this->_key_bank = array('d4c777b679398c1f',
								 'af78709edfd4ec2a',
								 'd99ad94c123332dc',
								 '884f5119fba8dcca',
								 'ea3e5444b26c226d',
								 'fc2b1beb23d8c176'
								 );

		$this->_api_key = $this->_key_bank[array_rand($this->_key_bank)];
	}

	public function retrive_api()
	{
		$data = $this->_CacheAPI();

		if($data == null)
		{
			return self::ERROR_MSG;
		}
		else
		{
			$decode = json_decode($data, true);

			if( isset($decode['response']['error']) )
			{
				return $decode['response']['error']['description'];
			}
			else
			{
				return ArrayToObject($decode);
			}
		}
	}

	protected function _CacheAPI()
	{
		$data = $this->_args;
		$city = isset($data['city']) ? $data['city'] : 'Kuningan';
		$stored = BASEDIR.'/json/forecast-'.$city.'.json';
		$expire_cache = isset($data['expire_cache']) ? $data['expire_cache'] : strtotime('+1 Hour');

		if( file_exists($stored) AND ( filemtime($stored) < strtotime('now') ) )
		{
			unlink($stored);
			$data = $sw;
		}

		if( !file_exists($stored) )
		{
			$sw = $this->_ServiceWeather();

			if($sw == false)
			{
				$data = null;
			}
			else
			{
				@file_put_contents($stored, $sw);
				touch($stored, $expire_cache);
				$data = $sw;
			}
		}
		else
		{
			$data = @file_get_contents($stored);
		}

		return $data;
	}

	protected function _ServiceWeather()
	{
		$data = $this->_args;

		if($data == null)
		{
			return false;
		}
		else
		{
			$API_KEY = $this->_api_key;
			$server = 'http://api.wunderground.com/api';
			$key = isset($data['key']) ? $data['key'] : $API_KEY;
			$lang = isset($data['lang']) ? $data['lang'] : self::BASE_LANG;
			$city = isset($data['city']) ? $data['city'] : self::BASE_CITY;
			$forecast = isset($data['forecast']) ? ($data['forecast'] == true ? 'forecast/' : '') : '';
			$API_ENDPOINT = $server.'/'.$key.'/conditions/'.$forecast.'lang:'.$lang.'/q/'.$city.'.json';

			$cURL = new cURLs(array('url'=>$API_ENDPOINT,'type'=>'data'));
			return $cURL->access_curl();
		}
	}
}

?>
<?php
date_default_timezone_set("Asia/Jakarta");

require_once dirname(__FILE__).'/lib.php';

$city = isset($_GET['city']) ? $_GET['city'] : null;

if($city == null)
{
	header('Location:'.$_SERVER['PHP_SELF'].'?city=Kuningan');
}

$city = strtolower($city);

$data = array(	//'key'=>'d4c777b679398c1f',
				'lang'=>'ID',
				'city'=>$city,
				//'forecast'=>true,
				'expire_cache'=>strtotime('+30 Minute')
				);

$WuForecast = new WuForecast($data);
$decode = $WuForecast->retrive_api();

/*
$serialize = serialize($decode);
$unserialize = unserialize($serialize);
*/

if(is_string($decode) OR $decode==null) die($decode);

$api_forecast_icon = $decode->current_observation->icon;
$api_forecast_name = $decode->current_observation->weather;
$api_forecast_temp = intval($decode->current_observation->temp_c);
$api_forecast_city = preg_replace('/((K|k)ota|(C|c)ity| )/','',$decode->current_observation->display_location->city);
$api_forecast_time = $decode->current_observation->local_epoch;

$suffix_icon_weather = is_night_day_bool() == 0 ? 'nt_' : '';

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Wunderground API with Icon Font</title>
		<link media="all" rel="stylesheet" href="assets/css/style.css">
		<link rel="stylesheet" href="assets/css/weather-icons.css">
	</head>
	<body>
		<div class="container">
			<div class="icon-weather">
				<div class="heading-weather">
					<span class="forecast-city"><?= $api_forecast_city ?></span>
					<span class="forecast-name"><?= $api_forecast_name ?></span>
				</div>
				<div class="body-weather" style="color:#<?= convert_color_temp($api_forecast_temp) ?>">
					<span class="forecast-icon wi wi-<?= $suffix_icon_weather . $api_forecast_icon ?>"></span>
					<span class="forecast-temp"><?= $api_forecast_temp ?><i class="wi wi-celsius"></i></span>
				</div>
			</div>
		</div>
	</body>
</html>
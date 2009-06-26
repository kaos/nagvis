<?php

define('KEY', 'ABQIAAAAbL-l0p12tju9OYnJPNXPYBT3DJAw-5FLyP9xbJAH2x0dPCeSTRSPyzBWS-uRnuZRdDF49cYyKSpsdQ');

class Geocode
{
	/**
	 * @param  string $address
	 * @return array
	 */
	public function resolve($address)
	{
		$locations = array();

		$request_url = 'http://maps.google.com/maps/geo?output=xml&key=' . KEY
			. '&sensor=false&oe=utf8&q=' . urlencode($address);
		if (($xml = @simplexml_load_file($request_url)) === FALSE)
			throw new Exception('Cannot connect to Google Maps');

		switch ($xml->Response->Status->code)
		{
			case "200":
				foreach ($xml->Response->Placemark as $placemark)
				{
					$coordinates = explode(',', (string)$placemark->Point->coordinates);
					$locations[] = array(
						'point' => ($coordinates[1] . ',' . $coordinates[0]),
						'address' => substr((string)$placemark->address, 0, -2)
					);
				}
				break;

			case "500":
				throw new Exception('Google Maps server error');

			case "602":
				throw new Exception('Could not resolve the address');

			case "610":
				throw new Exception('Invalid Google Maps key');

			case "620":
				throw new Exception('Geocoding requests are sent too frequently,'
					. ' please repeat the action again after some time');

			default:
				throw new Exception('Unexpected Google Maps error');
		}

		return $locations;
	}
}

?>

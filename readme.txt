=== Fieldmanager Mapbox GL ===
Contributors: aesqe
Donate link: http://skyphe.org/donate/
Tags: fieldmanager, maps, map, mapbox, mapboxgl
Requires at least: 4.0
Tested up to: 4.5.2
Stable tag: 0.1.1

== Description ==

A plugin for Fieldmanager (https://github.com/alleyinteractive/wordpress-fieldmanager)

Adds a Fieldmanager_MapboxGL field which renders a Mapbox GL map on which you can place multiple markers

== Example use ==

``php
'locations' => new Fieldmanager_MapboxGL('Geographical locations', array(
	'index' => 'locations',
	'accesstoken' => 'YOUR MAPBOX GL TOKEN',
	// 'style' => 'YOUR CUSTOM STYLE LINK',
	// 'markersymbol' => 'YOUR CUSTOM MARKER',
	'center' => array(16.2917462418651, 43.4381888676707),
	'zoom' => 7
))
``

== Changelog ==

= 0.1.1 =
* May 10th, 2016.
* minor cosmetic modifications to better follow Fieldmanager standards

= 0.1.0 =
* May 9th, 2016.
* initial release

jQuery(document).ready(function()
{
	// prevent accidental form submit via geocoder
	jQuery("#post").on("submit", function()
	{
		var el = jQuery(document.activeElement);
		var geocoderSearch = el.is(".mapboxgl-ctrl-geocoder input[placeholder='Search']");
		var geocoderClose = el.is(".geocoder-icon-close");

		if( geocoderSearch || geocoderClose ) {
			event.preventDefault();
		}
	});

	jQuery(".fm-mapboxgl-map").each(function(i)
	{
		var optionsname = jQuery(this).parent().data("optionsname");
		var options = window["fm_mapboxgl_" + optionsname];

		if( ! options ) {
			return;
		}

		// gets the hidden input element containing saved marker coords
		var inputEl = jQuery("#" + this.id.replace(/map$/, "val"));
		var rawValues = [];
		var markers = [];

		try {
			rawValues = JSON.parse(inputEl.val().trim());
			markers = rawValues.map(function(c) {
				return createMarker({lat: c[1], lng: c[0]}, options.markersymbol);
			});
		} catch( err ) { }

		mapboxgl.accessToken = options.accesstoken;

		var map = new mapboxgl.Map({
			container: this.id,
			style: options.style,
			center: options.center,
			zoom: options.zoom
		});

		if( options.geocoder )
		{
			var geocoder = new mapboxgl.Geocoder();
			map.addControl(geocoder);

			geocoder.on("result", function(event) {
				map.setCenter(event.result.geometry.coordinates);
			});
		}

		map.on("click", function(event)
		{
			var markersSource = map.getSource("markers");
			var features = map.queryRenderedFeatures(event.point, {
				radius: 10,
				includeGeometry: true,
				layers: ["markers"]
			});

			// clicked on a marker - remove it
			if( features.length )
			{
				// "queryRenderedFeatures"-returned coords are not exactly
				// the same as marker coords on the "markers" layer, so we
				// need to determine the closest marker manually
				var featCoords = features[0].geometry.coordinates;
				var closest = markers.reduce(function(prev, curr, i, arr)
				{
					var distance = getDistance(featCoords, curr.geometry.coordinates);
					
					if( prev.distance > distance ) {
						return {index: i, distance: distance};
					}

					return prev;
				}, {index: -1, distance: Number.POSITIVE_INFINITY});

				if( closest.index > -1 ) {
					markers.splice(closest.index, 1);
				}
			}
			else
			{
				markers.push( createMarker(event.lngLat, options.markersymbol) );
			}

			// is there a better way to re-render markers?
			markersSource._dirty = true;
			markersSource.fire("change");

			var vals = markers.map(function(marker) {
				return marker.geometry.coordinates;
			});

			// update hidden input field values
			inputEl.val(JSON.stringify(vals));
		});

		map.on("style.load", function()
		{
			map.addSource("markers", {
				"type": "geojson",
				"data": {
					"type": "FeatureCollection",
					"features": markers
				}
			});

			map.addLayer({
				"id": "markers",
				"type": "symbol",
				"source": "markers",
				"interactive": true,
				"layout": {
					"icon-image": "{marker-symbol}",
					"text-field": "{title}",
					"text-font": ["Open Sans Semibold", "Arial Unicode MS Bold"],
					"text-offset": [0, 0.6],
					"text-anchor": "top"
				}
			});

			var bounds = getBounds(rawValues);

			if( bounds ) {
				map.fitBounds( mapboxgl.LngLatBounds.convert(bounds) );
			}
		});
	});

	function getDistance ( a, b )
	{
		return Math.pow(Math.abs(a[0] - b[0]), 2) + Math.pow(Math.abs(a[1] - b[1]), 2);
	}

	function createMarker ( coords, markersymbol, title, description )
	{
		title = title || "";
		description = description || "";

		return {
			"type": "Feature",
			"geometry": {
				"type": "Point",
				"coordinates": [
					coords.lng,
					coords.lat
				]
			},
			"properties": {
				"marker-symbol": markersymbol,
				"title": title,
				"description": description
			}
		};
	}

	// Taken from https://github.com/geosquare/geojson-bbox/
	function getBounds ( coords )
	{
		if( ! coords || coords.length < 2 ) {
			return false;
		}

		var bbox = [
			Number.POSITIVE_INFINITY, Number.POSITIVE_INFINITY,
			Number.NEGATIVE_INFINITY, Number.NEGATIVE_INFINITY
		];

		return coords.reduce(function(prev,coord)
		{
			return [
				Math.min(coord[0], prev[0]),
				Math.min(coord[1], prev[1]),
				Math.max(coord[0], prev[2]),
				Math.max(coord[1], prev[3])
			];
		}, bbox);
	}
});

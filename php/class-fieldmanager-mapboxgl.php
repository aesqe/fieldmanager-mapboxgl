<?php

class Fieldmanager_MapboxGL extends Fieldmanager_Field {

	public $field_class = 'mapboxgl';
	public $accesstoken = '';
	public $geocoder = true;
	public $zoom = 1;
	public $center = array(-10.004459893143633, 44.16539211301719);
	public $markersymbol = 'marker-15';
	public $options;
	public $style = 'mapbox://styles/mapbox/streets-v8';
	public $mapheight = '500px';
	public $mapwidth = '100%';

	public function __construct( $label = '', $options = array() )
	{
		add_action( 'admin_enqueue_scripts', array($this, 'add_scripts') );

		parent::__construct( $label, $options );
	}

	public function add_scripts ()
	{
		$mbgl = 'https://api.mapbox.com/mapbox-gl-js';

		wp_enqueue_style('fm-mapboxgl-css', FM_MAPBOXGL_URL . '/css/fieldmanager-mapboxgl.css');
		wp_enqueue_script('fm-mapboxgl-js', FM_MAPBOXGL_URL . '/js/fieldmanager-mapboxgl.js', array('jquery', 'fieldmanager_script'));
		wp_localize_script('fm-mapboxgl-js', 'fm_mapboxgl_' . $this->name . '_' . $this->get_seq(), array(
			'zoom' => $this->zoom,
			'center' => $this->center,
			'style' => $this->style,
			'markersymbol' => $this->markersymbol,
			'accesstoken' => $this->accesstoken,
			'geocoder' => $this->geocoder,
			'mapheight' => $this->mapheight,
			'mapwidth' => $this->mapwidth
		));

		wp_enqueue_style('mapboxgl-css', $mbgl . '/v0.18.0/mapbox-gl.css');
		wp_enqueue_script('mapboxgl', $mbgl . '/v0.18.0/mapbox-gl.js');

		if( $this->geocoder )
		{
			wp_enqueue_style('mapboxgl-geocoder-css', $mbgl . '/plugins/mapbox-gl-geocoder/v1.0.0/mapbox-gl-geocoder.css');
			wp_enqueue_script('mapboxgl-geocoder-js', $mbgl . '/plugins/mapbox-gl-geocoder/v1.0.0/mapbox-gl-geocoder.js', array('mapboxgl'));
		}
	}

	public function form_element( $value )
	{
		$id = esc_attr( $this->get_element_id() );

		if( is_array($value) ) {
			$value = json_encode($value);
		}

		return sprintf(
			'<div class="fm-mapboxgl-container" id="%s" data-optionsname="%s">
				<div class="fm-mapboxgl-map" id="%s-map" style="height:%s;width:%s"></div>
				<input type="hidden" name="%s" value="%s" id="%s-val" />
			</div>',
			esc_attr($id),
			esc_attr($this->name . '_' . $this->get_seq()),
			esc_attr($id),
			esc_attr($this->mapheight),
			esc_attr($this->mapwidth),
			esc_attr($this->get_form_name()),
			esc_attr($value),
			esc_attr($id)
		);
	}

	public function presave( $value, $current_value = array() )
	{
		if( is_string($value) ) {
			$value = json_decode($value);
		}

		return $value;
	}
}
<?php

class Fieldmanager_MapboxGL extends Fieldmanager_Field {

	public $field_class = 'mapboxgl';
	public $accesstoken;
	public $geocoder;
	public $zoom;
	public $center;
	public $markersymbol;
	public $options;
	public $style;
	public $mapheight;

	public function __construct( $label = '', $options = array() )
	{
		add_action( 'admin_enqueue_scripts', array($this, 'add_scripts') );

		$defaults = array(
			'zoom' => 1,
			'center' => array(-10.004459893143633, 44.16539211301719),
			'style' => 'mapbox://styles/mapbox/streets-v8',
			'markersymbol' => 'marker-15',
			'accesstoken' => '',
			'geocoder' => true,
			'mapheight' => '500px',
			'mapwidth' => '100%'
		);

		$this->options = wp_parse_args( $options, $defaults );

		parent::__construct( $label, $options );
	}

	public function form_element( $value )
	{
		$id = esc_attr( $this->get_element_id() );

		if( is_array($value) ) {
			$value = json_encode($value);
		}

		return '
		<div class="fm-mapboxgl-container" id="' . $id . '" 
			data-optionsname="' . $this->name . '_' . $this->get_seq() . '">
			<div class="fm-mapboxgl-map" id="' . $id . '-map" 
				style="height:' . $this->options['mapheight'] . ';
				width:' . $this->options['mapwidth'] . '">
			</div>
			<input type="hidden" 
				name="' . esc_attr( $this->get_form_name() ) . '" 
				value="' . esc_attr( $value ) . '"
				id="' . $id . '-val"
			/>
		</div>';
	}

	public function add_scripts ()
	{
		$mbgl = 'https://api.mapbox.com/mapbox-gl-js';

		wp_enqueue_style('fm-mapboxgl-css', FM_MAPBOXGL_URL . '/css/fieldmanager-mapboxgl.css');
		wp_enqueue_script('fm-mapboxgl-js', FM_MAPBOXGL_URL . '/js/fieldmanager-mapboxgl.js', array('jquery', 'fieldmanager_script'));
		wp_localize_script('fm-mapboxgl-js', 'fm_mapboxgl_' . $this->name . '_' . $this->get_seq(), $this->options);

		wp_enqueue_style('mapboxgl-css', $mbgl . '/v0.18.0/mapbox-gl.css');
		wp_enqueue_script('mapboxgl', $mbgl . '/v0.18.0/mapbox-gl.js');

		if( $this->options['geocoder'] )
		{
			wp_enqueue_style('mapboxgl-geocoder-css', $mbgl . '/plugins/mapbox-gl-geocoder/v1.0.0/mapbox-gl-geocoder.css');
			wp_enqueue_script('mapboxgl-geocoder-js', $mbgl . '/plugins/mapbox-gl-geocoder/v1.0.0/mapbox-gl-geocoder.js', array('mapboxgl'));
		}
	}

	public function presave( $value, $current_value = array() )
	{
		if( is_string($value) ) {
			$value = json_decode($value);
		}

		return $value;
	}
}
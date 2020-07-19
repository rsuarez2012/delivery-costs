<?php
 
/**
 * Plugin Name: Delivery Costs
 * Plugin URI: 
 * Description: Delivery costs Custom Shipping Method for WooCommerce
 * Version: 1.0.0
 * Author: Global Design
 * Author URI: http://globaldesign.com
 * License: GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path: /lang
 * Text Domain: deliveryCost
 */

if ( ! defined( 'WPINC' ) ) {
 
    die;
 
}
	

/*
 * Check if WooCommerce is active
 */
if (in_array('woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option('active_plugins')))){


    function request_a_shipping_quote_init() {
    	if ( !class_exists( 'Delivery_Costs_Shipping_Method' ) ) {
    		class WC_Delivery_Cost_Shipping_Method extends WC_Shipping_Method {
    			public $version = "3.22";
				public $key = "";
				public $map_uri;
				public $language;
				public $callback;
				public $libraries;
    			public function __construct($callback = "", $key = "", $libraries = "") {
    				$this->key = $key;
					$this->callback = $callback;
    				$map_uri = "https://maps.googleapis.com/maps/api/js";
    				
	    			if($key != ""){
    					$map_uri = add_query_arg(array( "key" => $key), $map_uri);
    				}
    				if($libraries != ""){
    					$map_uri = add_query_arg(array( "libraries" => $libraries), $map_uri);
    				}
    				if($callback != ""){
    					$map_uri = add_query_arg(array( "callback" => $callback), $map_uri);
    				}
    				$this->map_uri = $map_uri;
    				//print_r($this->map_uri);

    				$this->id                 = 'delivery_cost'; 
                    $this->method_title       = __( 'Delivery Cost Shipping', 'delivery_cost' );  
                    $this->method_description = __( 'Custom Shipping Method for Delivery Cost', 'delivery_cost' ); 
 
                    $this->init();
                    $this->enabled = isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : 'yes';
                    $this->title = isset( $this->settings['title'] ) ? $this->settings['title'] : __( 'Delivery Cost Shipping', 'delivery_cost' );

                    //campos de configuracion
                    $this->api_google_map = isset( $this->settings['api_google_map'] ) ? $this->settings['api_google_map'] : __( 'Api Google Maps', 'delivery_cost' );//$this->get_option( 'api_key', '' );
                    $this->address_shop = isset( $this->settings['address_shop'] ) ? $this->settings['address_shop'] : __('Adddress Shop', 'delivery_cost');
                    $this->minimum_shipping_cost = isset( $this->settings['minimum_shipping_cost'] ) ? $this->settings['minimum_shipping_cost'] : __('Minimum Shipping Cost', 'delivery_cost');
                    $this->cost_per_extra_journey = isset( $this->settings['cost_per_extra_journey'] ) ? $this->settings['cost_per_extra_journey'] : __('Cost Per Extra Journey', 'delivery_cost');
                    $this->maximum_shipping_path = isset( $this->settings['maximum_shipping_path'] ) ? $this->settings['maximum_shipping_path'] : __('Maximum Shipping Path', 'delivery_cost');


                    //var_dump($this->settings['api_google_map']);

                    
    			}
    			function init() {
                    // Load the settings API
                    $this->init_form_fields(); 
                    $this->init_settings(); 
 
                    // Save settings in admin if you have any defined
                    add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
                }
                function init_form_fields() { 
 
                    // We will add our settings here
                    $this->form_fields = array(
                        'enabled' => array(
                            'title' => __('Enable', 'delivery_cost'),
                            'type' => 'checkbox',
                            'description' => __( 'Enable this Shipping.','delivery_cost'),
                            'default' => 'yes'
                        ),
                        'title' => array(
                            'title' => __( 'Title', 'delivery_cost' ),
                            'type' => 'text',
                            'description' => __( 'Title to be display on site', 'delivery_cost' ),
                            'default' => __( 'Delivery Cost Shipping', 'delivery_cost' )
                        ),
                        'api_google_map' => array (
                            'title' => __( 'Api Google Maps', 'delivery_cost' ),
                            'type' => 'text', 
                            'description' => __( 'Your <a href="https://docs.woocommerce.com/document/woocommerce-distance-rate-shipping/#section-3">Google API Key</a>', 'woocommerce-distance-rate-shipping' )
                        ),
                        'address_shop' => array(
                            'title' => __('Address Shop', 'delivery_cost'),
                            'type' => 'text',

                        ),
                        'minimum_shipping_cost' => array(
                            'title' => __('Minimum Shipping Cost'),
                            'type' => 'text',
                        ),
                        'cost_per_extra_journey' => array(
                            'title' => __('Cost Per Extra Journey'),
                            'type' => 'text',
                        ),
                        'maximum_shipping_path' => array(
                            'title' => __('Maximum Shipping Path'),
                        ),
                    );
                }
                public function calculate_shipping( $package = array() ) {
                    
                    // We will add the cost, rate and logics in here
                    /*$cost = '40';
                    $distance;// = 0;
                    $shop;
                    $distance_max;
                    $cost_per_distance_extra;*/
                    $label = $this->title;
                     $rate = array(
                        'id' => $this->id,
                        'label' => $label,
                        'cost' => 0
                    );
 
                    $this->add_rate( $rate );
                }
                public function api_key(){
                	$api_key = $this->api_google_map;
                	return $api_key;
                	//print_r($key);
                }
                public function coordenates_shop(){
                	$coordenates_shop = $this->address_shop;
                	return $coordenates_shop;
                }
                public function maximum_shipping_path(){
                	$max_shipping_path = $this->maximum_shipping_path;
                	return $max_shipping_path;
                }
                public function minimum_shipping_cost(){
                	$min_shipping_cost = $this->minimum_shipping_cost;
                	return $min_shipping_cost; 
                }
                function cost_per_extra_journey(){
                	$cost_extra = $this->cost_per_extra_journey;
                	return $cost_extra;
                } 
                function title_method_shipping(){
                	$title_shipping = $this->title;
                	return $title_shipping;
                }

		    }
		    //campo adicional en el formulario de envio
		   
	    	function rmap() {
	    		$call_class = new WC_Delivery_Cost_Shipping_Method;

	    		global $woocommerce;
	    		//print_r($woocommerce->customer->get_shipping());
	    		//print_r("<br>");
	    		$checkout = WC()->checkout(); ?>
				<input type="button" name="update" data-index="0" id="update" value="Confirmar Dirección de envio" class="shipping_method update_totals_on_change">
		        <input type="text" name="distancia_recorrida" value="" id="distancia_recorrida">
		        <input type="text" name="shipping_cost" id="shipping_cost" value="5" class="form-row-wide update_totals_on_change">
		        <div id="map" style="width:600px;height:400px;" class="col-2">Cargando Mapa....</div>
		       
		        <!--<input type="hidden" name="shipping_costs" value="" id="shipping_costs">-->
		        
		        <script type="text/javascript">
		        	
		        	/*f (document.getElementById('shipping_method_0_flat_rate3').checked){
						alert('checkbox1 esta seleccionado');
					}*/
					
		var $j = jQuery.noConflict();
		$j(function(){
			$j( document ).ready(function( $ ) {
				$j('#update').click(function (){
					alert('Se ha confirmado la dirección de envio. Si hay algún error, introduzca la nueva dirección y presione nuevamente el botón para Confirmar la nueva Dirección.');
					$j('body').trigger('update_checkout');
				});
				if( $('input#shipping_method_0_flat_rate3').is(':checked') ) {
				    alert('checkbox1 esta Seleccionado');
				}
				
				$j('input#costo_telefono').on('click', function(){
					alert($j('#shipping_cost').val());
					$j('body').trigger("update_checkout");
				});
					//var val_input = $j('#shipping_address_2').val();

				$j('input[name=shipping_address_2]').on('input',function() { 
				   var value = $(this).val(); //obtiene el valor actual del input.
				   var name = $(this).prop('name');
				   if($(this).data("lastval") != value)
					   console.log(name+": "+value);
				    
				});
				/*$j('#shipping_method_0_flat_rate2').change(function(){
					var checkbox = $(this);
					if(checkbox.is(':checked')){
						$('#shipping_method_0_free_shipping1').css('display','none');;					
					}else{
						$('#shipping_method_0_free_shipping1').css('display','block');;
					}
				});

				*var distancia = Math.floor(Math.random()*20);
				$('#shipping_city').val(distancia);*/
				 
			});
		});
					/*var checkbox = document.getElementById('shipping_method_0_flat_rate3');
					checkbox.addEventListener("change", validaCheckbox, false);
					function validaCheckbox()
					{
						var checked = checkbox.checked;
						if(checked){
						   alert('checkbox1 esta seleccionado');
						}
					}*/

		       		function initMap(){
			       		var address_shop = "<?php echo $call_class->coordenates_shop();?>";
			       		var separar = address_shop.split(",");
			       		var lat = separar[0];
			       		var lng = separar[1];
			       		var coordenates = new google.maps.LatLng(lat, lng);
			       		var map = new google.maps.Map(document.getElementById('map'), {
			       			zoom:17,
			       			// pasar el zoom a auto configurable en el admin
			       			center:coordenates
			       		});
			       		var marker = new google.maps.Marker({
			       			position: coordenates,
			       			map:map,
			       			draggable:true
			       		});
			       		var infowindow = new google.maps.InfoWindow();
			       		var input = (document.getElementById('distancia_recorrida'));
			       		var autocomplete = new google.maps.places.Autocomplete(input);
			       		autocomplete.bindTo('bounds', map);
			       		autocomplete.addListener('place_changed', function(){
			       			infowindow.close();
			       			var place = autocomplete.getPlace();
			       			if(!place.geometry){
			       				window.alert("Autocomplete's returned place contains no geometry");
			       				return;
			       			}
			       			if(place.geometry.viewport){
			       				map.fitBounds(place.geometry.viewport);
			       			}else{
			       				map.setCenter(place.geometry.location);
			       				map.setZoom(17);
			       			}
			       			marker.setPosition(place.geometry.location);
					        marker.setVisible(true);

					        var latitud = place.geometry.location.lat();
					        var longitud = place.geometry.location.lng();
					        var destine = new google.maps.LatLng(latitud, longitud);
					        var distance = google.maps.geometry.spherical.computeDistanceBetween(coordenates, destine);
					        var distancia = distance / 1000;
					        var dist = parseInt(distancia);//monitorear
					        //ruta maxima a cubrir
					        var maximum_shipping_path = <?php echo $call_class->maximum_shipping_path();?>;
					        //costo minimo del envio
					        var minimum_shipping_cost = parseFloat("<?php echo $call_class->minimum_shipping_cost();?>");
					        //costo adicional del envio fuera de la ruta
					        var adicional = 0;
					        var extra = parseFloat("<?php echo $call_class->cost_per_extra_journey();?>")
					        var total;
					        if(dist >= maximum_shipping_path){
					        	adicional = dist - maximum_shipping_path;
					        	adicional = Math.ceil(adicional) * extra;
					        	total = parseFloat(minimum_shipping_cost + adicional);
					        	infowindow.setContent('<div><strong>' + "El costo para este envio es de: "+total+"$" + '</strong><br>');
					            infowindow.open(map, marker);
					            document.getElementById('shipping_address_2').value = total;
					        	//alert("la distancia recorrida es: "+dist+" km el costo minimo del envio: "+minimum_shipping_cost+" el costo adicional fue: "+adicional+" y el total fue: "+total);
					        }else{
					        	if(dist >= 0 || dist <= maximum_shipping_path){
					        		infowindow.setContent('<div><strong>' +  "El costo para este envio es de: "+minimum_shipping_cost+"$" + '</strong><br>');
					                infowindow.open(map, marker);
					        		document.getElementById('shipping_address_2').value = minimum_shipping_cost;
					        		//alert("la distancia recorrida es: "+dist+" km el costo minimo del envio: "+minimum_shipping_cost+" y el total fue: "+minimum_shipping_cost);
					        	}
					        }
					        marker.addListener('dragend', function(event){
					        	marker_lat = this.getPosition().lat();
					        	marker_lng = this.getPosition().lng();
					        	var change_destine = new google.maps.LatLng(marker_lat, marker_lng);
					        	var new_distance = google.maps.geometry.spherical.computeDistanceBetween(coordenates, change_destine);
					        	var new_dist = new_distance / 1000;
					        	var new_dista = parseInt(new_dist);
					        	if(new_dista >= maximum_shipping_path){
						        	adicional = new_dista - maximum_shipping_path;
						        	adicional = Math.ceil(adicional) * extra;
						        	total = parseFloat(minimum_shipping_cost + adicional);
						        	infowindow.setContent('<div><strong>' + "El costo para este envio es de: "+total+"$" + '</strong><br>');
						            infowindow.open(map, marker);
						            document.getElementById('shipping_address_2').value = total;
						        	//alert("la distancia recorrida es: "+new_dista+" km el costo minimo del envio: "+minimum_shipping_cost+" el costo adicional fue: "+adicional+" y el total fue: "+total);
						        }else{
						        	if(new_dista >= 0 || new_dista <= maximum_shipping_path){
						        		infowindow.setContent('<div><strong>' +  "El costo para este envio es de: "+minimum_shipping_cost+"$" + '</strong><br>');
						                infowindow.open(map, marker);
						                document.getElementById('shipping_address_2').value = minimum_shipping_cost;
						        		//alert("la distancia recorrida es: "+new_dista+" km el costo minimo del envio: "+minimum_shipping_cost+" y el total fue: "+minimum_shipping_cost);
						        	}
						        } 
					        	//alert(new_distance);
					        });//end dragend
			       		});

		       		}
		        </script>
		        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo $call_class->api_key();?>&libraries=places,geometry,directions&callback=initMap">
		        </script>
		        <?php 
		        
	    	}
		
		    add_action('woocommerce_after_checkout_shipping_form', 'rmap');
		    function return_cost(){
		    	//$new_cost = $_POST['shipping_cost'];
		    	$new_cost = WC()->session->customer['shipping_address_2'];
		    	//var_dump(WC()->session->customer['shipping_address_2']);
		    	return $new_cost;
		    }
		    add_action( 'woocommerce_shipping_fields', 'costo_del_envio' );
			// Our hooked in function - $fields is passed via the filter!
			// si se utiliza este hook woocommerce_shipping_fields o billing los campos deben empezar con el prefijo shipping_ o billing_ luego en nombre del campo
			function costo_del_envio( $fields ) {
			     $fields['shipping_XXX'] = array(
			     	'type'		=> 'text',
			        'label'     => __('Phone_XXX', 'woocommerce'),
				    'placeholder'   => _x('Phone', 'placeholder', 'woocommerce'),
				    'required'  => false,
				    'class'     => array('form-row-wide', 'update_totals_on_change'),
				    'clear'     => false,
				    'id' 		=> 'costo_telefono',
				    'show'      => true
				 );

			     return $fields;
			}
			function claserama_add_select_prefered_contact_method($checkout){
			    woocommerce_form_field('contactmethod',array(
			        'type' => 'text', //textarea, text,select, radio, checkbox, password
			        'required' => false, //este parámetro no valida, solo agrega un "*" al campo
			        'class' => array('form-row-wide', 'update_totals_on_change'), // un array puede ser la clase 'form-row-wide', 'form-row-first', 'form-row-last'
			        'label' => 'Método de contacto preferido',
			        'id' => 'contact'
			        ), $checkout->get_value('contactmethod')
			    );
			}
			add_action('woocommerce_after_checkout_shipping_form','claserama_add_select_prefered_contact_method');




		    add_filter( 'woocommerce_package_rates', 'woocommerce_package_rates' );
			function woocommerce_package_rates( $rates ) {
				global $woocommerce;
				$title_method = new WC_Delivery_Cost_Shipping_Method;
				$title_met = $title_method->title_method_shipping();
				//var_dump($title_met);
				//var_dump(WC()->checkout->get_value('contactmethod'));  //null
				
				//var_dump(WC()->checkout()->get_checkout_fields("shipping_XXX")); me muestra el campo
				//var_dump(WC()->session->customer);

				$valorx = WC()->customer->get_shipping_city();
				//$valorx = $woocommerce->checkout->get_value("costo_telefono");// 5;//WC()->customer->get_shipping_city();
				//var_dump($valorx);
				//jQuery( function( $ ) {
				//	  $('body').on('change', '#billing_state', function(){
				//		    $(document.body).trigger("update_checkout");
				//	  });
				//});
				foreach($rates as $key => $rate ) {
					$chosen_method    = WC()->session->get( 'chosen_shipping_methods' );
					$chosen_method     = explode(':', reset($chosen_method) );

											
					if($chosen_method[0] == "delivery_cost" && $rate->label == $title_met){
					//if($chosen_method[0] == "flat_rate" && $rate->label == "Servicio Delivery"){
						
						//$rate->cost=valor();
						$rates[$key]->cost = return_cost(); //$valorx;

					}		
				}
				return $rates;
			}

	    }// fin de verificar si la clase existe
    }//end request_a_shipping_quote_init

	add_action( 'woocommerce_shipping_init', 'request_a_shipping_quote_init' );

	function request_shipping_quote_shipping_method( $methods ) {
	    //$methods['imp_pickup_shipping_method'] = 'Imp_WC_Pickup_Shipping_Method';
	    $methods['delivery_cost'] = 'WC_Delivery_Cost_Shipping_Method';

	    return $methods;
	}
	add_filter( 'woocommerce_shipping_methods', 'request_shipping_quote_shipping_method' );



} 

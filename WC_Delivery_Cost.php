<?php
class WC_Delivery_Cost {

      public function __construct() {
        // called just before the woocommerce template functions are included
        // llamado justo antes de que se incluyan las funciones de plantilla de woocommerce

        add_action( 'init', array( $this, 'include_template_functions' ), 20 );

        // called only after woocommerce has finished loading
        // llamado solo después de que woocommerce ha terminado de cargar
        add_action( 'woocommerce_init', array( $this, 'woocommerce_loaded' ) );

        // called after all plugins have loaded
        // llamado después de que se hayan cargado todos los complementos
        add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );

        // indicates we are running the admin
        //  indica que estamos ejecutando el administrador
        if ( is_admin() ) {
          // ...
        }

        // indicates we are being served over ssl
        //  indica que estamos siendo atendidos por SSL

        if ( is_ssl() ) {
          // ...
        }

        // take care of anything else that needs to be done immediately upon plugin instantiation, here in the constructor
        //  se encarga de cualquier otra cosa que deba hacerse inmediatamente después de la creación de instancias del complemento, aquí en el constructor

      }//end construct

      /**
       * Override any of the template functions from woocommerce/woocommerce-template.php
       * with our own template functions file
       *
       * Anule cualquiera de las funciones de plantilla de woocommerce / woocommerce-template.php
       * con nuestro propio archivo de funciones de plantilla
       */
      public function include_template_functions() {
        include( 'woocommerce-template.php' );
      }

      /**
       * Take care of anything that needs woocommerce to be loaded.
       * For instance, if you need access to the $woocommerce global
       *
       * Cuida de cualquier cosa que necesite woocommerce para cargarse.
       * Por ejemplo, si necesita acceso al $ woocommerce global
       */
      public function woocommerce_loaded() {
        // ...
      }

      /**
       * Take care of anything that needs all plugins to be loaded
       */
      public function plugins_loaded() {
        // ...
      }
    }

    // finally instantiate our plugin class and add it to the set of globals

    $GLOBALS['wc_acme'] = new WC_Acme();
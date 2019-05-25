<?php

/*
Plugin Name: Buscador de sondas espaciales
Plugin URI: https://www.ua.es/wikidata
Description: Plugin creado en base a "University of Alicante Wikidata Plugin" adaptado para buscar sondas espaciales filtrando por decadas
Version: 1.0
Author: Marcos Checa Pérez
Author URI: https://quieroserseo.com
*/

/**
 * Funcion que instancia el Widget
 */
//function mywikidata_create_widget(){
    //include_once(plugin_dir_path( __FILE__ ).'/includes/widget.php');
    //register_widget('mywikidata_widget');
//}
//add_action('widgets_init', 'mywikidata_create_widget');

require_once 'easyrdf/vendor/autoload.php';

if (!function_exists('write_log')) {
    function write_log ( $log )  {
        if ( true === WP_DEBUG ) {
            if ( is_array( $log ) || is_object( $log ) ) {
                error_log( print_r( $log, true ) );
            } else {
                error_log( $log );
            }
        }
    }
}

register_activation_hook(__FILE__,'mywikidata_install');

function mywikidata_install() {
   global $wp_version;
   If (version_compare($wp_version, "2.9", "<"))
    {
      deactivate_plugins(basename(__FILE__)); // Deactivate plugin
      wp_die("This plugin requires WordPress version 2.9 or higher.");
    }

    // create page
    check_pages_live();
}

add_filter( 'template_include', 'wikidata_page_template');

function wikidata_page_template( $template ) {

    if ( is_page( 'sondas-espaciales' )  ) {
        $new_template = plugin_dir_path( __FILE__ ) . 'templates/wikidata-page-template.php';
		return $new_template;
    }

    return $template;
}


function check_pages_live(){
    if(get_page_by_title( 'sondas-espaciales') == NULL) {
        create_pages_fly('sondas-espaciales');
    }
}

function create_pages_fly($pageName) {
	$createPage = array(
	  'post_title'    => $pageName,
	  'post_content'  => 'Wikidata Search Example',
	  'post_status'   => 'publish',
	  'post_author'   => 1,
	  'post_type'     => 'page',
	  'post_name'     => $pageName
	);

	// Insert the post into the database
	wp_insert_post( $createPage );
}

function my_enqueued_assets() {
    wp_enqueue_style('my-css-file', plugin_dir_url(__FILE__) . '/css/style.css', '', time());
}
add_action('wp_enqueue_scripts', 'my_enqueued_assets');


function movement_wikidata_call($decada){

	$sparql = new EasyRdf_Sparql_Client('http://query.wikidata.org/sparql');

	echo "<h2>Listado de lanzamiento de sondas espaciales en la decada de los ".$decada."</h2>";
    $decadaux = $decada+10;
		$result = $sparql->query(
      'SELECT ?item ?itemLabel ?launchdate ?operator ?operatorLabel (SAMPLE(?image) AS ?image)'.
      'WHERE'.
      '{'.
      	'?item wdt:P31 wd:Q26529 .'.
          '?item wdt:P619 ?launchdate .'.
          '?item wdt:P137 ?operator .'.
          'FILTER (?launchdate > "'.$decada.'-01-01T00:00:00+05:30"^^xsd:dateTime && ?launchdate < "'.$decadaux.'-01-01T00:00:00+05:30"^^xsd:dateTime)'.
      	'SERVICE wikibase:label { bd:serviceParam wikibase:language "[AUTO_LANGUAGE],en" }'.
          'OPTIONAL { ?item wdt:P18 ?image }'.
      '}'.
      'GROUP BY ?item ?itemLabel ?launchdate ?operator ?operatorLabel'
		);


  /*  $result = $sparql->query(
      'SELECT ?barba ?barbaLabel ?imagen WHERE {'.
      'SERVICE wikibase:label { bd:serviceParam wikibase:language "[AUTO_LANGUAGE],en". }'.
      '?barba wdt:P180 wd:Q42804.'.
      'OPTIONAL { ?barba wdt:P18 ?imagen. }'.
    '}'.
    'LIMIT '.$numresults
  );*/

  /*SELECT ?barbaLabel ?imagen WHERE {
  SERVICE wikibase:label { bd:serviceParam wikibase:language "[AUTO_LANGUAGE],en". }
  ?barba wdt:P180 wd:Q42804.
  OPTIONAL { ?barba wdt:P18 ?imagen. }
}
LIMIT 100*/
echo " <div class='lista'> ";
		foreach ($result as $row) {
    //  echo "Se han econtrado: ";
      echo "<div class='display'><header class=''><h3 class=''><a href='".$row->item."'><span>".$row->itemLabel."</span></a></h3></header>";
      echo "<p>Lanzado el: ".$row->launchdate."</p>";
      echo "<p>Lanzado por: <a href='".$row->operator."'>".$row->operatorLabel."</a></p>";

      if(isset($row->image)){
            echo "<img src='".$row->image."' class='imgwiki'>";
      }
      else{
        echo "<img max-width='390' max-height='200' src='https://via.placeholder.com/300?text=No-hay-imagen' class=''></header>";
      }
      echo "</div>";



		}
  echo "</div> ";

}
?>

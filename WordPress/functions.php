<?php
/**
 * Theme functions and definitions.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * https://developers.elementor.com/docs/hello-elementor-theme/
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_CHILD_VERSION', '2.0.0' );

/**
 * Load child theme scripts & styles.
 *
 * @return void
 */
function hello_elementor_child_scripts_styles() {

	wp_enqueue_style(
		'hello-elementor-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		[
			'hello-elementor-theme-style',
		],
		HELLO_ELEMENTOR_CHILD_VERSION
	);

}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_scripts_styles', 20 );

//------------------------------- Before is Elementor stuff --------

function printDB(){
	// -> works like . in java , so "$wpdb->get_results" would be "wpdb.get_results" in java
	global $wpdb;
	$result = $wpdb->get_results ( 'SELECT * FROM Queue' );
	//echo "<h3 style='display:inline'>Årgang</h3> <h3 style='display:inline'>Klasse</h3> <h3 style='display:inline' >Tidspunkt</h3> <br>";
    HTMLDisplayString($result);
}

function HTMLDisplayString($result = array()) {
	foreach ($result as $row) {
        $time = substr($row->time, 0, -3);
            echo "<script>
            document.getElementById('DataBaseDisplay').insertAdjacentHTML('beforeend', '<div id=\"{$time}:00\"> <input type=\"checkbox\"> <h4 style=\"display:inline\">{$row->class} </h4> <h4 style=\"display:inline\">{$row->gradYear} </h4> <h4 style=\"display:inline\">{$time} </h4> </div> <br>'); 
                document.getElementById('DataBaseDisplay').insertAdjacentHTML('beforeend', '<hr id=\"{$time}:00\" style=\"width:80%;text-align:centered;margin-left:10%\">');
                </script>
            ";
    }
}

//Action takes place during WP hook (Someplace in the loading of the site the 'wp' "hook" is activated and we do the action)
add_action('shutdown', 'page_check');
//Check if page is queueadmin else do nothing
function page_check() {
	if (is_page('queueadmin')) {
		printDB();
		//add_action('post_to_DB', 'testFunc2');
		//add_action('admin_post_post_to_DB', 'addToDB');
	}
}
<?php
/**
 * Plugin Name: KvK API
 * Plugin URI: https://voys.nl
 * Description: Fetch company data from KvK API
 * Version: 1.0
 * Author: Mark
 * Author URI: https://voys.nl
 */

add_action('wp_ajax_retrieve_kvk_data', 'retrieve_kvk_data');
add_action('wp_enqueue_scripts', 'enqueue_assets' );

/**
 *
 */
function enqueue_assets() {
	wp_register_script( 'voys-kvk',trailingslashit( plugin_dir_url(__FILE__) ) . "js/voys-kvk.js", array( 'jquery' ) );
    wp_enqueue_script('voys-kvk');
	wp_localize_script(
		'voys-kvk-js',
		'voys_kvk',
		array(
			'admin_url' => admin_url( 'admin-ajax.php' ),
		)
	);
}
/**
 * Add HTML to display a form on the front-end
 */
function form_html() {
	?>
	<form id="getKvkResult" action="postKvkNumber" method="POST">
		<label for="kvkNumberInput"></label>
		<input type="text" id="kvkNumberInput" placeholder="69599084" name="kvkNumberInput">
		<button type="button" id="kvkButton">Indienen</button>
	</form>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            $(document).on('click', '#kvkButton', function (event ) {
                event.preventDefault();
                event.stopImmediatePropagation();

                let kvkNumber = document.getElementById('kvkNumberInput').value();

                $.ajax({
                    type: "POST",
                    url: voys_kvk.ajaxurl,
                    action: 'retrieve_kvk_data',
                    dataType: 'json',
                    kvkNumber: kvkNumber,

                    success: function (data) {

                    }
                });
            });
        });
    </script>
	<?php
}

/**
 * Get a result from KvK API
 */
function retrieve_kvk_data() {

//	if ( isset($_POST['kvkNumberInput'] ) ) {

//		$kvkNumber = intval( $_POST['kvkNumberInput'] );

//		$response = wp_remote_get( "https://developers.kvk.nl/test/api/v1/zoeken?kvkNummer=$kvkNumber&pagina=1&aantal=10");
		$response = wp_remote_get( "https://developers.kvk.nl/test/api/v1/zoeken?kvkNummer=69599084&pagina=1&aantal=10");
        $body = json_decode( wp_remote_retrieve_body($response) );
        $results = $body->resultaten;

        if ( ! empty( $results ) ) {
            ?>
            <div class="results">
                <?php
                // Loop through results
                foreach( $results as $result ) {
                    ?>
                        <div class="result-container" style="margin-bottom: 20px;">
                            <div class="name">Naam: <?php echo $result->handelsnaam ?></div>
                            <div class="kvknumber">kvkNummer: <?php echo $result->kvkNummer ?></div>
                            <div class="vestigingsnummer">vestigingsnummer: <?php echo $result->handelsnaam ?></div>
                            <div class="plaats">Plaats: <?php echo $result->plaats ?></div>
                            <div class="straatnaam">straatnaam: <?php echo $result->straatnaam ?></div>
                        </div>
                    <?php
                }
                ?>
            </div>
            <?php
        }
//	}
}

retrieve_kvk_data();

?>
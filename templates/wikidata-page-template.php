<?php
/**
 * Template Name: Wikidata Page
 *
 */
?>

<?php get_header() ?>

<div class="container flex">

		<fieldset class="fs">
            <legend>Buscador de sondas espaciales</legend>
			<p>Consulta las sondas espaciales que se han lanzado en cada década</p>
			<form method="post" name="front_end" action="" >
				<p>
				<label for="decada">Década:</label><br>
				<select name="decada">
				  <option value="1960">1960</option>
				  <option value="1970">1970</option>
				  <option value="1980">1980</option>
				  <option value="1990">1990</option>
				  <option value="2000">2000</option>
				  <option value="2010">2010</option>
				</select>
				</p>


				<input type="hidden" name="new_search" value="1"/>
				<button class="btn2" type="submit">Buscar</button>
			</form>
		</fieldset>

			<?php
			if(isset($_POST['new_search']) == '1') {
				$decada = $_POST['decada'];

				movement_wikidata_call($decada);
			}
			?>

</div><!-- .wrap -->


<?php get_footer() ?>

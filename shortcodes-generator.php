<?php
require_once(__DIR__ . '/shortcodes.php');

/**
 * blah blAH blaH!
 */
Class WP_Doin_Shortcodes_Generator {

	/**
	 * Store the shortcodes created
	 */
	private $shortcodes = array();

	/**
	 * Genrate an array of all shortcodes added
	 * 
	 * @return \WP_Doin_Shortcodes_Generator
	 */
	public function add_shortcode($id, $name, $description) {
		$shortcode = new WP_Doin_Shortcode( $id, $name, $description );
		$this->shortcodes[$id] = $shortcode;
		return $shortcode;
	}

	/**
	 * Hook into the media buttons and admin footer to show up the thicbkox script and add media buttons
	 */
	public function generate() {
		add_action( 'media_buttons', array( $this, 'wp_doin_media_buttons' ) );
		add_action( 'admin_footer', array( $this, 'wp_doin_mce_popup' ) );
	}

	/**
	 * Hook into the media buttons to show up custom media buttons
	 * @hook admin_footer
	 */
	public function wp_doin_media_buttons() {
		?>
		<style>
			.wp-core-ui a.editr_media_link {
				padding-left: 0.4em;
			}

			.label-desc {
				width: 27%;
				margin-right: 3%;
				float: left;
				font-weight: bold;
				text-align: right;
				padding-top: 2px;
			}
			.wp_doin_shortcode .content {
				float: left;
				width: 65%;
			}
			.field-container {
				margin: 5px 0;
				display: inline-block;
				width: 100%;
			}
			.field-container input[type="text"],
			.field-container textarea {
				width: 100%;
			}
			.field-container p {
				margin:0;
			}

			#TB_window {
				width:100% !important;
				top:0 !important;
				margin:0 0 !important;
				left: 0 !important;
			}
			#TB_ajaxContent h3 {
				margin-bottom: 20px;
			}
			#TB_ajaxContent {
				width:100% !important;
			}


			.columns-3 {
				overflow: hidden;
				float: left;
				width: 30%;
				margin-right: 1%;
			}

			.wp_doin_show_dropdown ul {
				display:none;
				position:absolute;
				z-index:99;
				border: 1px solid;
				padding: 10px;
				border-color: #ccc;
				background: #f7f7f7;
				-webkit-box-shadow: inset 0 1px 0 #fff,0 1px 0 rgba(0,0,0,.08);
				box-shadow: inset 0 1px 0 #fff,0 1px 0 rgba(0,0,0,.08);
			}
			.wp_doin_show_dropdown:hover ul {
				display:block;
			}

			.wp_doin_show_dropdown ul li {
				margin-bottom: 0;
				border-bottom: 1px solid #ccc;
			}

			.wp_doin_show_dropdown ul a {
				float:left;
			}

			.wp_doin_show_dropdown ul:hover {
				border-color: #999;
			}

		</style>

		<?php
		// iterate over all of the fields and introduce corresponding buttons
		if ( !empty( $this->shortcodes ) ):
			add_thickbox();
			?>
			<ul>
				<li class="wp_doin_show_dropdown"><a href="#" class="button wp_doin_media_link">WP Doin Shortcodes</a>
					<ul >
						<?php foreach ( $this->shortcodes as $name => $field ):
							?>
							<li><a href = "#TB_inline?width=900&height=1200&inlineId=<?php echo $name; ?>" class = "button thickbox wp_doin_media_link"  title = "<?php echo $field->name; ?>"><?php echo $field->name; ?></a><p><em><?php echo $field->description; ?></em></p></li>
						<?php endforeach; ?>
					</ul>
				</li>
			</ul>
		<?php endif; ?>
		<?php
	}

	/**
	 * Utility to add MCE Popup fired by custom Media Buttons button
	 * 
	 * @hook admin_footer
	 */
	public function wp_doin_mce_popup() {
		?>

		<script>
			var obj = <?php echo json_encode( $this->shortcodes ); ?>;

			function InsertShortcode(name) {
				var atts = '';

				jQuery.each(obj[name]['fields'], function (key, value) {
					// get field type and specify different values for the atts as in textarea, select, checkbox, radio
					if (value['type'] === 'text') {
						var val = jQuery('.wp_doin_shortcode.' + name).find('.field-container.' + key).find('input').attr('value');
						if (val !== '') {
							atts += ' ' + key + '="' + val + '"';
						}
					}

					if (value['type'] === 'textarea') {
						var val = jQuery('.wp_doin_shortcode.' + name).find('.field-container.' + key).find('textarea').val();
						if (val !== '') {
							atts += ' ' + key + '="' + val + '"';
						}
					}

					if (value['type'] === 'checkbox') {
						var val = jQuery('.wp_doin_shortcode.' + name).find('.field-container.' + key).find('input:checked').val();
						if (typeof val !== 'undefined') {
							atts += ' '+ key + '="' + val + '"';
						}
					}

					if (value['type'] === 'select') {
						var val = jQuery('.wp_doin_shortcode.' + name).find('.field-container.' + key).find('select option:selected').val();
						if (val !== '') {
							atts += ' ' + key + '="' + val + '"';
						}
					}

				});
				window.send_to_editor('[' + name + atts + ']');
			}
		</script>

		<?php 
		/**
		 * Iterate over all of the shortcodes created and construct the thickbox triggering buttons
		 */
		foreach ( $this->shortcodes as $name => $shortcode ) : ?>
			<div id="<?php echo $name; ?>" style="display:none;">
				<div class="wrap wp_doin_shortcode <?php echo $name; ?>">
					<div>
						<div style="overflow-y:scroll; height:650px;">
							<h3 style="color:#5A5A5A!important; font-family:Georgia,Times New Roman,Times,serif!important; font-size:1.8em!important; font-weight:normal!important;"><?php echo $shortcode->name; ?></h3>
							<p><?php echo $shortcode->description; ?></p>
							<hr />
							<?php echo $shortcode->generate_fields(); ?>
						</div>

						<hr />
						<div style="padding:40px;">
							<input type="button" class="button-primary" value="<?php echo __('Insert Shortcode', 'acf_rpw'); ?>" onclick="InsertShortcode('<?php echo $name; ?>');"/>&nbsp;&nbsp;&nbsp;
							<a class="button" href="#" onclick="tb_remove();
									return false;">Cancel</a>
						</div>
					</div>
				</div>
			</div>
		<?php endforeach; ?>

		<?php
	}

}

$shortcodes = new WP_Doin_Shortcodes_Generator();

$test = $shortcodes->add_shortcode( 'acfrpw', 'ACFRPW', 'Choose from a list of settings to create the acfrpw shortocde. Any content (shortcodes as well) can be placed in the textareas.' );

$test
		->add_field( 'col', 'fcs', 'start' )
		->add_field( 'text', 'css', __( 'CSS Class', 'acf_rpw' ) )
		->add_field( 'checkbox', 'is', __( 'Ignore sticky posts', 'acf_rpw' ), '', array( 'is' => '' ) )
		->add_field( 'text', 's', __( 'Search Keyword', 'acf_rpw' ), __( 'If specified it will limit posts satisfying the search query.', 'acf_rpw' ) )
		->add_field( 'text', 'ex', __( 'Exclude', 'acf_rpw' ), __( 'Specify comma separated post ids.', 'acf_rpw' ) )
		->add_field( 'checkbox', 'dd', __( 'Display Date', 'acf_rpw' ), '', array( 'dd' => '' ) )
		->add_field( 'checkbox', 'dlm', __( 'Display Modified Date', 'acf_rpw' ), __( 'Checked - displays the last modified date of the post. Settings below apply.', 'acf_rpw' ), array( 'dlm' => '' ) )
		->add_field( 'text', 'df', __( 'Date Format', 'acf_rpw' ), __( 'Specify any custom date format - <a href = "http://codex.wordpress.org/Formatting_Date_and_Time" target = "_blank">reference</a>.', 'acf_rpw' ) )
		->add_field( 'text', 'dr', __( 'Date Relative', 'acf_rpw' ), __( 'Checked - ignores the date format. Displays date in relateive format ex: 2 minutes ago.', 'acf_rpw' ) )
		->add_field( 'text', 'ds', __( 'Date Start', 'acf_rpw' ), __( 'Start date of posts to render. Posts during that day are not included.', 'acf_rpw' ) )
		->add_field( 'text', 'de', __( 'Date End', 'acf_rpw' ), __( 'End date of posts to render. Posts during that day are not included.', 'acf_rpw' ) )
		->add_field( 'text', 'pass', __( 'Password', 'acf_rpw' ), __( 'If not empty, only post with specific password will be shown.', 'acf_rpw' ) )
		->add_field( 'checkbox', 'hp', __( 'Show password protected posts only?', 'acf_rpw' ), '', array( 'hp' => '' ) )
		->add_field( 'checkbox', 'ep', __( 'Exclude password protected posts?', 'acf_rpw' ), __( 'Has lowest priority over the other password fields!', 'acf_rpw' ), array( 'ep' => '' ) )
		->add_field( 'col', 'fce', 'end' )
		->add_field( 'col', 'tcs', 'start' )
		->add_field( 'checkbox', 'pt', __( 'Post Types', 'acf_rpw' ), '', array_combine( get_post_types( array( 'public' => true ), 'names' ), get_post_types( array( 'public' => true ), 'names' ) ) );

// print the post formats checkboxes
if ( current_theme_supports( 'post-formats' ) ):
	$post_formats = get_theme_support( 'post-formats' );
	if ( is_array( $post_formats[0] ) ):
		array_push( $post_formats[0], 'standard' );
		$test->add_field( 'checkbox', 'pf', __( 'Post Formats', 'acf_rpw' ), __( 'Displays specific or multiple post formats', 'acf_rpw' ), array_combine( $post_formats[0], $post_formats[0] ) );
	endif;
endif;

$test->add_field( 'text', 'aut', __( 'Authors', 'acf_rpw' ), __( 'Comma separated list of author ids. Ex. 1, 2, 3, 4', 'acf_rpw' ) )
		->add_field( 'select', 'ord', __( 'Order', 'acf_rpw' ), '', array( 'ASC' => __( 'Ascending', 'acf_rpw' ), 'DESC' => __( 'Descending', 'acf_rpw' ) ) )
		->add_field( 'select', 'orderby', __( 'Orderby', 'acf_rpw' ), __( 'If meta order is specified the next field cannot be empty.', 'acf_rpw' ), array(
			'ID' => __( 'ID', 'acf_rpw' ),
			'author' => __( 'Author', 'acf_rpw' ),
			'title' => __( 'Title', 'acf_rpw' ),
			'date' => __( 'Date', 'acf_rpw' ),
			'modified' => __( 'Modified', 'acf_rpw' ),
			'rand' => __( 'Random', 'acf_rpw' ),
			'comment_count' => __( 'Comment Count', 'acf_rpw' ),
			'menu_order' => __( 'Menu Order', 'acf_rpw' ),
			'meta_value' => __( 'Meta Value', 'acf_rpw' ),
			'meta_value_num' => __( 'Meta Value Numeric', 'acf_rpw' ) ) )
		->add_field( 'text', 'mk', __( 'Meta Key', 'acf_rpw' ), __( 'Fetch only posts having the Meta Key. Required if Meta Value or Meta Value Numeric was selected above.', 'acf_rpw' ) )
		->add_field( 'select', 'meta_compare', __( 'Meta compare', 'acf_rpw' ), __( 'Specify the meta compare format, see CODEX and plugin documentation for further reference.', 'acf_rpw' ), array(
			'' => __( 'None', 'acf_rpw' ),
			' = ' => __( ' = ', 'acf_rpw' ),
			'!=' => __( '!=', 'acf_rpw' ),
			'>' => __( '>', 'acf_rpw' ),
			'>=' => __( '>=', 'acf_rpw' ),
			'<' => __( '<', 'acf_rpw' ),
			'<=' => __( '<=', 'acf_rpw' ),
			'LIKE' => __( 'LIKE', 'acf_rpw' ),
			'IN' => __( 'IN', 'acf_rpw' ),
			'NOT IN' => __( 'NOT IN', 'acf_rpw' ),
			'BETWEEN' => __( 'BETWEEN', 'acf_rpw' ),
			'NOT BETWEEN' => __( 'NOT BETWEEN', 'acf_rpw' ),
			'EXISTS' => __( 'EXISTS', 'acf_rpw' ),
			'NOT EXISTS' => __( 'NOT EXISTS', 'acf_rpw' ),
			'REGEXP' => __( 'REGEXP', 'acf_rpw' ),
			'NOT REGEXP' => __( 'NOT REGEXP', 'acf_rpw' ),
			'RLIKE' => __( 'RLIKE', 'acf_rpw' ),
		) )
		->add_field( 'text', 'meta_value', __( 'Meta Value', 'acf_rpw' ), __( 'Specify the Meta Value to compare the key with. Leave empty for none.', 'acf_rpw' ) )
		->add_field( 'col', 'tce', 'end' )
		->add_field( 'col', 'thcs', 'start' );
// obtain the categories list

$categories = array();
if ( !is_wp_error( get_terms( 'category' ) ) ) {
	foreach ( get_terms( 'category' ) as $cat ) {
		$categories[$cat->term_id] = $cat->name;
	}
	$test->add_field( 'checkbox', 'ltc', __( 'Limit to Category', 'acf_rpw' ), '', $categories );
}
// obtain the categories list
$tags = array();

$tags_get = get_terms( 'post_tag' );

$tags = array();

if ( !is_wp_error( $tags_get ) ) {
	foreach ( $tags as $tag ) {
		$tags[$tag->term_id] = $tag->name;
	}

	$test->add_field( 'lttag', __( 'Limit to Tag', 'acf_rpw' ), '', $tags );
}
$test->add_field( 'text', 'ltt', __( 'Limit to taxonomy', 'acf_rpw' ), __( 'Ex: category=1,2,4&amp;post-tag=6,12.', 'acf_rpw' ) )
		->add_field( 'select', 'ltto', __( 'Operator', 'acf_rpw' ), __( '"IN" includes posts from the taxonomies, NOT IN excludes posts from these taxonomies.', 'acf_rpw' ), array( 'IN' => __( 'IN', 'acf_rpw' ), 'NOT IN' => __( 'NOT IN', 'acf_rpw' ) ) )
		->add_field( 'text', 'np', __( 'Number of posts to show', 'acf_rpw' ), __( 'Use -1 to list all posts.', 'acf_rpw' ) )
		->add_field( 'text', 'ns', __( 'Number of posts to skip', 'acf_rpw' ), __( 'Ignored if -1 is specified above.', 'acf_rpw' ) );

// thumbnail related settings
if ( current_theme_supports( 'post-thumbnails' ) ) {
	$test->add_field( 'checkbox', 'dth', __( 'Display Thumbnail', 'acf_rpw' ), array( 'display' => __( 'Display', 'acf_rpw' ) ) )
			->add_field( 'text', 'thh', __( 'Thumbnail Height', 'acf_rpw' ) )
			->add_field( 'text', 'thw', __( 'Thumbnail Width', 'acf_rpw' ) )
			->add_field( 'select', 'tha', __( 'Thumbnail Alignment', 'acf_rpw' ), '', array(
				'acf-rpw-left' => __( 'Left', 'acf_rpw' ),
				'acf-rpw-right' => __( 'Right', 'acf_rpw' ),
				'acf-rpw-middle' => __( 'Middle', 'acf_rpw' )
			) );
}

$test->add_field( 'text', 'dfth', __( 'Default Thumbnail', 'acf_rpw' ), __( 'Specify full, valid image URL here. Ex: http://placehold.it/50x50/f0f0f0/ccc. All of the above apply to thumbnails but not to ACF image field type. Use CSS "acf-img" class to reference these.', 'acf_rpw' ) )
		->add_field( 'checkbox', 'excerpt', __( 'Show excerpt', 'acf_rpw' ), '', array( 'ignore' => __( 'Ignore', 'acf_rpw' ) ) )
		->add_field( 'text', 'el', __( 'Excerpt Length', 'acf_rpw' ), __( 'Limits the excerpt to specified number of words.', 'acf_rpw' ) )
		->add_field( 'checkbox', 'rm', __( 'Display Readmore', 'acf_rpw' )  )
		->add_field( 'text', 'rt', __( 'Readmore text', 'acf_rpw' ), __( 'Leave empty for default "... Continue Reading" text. If full excerpt is printed, this text will not appear.', 'acf_rpw' ) )
		->add_field( 'col', 'thce', 'end' );


$shortcodes->generate();

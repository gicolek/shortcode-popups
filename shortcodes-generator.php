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

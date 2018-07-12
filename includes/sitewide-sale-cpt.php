<?php
/**
 * Functions for creating/maintaining Sale CPT
 *
 * @package pmpro-sitewide-sale/includes
 */

/**
 * Creates a Sitewide Sale CPT
 */
function pmpro_sws_create_sitewide_sale_cpt() {
	register_post_type( 'pmpro_sitewide_sale',
		array(
			'labels'      => array(
				'name'               => __( 'Sitewide Sales', 'pmpro_sitewide_sale' ),
				'singular_name'      => __( 'Sitewide Sale', 'pmpro_sitewide_sale' ),
				'add_new_item'       => __('Add New Sitewide Sale', 'pmpro_sitewide_sale'),
				'edit_item'          => __( 'Edit Sitewide Sale', 'pmpro_sitewide_sale' ),
				'new_item'           => __( 'New Sitewide Sale', 'pmpro_sitewide_sale' ),
				'view_item'          => __( 'View Sitewide Sale', 'pmpro_sitewide_sale' ),
				'search_items'       => __( 'Search Sitewide Sales', 'pmpro_sitewide_sale' ),
				'not_found'          => __( 'No Sitewide Sale Found', 'pmpro_sitewide_sale' ),
				'not_found_in_trash' => __( 'No Sitewide Sales Found In Trash', 'pmpro_sitewide_sale' ),
				'all_items'          => __( 'All Sitewide Sales', 'pmpro_sitewide_sale' ),
			),
			'public'               => true,
			'has_archive'          => true,
			'menu_icon'            => 'dashicons-megaphone',
			'register_meta_box_cb' => 'pmpro_sws_cpt_meta_box_callback',
		)
	);
	remove_post_type_support( 'pmpro_sitewide_sale', 'editor' );
	remove_post_type_support( 'pmpro_sitewide_sale', 'slug' );
}
add_action('init','pmpro_sws_create_sitewide_sale_cpt');

function pmpro_sws_cpt_meta_box_callback( $post ) {
	add_meta_box( 'pmpro_sws_cpt_set_as_sitewide_sale',
        __( 'Sitewide Sale', 'pmpro_sitewide_sale' ),
        'pmpro_sws_cpt_display_set_as_sitewide_sale',
        'pmpro_sitewide_sale', 'side', 'high'
  );
	add_meta_box( 'pmpro_sws_cpt_step_1',
        __( 'Step 1: Choose Discount Code to Associate With Sale', 'pmpro_sitewide_sale' ),
        'pmpro_sws_cpt_display_step_1',
        'pmpro_sitewide_sale', 'normal', 'high'
  );
	add_meta_box( 'pmpro_sws_cpt_step_2',
        __( 'Step 2: Create Landing Page', 'pmpro_sitewide_sale' ),
        'pmpro_sws_cpt_display_step_2',
        'pmpro_sitewide_sale', 'normal', 'high'
  );
	add_meta_box( 'pmpro_sws_cpt_step_3',
        __( 'Step 3: Steup Banners', 'pmpro_sitewide_sale' ),
        'pmpro_sws_cpt_display_step_3',
        'pmpro_sitewide_sale', 'normal', 'high'
  );
	add_meta_box( 'pmpro_sws_cpt_step_4',
        __( 'Step 4: Monitor Your Sale', 'pmpro_sitewide_sale' ),
        'pmpro_sws_cpt_display_step_4',
        'pmpro_sitewide_sale', 'normal', 'high'
  );
}

function pmpro_sws_cpt_display_set_as_sitewide_sale( $post ) {
	$init_checked = false;
	if ( isset( $_REQUEST['set_sitewide_sale'] ) && 'true' === $_REQUEST['set_sitewide_sale'] ) {
		$init_checked = true;
	} else {
		$options = pmprosws_get_options();
		if ( $post->ID . '' === $options['active_sitewide_sale_id'] ) {
			$init_checked = true;
		}
	}
	echo '<table class="form-table"><tr>
	<th scope="row" valign="top"><label>' . esc_html( 'Set as Current Sitewide Sale', 'pmpro-sitewide-sale' ) . ':</label></th>
	<td><input name="pmpro_sws_set_as_sitewide_sale" type="checkbox" ' . ( $init_checked ? 'checked' : '' ) . ' /></td>
	</tr></table>';
}

function pmpro_sws_cpt_display_step_1($post) {
	global $wpdb;
	$codes            = $wpdb->get_results( "SELECT * FROM $wpdb->pmpro_discount_codes", OBJECT );
	$current_discount = esc_html( get_post_meta( $post->ID, 'discount_code_id', true ) );
	if ( empty( $current_discount ) ) {
		$current_discount = false;
	}
	?>
	<select class="discount_code_select pmpro_sws_option" id="pmpro_sws_discount_code_select" name="pmpro_sws_discount_code_id">
	<option value=-1></option>
	<?php
	foreach ( $codes as $code ) {
		$selected_modifier = '';
		if ( $code->id === $current_discount ) {
			$selected_modifier = ' selected="selected"';
		}
		echo '<option value = ' . esc_html( $code->id ) . esc_html( $selected_modifier ) . '>' . esc_html( $code->code, 'pmpro-sitewide-sale' ) . '</option>';
	}
	echo '</select> ' . esc_html( 'or', 'pmpro_sitewide_sale' ) . ' <a href="' . esc_html( get_admin_url() ) .
	'admin.php?page=pmpro-discountcodes&edit=-1&set_sitewide_sale=true">' . esc_html( 'create a new discount code, doesn\'t update', 'pmpro_sitewide_sale' ) . '</a>';
		?>
	<script>
		jQuery( document ).ready(function() {
			jQuery("#pmpro_sws_discount_code_select").selectWoo();
		});
	</script>
	<?php
}

function pmpro_sws_cpt_display_step_2( $post ) {
	global $wpdb;
	$pages        = get_pages();
	$current_page = esc_html( get_post_meta( $post->ID, 'landing_page_post_id', true ) );
	if ( empty( $current_page ) ) {
		$current_page = false;
	}

	?>
	<select class="landing_page_select pmpro_sws_option" id="pmpro_sws_landing_page_select" name="pmpro_sws_landing_page_post_id">
	<option value=-1></option>
	<?php
	foreach ( $pages as $page ) {
		$selected_modifier = '';
		if ( $page->ID . '' === $current_page ) {
			$selected_modifier = ' selected="selected"';
		}
		echo '<option value=' . esc_html( $page->ID ) . esc_html( $selected_modifier ) . '>' . esc_html( $page->post_title ) . '</option>';
	}
	echo '</select> ' . esc_html( 'or', 'pmpro_sitewide_sale' ) . ' <a href="' . esc_html( get_admin_url() ) . 'post-new.php?post_type=page&set_sitewide_sale=true&sws_default=true">
			 ' . esc_html( 'create a new page, doesn\'t work yet', 'pmpro_sitewide_sale' ) . '</a>.';
	?>
	<script>
		jQuery( document ).ready(function() {
			jQuery("#pmpro_sws_landing_page_select").selectWoo();
		});
	</script>
	<?php
}

function pmpro_sws_cpt_display_step_3( $post ) {
	$use_banner = esc_html( get_post_meta( $post->ID, 'use_banner', true ) );
	if ( empty( $use_banner ) ) {
		$use_banner = 'no';
	}
	$banner_title = esc_html( get_post_meta( $post->ID, 'banner_title', true ) );
	if ( empty( $banner_title ) ) {
		$banner_title = '';
	}
	$banner_description = esc_html( get_post_meta( $post->ID, 'banner_description', true ) );
	if ( empty( $banner_description ) ) {
		$banner_description = '';
	}
	$link_text = esc_html( get_post_meta( $post->ID, 'link_text', true ) );
	if ( empty( $link_text ) ) {
		$link_text = '';
	}
	$css_option = esc_html( get_post_meta( $post->ID, 'css_option', true ) );
	if ( empty( $css_option ) ) {
		$css_option = '';
	}
	$hide_for_levels = get_post_meta( $post->ID, 'hide_for_levels', true );
	if ( empty( $hide_for_levels ) ) {
		$hide_for_levels = [];
	}
	$hide_on_checkout = esc_html( get_post_meta( $post->ID, 'hide_on_checkout', true ) );
	if ( empty( $hide_on_checkout ) ) {
		$hide_on_checkout = false;
	}
	?>
	</br>
		<table class="form-table"><tr>
			<th scope="row" valign="top"><label><?php esc_html_e( 'Use the built-in banner?', 'pmpro-sitewide-sale' ); ?></label></th>
			<td><select class="use_banner_select pmpro_sws_option" id="pmpro_sws_use_banner_select" name="pmpro_sws_use_banner">
				<option value="no" <?php selected( $use_banner, 'no'); ?>><?php esc_html_e( 'No', 'pmpro-sitewide-sale' ); ?></option>
				<option value="top" <?php selected( $use_banner, 'top' );?>><?php esc_html_e( 'Yes. Top of Site.', 'pmpro-sitewide-sale' ); ?></option>
				<option value="bottom" <?php selected( $use_banner, 'bottom' );?>><?php esc_html_e( 'Yes. Bottom of Site.', 'pmpro-sitewide-sale' ); ?></option>
				<option value="bottom-right" <?php selected( $use_banner, 'bottom-right' );?>><?php esc_html_e( 'Yes. Bottom Right of Site.', 'pmpro-sitewide-sale' ); ?></option>
			</select></td>
		</tr></table>
		<table class="form-table" id="pmpro_sws_banner_options">
	<?php
	echo '
	<tr>
		<th scope="row" valign="top"><label>' . __( 'Banner Title', 'pmpro-sitewide-sale' ) . '</label></th>
		<td><input class="pmpro_sws_option" type="text" name="pmpro_sws_banner_title" value="' . esc_html( $banner_title ) . '"/></td>
	</tr>';
	echo '
	<tr>
		<th scope="row" valign="top"><label>' . __( 'Banner Description', 'pmpro-sitewide-sale' ) . '</label></th>
		<td><textarea rows="5" cols="20" class="pmpro_sws_option" name="pmpro_sws_banner_description">' . esc_textarea( $banner_description ) . '</textarea></td>
	</tr>';
	echo '
	<tr>
		<th scope="row" valign="top"><label>' . __( 'Button Text', 'pmpro-sitewide-sale' ) . '</label></th>
		<td><input class="pmpro_sws_option" type="text" name="pmpro_sws_link_text" value="' . esc_html( $link_text ) . '"/></td>
	</tr>';

	echo '
	<tr>
		<th scope="row" valign="top"><label>' . esc_html( 'Custom Banner CSS', 'pmpro-sitewide-sale' ) . '</label></th>
		<td><textarea class="pmpro_sws_option" name="pmpro_sws_css_option">' . esc_html( $css_option ) . '</textarea></td>
	</tr>';
	echo '
		<tr>
			<th scope="row" valign="top"><label>' . esc_html( 'Hide Banner by Membership Level', 'pmpro-sitewide-sale' ) . '</label></th>
			<td><select class="pmpro_sws_option" id="pmpro_sws_hide_levels_select" name="pmpro_sws_hide_for_levels[]" style="width:12em" multiple/>';
	$all_levels    = pmpro_getAllLevels( true, true );
	$hidden_levels = $hide_for_levels;
	foreach ( $all_levels as $level ) {
		$selected_modifier = in_array( $level->id, $hidden_levels, true ) ? ' selected' : '';
		echo '<option value=' . esc_html( $level->id ) . esc_html( $selected_modifier ) . '>' . esc_html( $level->name ) . '</option>';
	}
	$checked_modifier = $hide_on_checkout ? ' checked' : '';
	echo '</td></tr>
		<tr>
			<th scope="row" valign="top"><label>' . esc_html( 'Hide Banner at Checkout', 'pmpro-sitewide-sale' ) . '</label></th>
			<td><input class="pmpro_sws_option" type="checkbox" name="pmpro_sws_hide_on_checkout" ' . esc_html( $checked_modifier ) . '/></td>
		</tr></table>';
		?>
		<script>
			jQuery( document ).ready(function() {
				jQuery("#pmpro_sws_use_banner_select").selectWoo();
				jQuery("#pmpro_sws_hide_levels_select").selectWoo();
			});
		</script>
		<?php
}

function pmpro_sws_cpt_display_step_4( $post ) {
	?>
	<a href="<?php echo admin_url('admin.php?page=pmpro-reports&report=pmpro_sws_reports');?>" target="_blank"><?php _e( 'Click here to view Sitewide Sale reports, need direct link.', 'pmpro-sitewide-sale' ); ?></a>
	<?php
}

function pmpro_sws_save_cpt( $post_id, $post ) {
	if ( 'pmpro_sitewide_sale' !== $post->post_type ) {
		return;
	}

	if ( isset( $_POST['pmpro_sws_discount_code_id'] ) ) {
		update_post_meta( $post_id, 'discount_code_id', trim( $_POST['pmpro_sws_discount_code_id'] ) );
	} else {
		update_post_meta( $post_id, 'discount_code_id', false );
	}

	if ( isset( $_POST['pmpro_sws_landing_page_post_id'] ) ) {
		update_post_meta( $post_id, 'landing_page_post_id', trim( $_POST['pmpro_sws_landing_page_post_id'] ) );
	} else {
		update_post_meta( $post_id, 'landing_page_post_id', false );
	}

	$possible_options = [ 'no', 'top', 'bottom', 'bottom-right' ];
	if ( isset( $_POST['pmpro_sws_use_banner'] ) && in_array( trim( $_POST['pmpro_sws_use_banner'] ), $possible_options, true ) ) {
		update_post_meta( $post_id, 'use_banner', trim( $_POST['pmpro_sws_use_banner'] ) );
	} else {
		update_post_meta( $post_id, 'use_banner', 'no' );
	}

	if ( isset( $_POST['pmpro_sws_banner_title'] ) ) {
		update_post_meta( $post_id, 'banner_title', trim( $_POST['pmpro_sws_banner_title'] ) );
	} else {
		update_post_meta( $post_id, 'banner_title', '' );
	}

	if ( isset( $_POST['pmpro_sws_banner_description'] ) ) {
		update_post_meta( $post_id, 'banner_description', trim( $_POST['pmpro_sws_banner_description'] ) );
	} else {
		update_post_meta( $post_id, 'banner_description', '' );
	}

	if ( isset( $_POST['pmpro_sws_link_text'] ) ) {
		update_post_meta( $post_id, 'link_text', trim( $_POST['pmpro_sws_link_text'] ) );
	} else {
		update_post_meta( $post_id, 'link_text', '' );
	}

	if ( isset( $_POST['pmpro_sws_css_option'] ) ) {
		update_post_meta( $post_id, 'css_option', trim( $_POST['pmpro_sws_css_option'] ) );
	} else {
		update_post_meta( $post_id, 'css_option', '' );
	}

	if ( isset( $_POST['pmpro_sws_hide_for_levels'] ) && is_array( $_POST['pmpro_sws_hide_for_levels'] ) ) {
		update_post_meta( $post_id, 'hide_for_levels', $_POST['pmpro_sws_hide_for_levels'] );
	} else {
		update_post_meta( $post_id, 'hide_for_levels', [] );
	}

	if ( isset( $_POST['pmpro_sws_hide_on_checkout'] ) ) {
		update_post_meta( $post_id, 'hide_on_checkout', true );
	} else {
		update_post_meta( $post_id, 'hide_on_checkout', false );
	}

	$options = pmprosws_get_options();
	if ( isset( $_POST['pmpro_sws_set_as_sitewide_sale'] ) ) {
		$options['active_sitewide_sale_id'] = $post_id;
	} elseif ( $options['active_sitewide_sale_id'] === $post_id . '' ) {
		$options['active_sitewide_sale_id'] = false;
	}
	pmprosws_save_options( $options );
}
add_action( 'save_post', 'pmpro_sws_save_cpt', 10, 2 );

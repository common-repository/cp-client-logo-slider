<?php
if (!defined('ABSPATH')) die ('No direct access allowed');

//function add menu in admin
function ccls_add_to_menu() 
{
	add_menu_page(" ", " ", "administrator", 'logo-client-carousel', 'ccls_render_settings_page', '' );
	add_submenu_page( 'edit.php?post_type=ccls-client-slider', 'Settings', 'Settings', 'manage_options', 'logo-client-carousel', '' );
}

if (is_admin()) 
{
	add_action('admin_menu', 'ccls_add_to_menu');
} 


function ccls_render_settings_page() {
?>
<div class="wrap">
<div id="icon-options-general" class="icon32"></div>
<h2>Client Logo Carousel Settings</h2>
	<?php settings_errors(); ?>
	<div class="clearfix paddingtop20">
		<div class="first ninecol">
			<form method="post" action="options.php">
				<?php settings_fields( 'ccls_settings' ); ?>
				<?php do_settings_sections( "ccls_settings" ); ?>
				<?php submit_button(); ?>
			</form>
		</div>
		
	</div>
</div>
<?php }

//function to create option
function ccls_create_options() { 
	
	add_settings_section( 'ccls_slider_section', null, null, 'ccls_settings' );

    add_settings_field(
        'items', 'Item Count', 'ccls_render_settings_field', 'ccls_settings', 'ccls_slider_section',
		array(
			'desc' => 'Number of items to show on carousel',
			'id' => 'items',
			'type' => 'text',
			'group' => 'ccls_slider_settings'
		)
    );
    add_settings_field(
        'single_item', 'Show Single Item', 'ccls_render_settings_field', 'ccls_settings', 'ccls_slider_section',
		array(
			'desc' => 'If checked only a single item will be displayed with slide',
			'id' => 'single_item',
			'type' => 'checkbox',
			'group' => 'ccls_slider_settings'
		)
    );
	
	add_settings_field(
        'slide_orderby', 'Slide Images Order by', 'ccls_render_settings_field', 'ccls_settings', 'ccls_slider_section',
		array(
			'desc' => 'Here we can choose different order by for slide images.',
			'id' => 'slide_orderby',
			'type' => 'select',
			'group' => 'ccls_slider_settings',
			'options' => array('date'=>'Order by Date DESC','title'=>'Order by Title','rand'=>'Random Order')
		)
    );
	
    add_settings_field(
        'slide_speed', 'Slider Speed', 'ccls_render_settings_field', 'ccls_settings', 'ccls_slider_section',
		array(
			'desc' => 'Animation speed of the slider in milliseconds',
			'id' => 'slide_speed',
			'type' => 'text',
			'group' => 'ccls_slider_settings'
		)
    );
    add_settings_field(
        'pagination_speed', 'Pagination Speed', 'ccls_render_settings_field', 'ccls_settings', 'ccls_slider_section',
		array(
			'desc' => 'Pagination speed of the slider in milliseconds',
			'id' => 'pagination_speed',
			'type' => 'text',
			'group' => 'ccls_slider_settings'
		)
    );
    add_settings_field(
        'rewind_speed', 'Rewind Speed', 'ccls_render_settings_field', 'ccls_settings', 'ccls_slider_section',
		array(
			'desc' => 'Rewind speed of the slider in milliseconds',
			'id' => 'rewind_speed',
			'type' => 'text',
			'group' => 'ccls_slider_settings'
		)
    );
	add_settings_field(
        'auto_play', 'Auto Play Slider', 'ccls_render_settings_field', 'ccls_settings', 'ccls_slider_section',
		array(
			'desc' => 'If checked the slider will slide automatically',
			'id' => 'auto_play',
			'type' => 'checkbox',
			'group' => 'ccls_slider_settings'
		)
    );
	add_settings_field(
        'stop_on_hover', 'Stop on hover', 'ccls_render_settings_field', 'ccls_settings', 'ccls_slider_section',
		array(
			'desc' => 'If checked the animation will stop on hover',
			'id' => 'stop_on_hover',
			'type' => 'checkbox',
			'group' => 'ccls_slider_settings'
		)
    );
	add_settings_field(
        'navigation', 'Display Navigation', 'ccls_render_settings_field', 'ccls_settings', 'ccls_slider_section',
		array(
			'desc' => 'If checked, next and previous links will be displayed',
			'id' => 'navigation',
			'type' => 'checkbox',
			'group' => 'ccls_slider_settings'
		)
    );
	add_settings_field(
        'pagination', 'Display Pagination', 'ccls_render_settings_field', 'ccls_settings', 'ccls_slider_section',
		array(
			'desc' => 'If checked the slider will be paginated',
			'id' => 'pagination',
			'type' => 'checkbox',
			'group' => 'ccls_slider_settings'
		)
    );
	
	add_settings_field(
        'responsive', 'Responsive', 'ccls_render_settings_field', 'ccls_settings', 'ccls_slider_section',
		array(
			'desc' => 'If checked the slider will automatically.',
			'id' => 'responsive',
			'type' => 'checkbox',
			'group' => 'ccls_slider_settings'
		)
    );
	
    //register the fields with WordPress 
	register_setting('ccls_settings', 'ccls_slider_settings', 'ccls_settings_validation');
	
}
add_action('admin_init', 'ccls_create_options');

//Fuction to settings validation
function ccls_settings_validation($input){
	$input['single_item'] 		= (bool)$input['single_item'];
	$input['auto_play'] 		= (bool)$input['auto_play'];
	$input['stop_on_hover'] 	= (bool)$input['stop_on_hover'];
	$input['slide_orderby'] 	= trim($input['slide_orderby']);
	$input['navigation'] 		= (bool)$input['navigation'];
	$input['pagination'] 		= (bool)$input['pagination'];
	$input['responsive'] 		= (bool)$input['responsive'];
	$input['items'] 			= (trim($input['items']) && is_numeric($input['items']))?((int)$input['items']):6;
	$input['slide_speed'] 		= (trim($input['slide_speed']) && is_numeric($input['slide_speed']))?((int)$input['slide_speed']):500;
	$input['pagination_speed'] 	= (trim($input['pagination_speed']) && is_numeric($input['pagination_speed']))?((int)$input['pagination_speed']):500;
	$input['rewind_speed'] 		= (trim($input['rewind_speed']) && is_numeric($input['rewind_speed']))?((int)$input['rewind_speed']):500;
	return $input;
}

//Fuction to render settings field
function ccls_render_settings_field($args){
	$option_value = get_option($args['group']);
?>
	<?php if($args['type'] == 'text'): ?>
		<input type="text" id="<?php echo $args['id'] ?>" name="<?php echo $args['group'].'['.$args['id'].']'; ?>" value="<?php echo (isset($option_value[$args['id']]))?esc_attr($option_value[$args['id']]):''; ?>">
	<?php elseif ($args['type'] == 'select'): ?>
		<select name="<?php echo $args['group'].'['.$args['id'].']'; ?>" id="<?php echo $args['id']; ?>">
			<?php foreach ($args['options'] as $key=>$option) { ?>
				<option <?php if(isset($option_value[$args['id']])) selected($option_value[$args['id']], $key); echo 'value="'.$key.'"'; ?>><?php echo $option; ?></option><?php } ?>
		</select>
	<?php elseif($args['type'] == 'checkbox'): ?>
		<input type="hidden" name="<?php echo $args['group'].'['.$args['id'].']'; ?>" value="0" />
		<input type="checkbox" name="<?php echo $args['group'].'['.$args['id'].']'; ?>" id="<?php echo $args['id']; ?>" value="1" <?php if(isset($option_value[$args['id']])) checked($option_value[$args['id']], true); ?> />
	<?php elseif($args['type'] == 'textarea'): ?>
		<textarea name="<?php echo $args['group'].'['.$args['id'].']'; ?>" type="<?php echo $args['type']; ?>" cols="" rows=""><?php echo isset($option_value[$args['id']])?stripslashes(esc_textarea($option_value[$args['id']]) ):''; ?></textarea>
	<?php endif; ?>
		<p class="description"><?php echo $args['desc'] ?></p>
<?php
}

?>
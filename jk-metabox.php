<?php

/**
 * Plugin Name: JK Metabox
 * Plugin URI: https://www.github.com/fayekalvi/
 * Description: Metabox API Test plugin.
 * Version: 1.0.0
 * Author: Fayek Alvi Rahman Jaki
 * Author URI: https://www.github.com/fayekalvi/
 * Text Domain: jkmetabox
 * Domain Path: /languages/
 * License: GNU General Public License v2.0 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires at least: 5.2
 * Tested up to: 6.0
 *
 */

class JKMetabox
{
    public function __construct()
    {
        add_action('plugins_loaded', array($this, 'jkm_load_textdomain'));

        add_action('admin_menu', array($this, 'jkm_add_metabox'));
        add_action(
            'save_post',
            array($this, 'jkm_save_metabox')
        );
        add_action('save_post', array($this, 'jkm_save_image'));
        add_action('save_post', array($this, 'jkm_save_gallery'));

        add_action('admin_enqueue_scripts', array($this, 'jkm_enqueue_scripts'));
    }

    public function jkm_load_textdomain()
    {
        load_plugin_textdomain('jkMetabox', false, dirname(__FILE__) . "/languages");
    }
    public function jkm_enqueue_scripts()
    {
        wp_enqueue_style(
            'jkm-admin-style',
            plugin_dir_url(__FILE__) . "assets/admin/css/style.css",
            null,
            time()
        );
        wp_enqueue_style(
            'jkm-datepicker',
            plugin_dir_url(__FILE__) . "assets/admin/css/datepicker.css",
            null,
            time()
        );
        wp_enqueue_style(
            'jquery-ui-css',
            '//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css',
            null,
            time()
        );
        wp_enqueue_style('font-awesome-css', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css', null, '6.2.0');
        wp_enqueue_script('jkm-admin-js', plugin_dir_url(__FILE__) . "assets/admin/js/main.js", array('jquery', 'jquery-ui-datepicker'), time(), true);
    }
    function jkm_add_metabox()
    {
        add_meta_box(
            'jkm_post_location',
            __('Location Info', 'jkmetabox'),
            array($this, 'jkm_display_metabox'),
            array('post', 'page'),
        );
        add_meta_box(
            'jkm_book_info',
            __('Book Info', 'jkmetabox'),
            array($this, 'jkm_book_info'),
            array('books'),
        );
        add_meta_box(
            'jkm_image_info',
            __('Image Info', 'jkmetabox'),
            array($this, 'jkm_image_info'),
            array('post'),
        );
        add_meta_box(
            'jkm_gallery_info',
            __('Gallery Info', 'jkmetabox'),
            array($this, 'jkm_gallery_info'),
            array('post'),
        );
    }
    private function is_secured($nonce_field, $action, $post_id)
    {
        $nonce = isset($_POST[$nonce_field]) ? $_POST[$nonce_field] : '';

        if ($nonce == '') {
            return false;
        }
        if (!wp_verify_nonce($nonce, $action)) {
            return false;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return false;
        }

        if (wp_is_post_autosave($post_id)) {
            return false;
        }

        if (wp_is_post_revision($post_id)) {
            return false;
        }

        return true;
    }
    function jkm_save_metabox($post_id)
    {
        if (!$this->is_secured('jkm_location_field', 'jkm_location', $post_id)) {
            return $post_id;
        }
        $location =
            isset($_POST['jkm_location']) ? $_POST['jkm_location'] : "";
        $is_favourite = isset($_POST['jkm_is_favourite']) ? $_POST['jkm_is_favourite'] : 0;
        $colors     = isset($_POST['jkm_color']) ? $_POST['jkm_color'] : '';


        if ($location == '') {
            return $post_id;
        }
        $location = sanitize_text_field($location);
        update_post_meta(
            $post_id,
            'jkm_location',
            $location
        );
        update_post_meta($post_id, 'jkm_is_favourite', $is_favourite);
        update_post_meta($post_id, 'jkm_color', $colors);
    }


    function jkm_save_image($post_id)
    {
        if (!$this->is_secured('jkm_image_nonce', 'jkm_image', $post_id)) {
            return $post_id;
        }
        $image_id =
            isset($_POST['jkm_image_id']) ? $_POST['jkm_image_id'] : "";
        $image_url = isset($_POST['jkm_image_url']) ? $_POST['jkm_image_url'] : "";

        update_post_meta($post_id, 'jkm_image_id', $image_id);
        update_post_meta($post_id, 'jkm_image_url', $image_url);
    }
    function jkm_save_gallery($post_id)
    {
        if (!$this->is_secured('jkm_gallery_nonce', 'jkm_gallery', $post_id)) {
            return $post_id;
        }
        $image_id =
            isset($_POST['jkm_images_id']) ? $_POST['jkm_images_id'] : "";
        $image_url = isset($_POST['jkm_images_url']) ? $_POST['jkm_images_url'] : "";

        update_post_meta($post_id, 'jkm_images_id', $image_id);
        update_post_meta($post_id, 'jkm_images_url', $image_url);
    }


    function jkm_book_info($post)
    {
        wp_nonce_field('jkm_book', 'jkm_book_nonce');

        $metabox_html = <<<EOD
<div class="fields">
	<div class="field_c">
		<div class="label_c">
			<label for="book_author">Book Author</label>
		</div>
		<div class="input_c">
			<input type="text" class="widefat" id="book_author">
		</div>
		<div class="float_c"></div>
	</div>
	
	<div class="field_c">
		<div class="label_c">
			<label for="book_isbn">Book ISBN</label>
		</div>
		<div class="input_c">
			<input type="text" id="book_isbn">
		</div>
		<div class="float_c"></div>
	</div>
	
	<div class="field_c">
		<div class="label_c">
			<label for="book_year">Publish Year </label>
		</div>
		<div class="input_c">
            <i class="fa-sharp fa-solid fa-calendar-days icon"></i>
			<input type="text" class="jkm_dp input-field" id="book_year" >
		</div>
		<div class="float_c"></div>
	</div>
	
</div>
EOD;
        echo $metabox_html;
    }

    function jkm_image_info($post)
    {
        $image_id = esc_attr(get_post_meta($post->ID, 'jkm_image_id', true));
        $image_url = esc_attr(get_post_meta($post->ID, 'jkm_image_url', true));
        wp_nonce_field('jkm_image', 'jkm_image_nonce');
        $button_label = __('Upload Image', 'jkmetabox');

        $metabox_html = <<<EOD
<div class="fields">
	<div class="field_c">
		<div class="label_c">
			<label>Image</label>
		</div>
		<div class="input_c">
			<button class="button" id="upload_image">{$button_label}</button>
			
            <input type="hidden" name="jkm_image_id" id="jkm_image_id" value ="{$image_id}" />
            <input type="hidden" name="jkm_image_url" id="jkm_image_url" value="{$image_url}" />
            <div id="image-container"> </div>
		</div>
		<div class="float_c"></div>
	</div>
</div>
EOD;
        echo $metabox_html;
    }

    function jkm_gallery_info($post)
    {
        $image_id = esc_attr(get_post_meta($post->ID, 'jkm_images_id', true));
        $image_url = esc_attr(get_post_meta($post->ID, 'jkm_images_url', true));
        wp_nonce_field('jkm_gallery', 'jkm_gallery_nonce');

        $label = __("Images", 'jkmetabox');
        $button_label = __('Insert Gallery', 'jkmetabox');
        $metabox_html = <<<EOD
<div class="fields">
	<div class="field_c">
		<div class="label_c">
			<label>{$label}</label>
		</div>
		<div class="input_c">
			<button class="button" id="upload_images">{$button_label}</button>
			
            <input type="hidden" name="jkm_images_id" id="jkm_images_id" value ="{$image_id}" />
            <input type="hidden" name="jkm_images_url" id="jkm_images_url" value="{$image_url}" />
            <div id="images-container"> </div>
		</div>
		<div class="float_c"></div>
	</div>
</div>
EOD;
        echo $metabox_html;
    }


    function jkm_display_metabox($post)
    {
        $location = get_post_meta(
            $post->ID,
            'jkm_location',
            true
        );
        $is_favourite = get_post_meta($post->ID, 'jkm_is_favourite', true);
        $checked = $is_favourite == 1 ? 'checked' : '';
        $saved_color = get_post_meta($post->ID, 'jkm_color', true);



        $label = __('Location', 'jkmMetabox');
        $label2 = __('Is Favoruite?', 'jkmMetabox');
        $label4 = __('Colors', 'our-metabox');

        $colors = array('red', 'green', 'blue', 'yellow', 'magenta', 'pink', 'black');



        wp_nonce_field('jkm_location', 'jkm_location_field');
        $metabox_html = <<<EOD
        <p>
        <label for='jkm_location'>{$label}</label>
        <input type='text' name='jkm_location' id='jkm_location' value="{$location}" />
        </p>
        <p>
        <label for='jkm_is_favourite'>{$label2}</label>
        <input type='checkbox' name='jkm_is_favourite' id='jkm_is_favourite' value="1" {$checked} />
        
        EOD;
        $metabox_html .= "</p>";
        $metabox_html .= <<<EOD
<p>
<label>{$label4}: </label>
EOD;
        $saved_color = is_array($saved_color) ? $saved_color : array();
        foreach ($colors as $color) {
            $_color       = ucwords($color);
            $checked      = ($color == $saved_color) ? "checked='checked'" : '';
            $metabox_html .= <<<EOD
<label for="jkm_color_{$color}">{$_color}</label>
<input type="radio" name="jkm_color" id="jkm_color_{$color}" value="{$color}" {$checked}  />
EOD;
        }
        $metabox_html .= "</p>";

        echo $metabox_html;
    }
}

new JKMetabox();

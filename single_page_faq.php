<?php
/*
  Plugin Name: Single Page FAQ
  Plugin URI:
  Description: Single One Page FAQ
  Version: 1.0
  Author: Sumit Kumar Upadhyay
  Author URI: http://housebudgetplanner.com
  License: GPLv2
 */

/*
  Copyright (C) 2015 Sumit Kumar Upadhyay

  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
define('SINGLE_PAGE_FAQ_DIR', dirname(__FILE__) . '/');

class SimpleSinglePageFAQ_SKU_555 {

    private $options;

    public function __construct() {
        add_shortcode('SINGLE_PAGE_FAQ', array($this, 'single_page_faq'));
        add_action('init', array($this, 'single_page_faq_posttype'));
        add_action('admin_menu', array($this, 'single_page_faq_setting_page'));
        add_action('admin_init', array($this, 'single_page_faq_setting_register'));
        add_filter('manage_edit-single_page_faq_columns', array($this, 'spf_set_menuOrder_columns'));
        add_action('manage_single_page_faq_posts_custom_column', array($this, 'spf_menuOrder_column'));
        add_filter('manage_edit-single_page_faq_sortable_columns', array($this, 'spf_order_column_register_sortable'));
        register_activation_hook(__FILE__, array($this, 'single_page_faq_activate'));
        register_deactivation_hook(__FILE__, array($this, 'single_page_faq_deactive'));
//        wp_enqueue_style('dashicons');
    }

    public function spf_set_menuOrder_columns($columns) {
        $columns['menu_order'] = __('Sort Order');
        return $columns;
    }

    public function spf_menuOrder_column($column) {
        global $post;

        switch ($column) {
            case 'menu_order':
                $order = $post->menu_order;
                echo $order;
                break;
            default:
                break;
        }
    }

    public function spf_order_column_register_sortable($columns) {
        $columns['menu_order'] = 'menu_order';
        return $columns;
    }

    public function single_page_faq_activate() {
        add_option('single_page_faq', array(
            'order_type' => 'ASC',
            'order_by' => 'title'
        ));
    }

    public function single_page_faq_deactive() {
        delete_option('single_page_faq');
    }

    public function single_page_faq_posttype() {

        register_post_type('single_page_faq', array(
            'labels' => array(
                'name' => __('Single Page FAQ'),
                'singular_name' => __('FAQ'),
                'menu_name' => __('Single Page FAQ'),
                'add_new' => __('Add New FAQ'),
                'add_new_item' => __('Add New FAQ'),
                'new_item' => __('New FAQ'),
                'edit_item' => __('Edit FAQ'),
                'view_item' => __('View FAQ'),
                'all_items' => __('All FAQ'),
                'search_items' => __('Search FAQ'),
                'not_found' => __('No FAQ Found'),
                'not_found_in_trash' => __('No FAQ Found in Trash'),
                'hierarchical' => false,
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'single_page_faq'),
            'hierarchical' => false,
            'menu_icon' => 'dashicons-lightbulb',
            'supports' => array('title', 'editor', 'revisions', 'page-attributes'),
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
                )
        );
    }

    public function single_page_faq($atts) {
        wp_enqueue_style('dashicons');
        $args = array('post_type' => 'single_page_faq', 'posts_per_page' => -1);
        $loop = new WP_Query($args);
        $this->css();
        $this->js();
        ?>
        <?php //print_r(get_option('single_page_faq'));              ?> 
        <div class="show_collapse">
            <span class="show_all"><span class="dashicons dashicons-minus"></span>Show All</span>
            <span class="collapse_all"><span class="dashicons dashicons-plus"></span>Collapse All</span>
        </div>
        <?php
        $singleOptions = get_option('single_page_faq');
        $args = array(
            'orderby' => $singleOptions['order_by'],
            'order' => $singleOptions['order_type'],
            'post_type' => 'single_page_faq',
            'posts_per_page' => -1
        );
        $query = new WP_Query($args);
        while ($query->have_posts()) : $query->the_post();
            ?>
            <div class="question"><span class="dashicons dashicons-plus"></span>
                <?php the_title() ?>
            </div>
            <div class="answer"><?php the_content(); ?></div>
            <?php
        endwhile;
    }

    private function js() {
        ?>
        <script>
            jQuery(document).ready(function() {
                jQuery('.question').toggle(function() {
                    //jQuery('.answer').hide(400);
                    jQuery(this).next().slideDown();
                    jQuery(this).children('span.dashicons').removeClass('dashicons-plus').addClass('dashicons-minus');
                }, function() {
                    jQuery(this).next().slideUp();
                    jQuery(this).children('span.dashicons').removeClass('dashicons-minus').addClass('dashicons-plus');
                });
                jQuery('.show_all').click(function() {
                    jQuery('.question').children('span.dashicons').removeClass('dashicons-plus').addClass('dashicons-minus');
                    jQuery('.answer').slideDown();
                });
                jQuery('.collapse_all').click(function() {
                    jQuery('.question').children('span.dashicons').removeClass('dashicons-minus').addClass('dashicons-plus');
                    jQuery('.answer').slideUp();
                });
            });
        </script>
        <?php
    }

    private function css() {
        ?>
        <style>
            .show_all,.collapse_all,.question{
                cursor: pointer;
            }
            .answer{display: none;padding-left: 25px}
            .question {
                background: none repeat scroll 0 0 gray;
                color: #fff;
                font-weight: bold;
                margin-bottom: 10px;
                padding: 8px;
            }
            .site-content .entry-header, .site-content .entry-content, .site-content .entry-summary, .site-content .entry-meta, .page-content {
                margin: 0px auto;
                max-width: 100%;
            }            		
            div.question .dashicons,div.question .dashicons-before::before{
                vertical-align: text-top;
            }
            .show_collapse{
                text-align: right;
            }
            .show_collapse .dashicons {
                vertical-align: bottom;
            }
        </style>
        <?php
    }

    public function single_page_faq_setting_page() {
        add_options_page(
                'Settings Admin', 'Single Page FAQ Settings', 'manage_options', 'single-page-faq-settings', array($this, 'create_faq_setting_page')
        );
    }

    public function create_faq_setting_page() {
        // Set class property
        $this->options = get_option('single_page_faq');
        ?>
        <div class="wrap">
            <h2>Single Page FAQ'S Settings</h2>
            <form method="post" action="options.php">
                <?php
                settings_fields('single_page_faq_option_group');
                do_settings_sections('single-page-faq-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function single_page_faq_setting_register() {
        register_setting(
                'single_page_faq_option_group', // Option group
                'single_page_faq', // Option name
                array($this, 'sanitize') // Sanitize
        );

        add_settings_section(
                'setting_section_id', // ID
                '[SINGLE_PAGE_FAQ] use this shortcode to show FAQ data.', // Title
                array($this, 'print_section_info'), // Callback
                'single-page-faq-settings' // Page
        );

        add_settings_field(
                'order_by', // ID
                'Order By', // Title 
                array($this, 'order_by_callback'), // Callback
                'single-page-faq-settings', // Page
                'setting_section_id' // Section           
        );

        add_settings_field(
                'order_type', 'Order Type', array($this, 'order_type_callback'), 'single-page-faq-settings', 'setting_section_id'
        );
    }

    public function sanitize($input) {
        $new_input = array();
        if (isset($input['order_type']))
            $new_input['order_type'] = sanitize_text_field($input['order_type']);


        if (isset($input['order_by']))
            $new_input['order_by'] = sanitize_text_field($input['order_by']);

        return $new_input;
    }

    public function print_section_info() {
        print 'Enter your settings below:';
    }

    public function order_by_callback() {
        $orderBY = esc_attr($this->options['order_by']);
        ?>
        <select id="order_by" name="single_page_faq[order_by]">
            <option value="ID" <?php if ($orderBY == 'ID') echo'selected="selected "'; ?>>ID</option>
            <option value="title" <?php if ($orderBY == 'title') echo'selected="selected "'; ?>>Title</option>
            <option value="date" <?php if ($orderBY == 'date') echo'selected="selected "'; ?>>Date</option>
            <option value="modified" <?php if ($orderBY == 'modified') echo'selected="selected "'; ?>>Modified</option>
            <option value="rand" <?php if ($orderBY == 'rand') echo'selected="selected "'; ?>>Random</option>
            <option value="menu_order" <?php if ($orderBY == 'menu_order') echo'selected="selected "'; ?>>Sort Order</option>
        </select>
        <?php
    }

    public function order_type_callback() {
        $orderType = esc_attr($this->options['order_type']);
        ?>
        <input type="radio" name="single_page_faq[order_type]" value="ASC" <?php if ($orderType == 'ASC') echo'checked="checked"'; ?>> ASC &nbsp;
        <input type="radio" name="single_page_faq[order_type]" value="DESC" <?php if ($orderType == 'DESC') echo'checked="checked"'; ?>> DESC 
        <?php
    }

}

$SimpleSinglePageFaq = new SimpleSinglePageFAQ_SKU_555();

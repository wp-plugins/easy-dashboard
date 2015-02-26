<?php
/*
Plugin Name: Easy Dashboard
Plugin URI: http://wpgov.it
Description: Refresh your WordPress dashboard with this new elegant, metro-based one.
Author: Marco Milesi
Author URI: http://marcomilesi.ml
Version: 1.0
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

function easy_dashboard_register() {
    add_submenu_page('', 'Impostazioni', 'Impostazioni', 'read', 'wp-dashboard', 'easy_dashboard_render' );
    global $pagenow;

    if ( 'index.php' === $pagenow ) {
        if ( function_exists('admin_url') ) {
            wp_redirect( admin_url('admin.php?page=wp-dashboard') );
        }
    }
}
add_action('admin_menu', 'easy_dashboard_register' );


function easy_dashboard_render() {
    if (wp_get_current_user()->user_displayname) {
        $name = wp_get_current_user()->user_displayname;
    } else if (wp_get_current_user()->user_firstname) {
        $name = wp_get_current_user()->user_firstname;
    } else {
        $name = wp_get_current_user()->user_login;
    }
    echo '
    <style>
    .dashicons {
        margin: 20px;
    }
    .about-text {
        min-height: inherit!important;
    }
    .wp-box {
        width: 150px;
        height: 150px;
        margin: 5px;
        float:left;
        border-radius:2px;
    }
    .wp-box .dashicons {
        width:100%;
        font-size: 70px;
        text-align:center;
        margin: 20px 0;
    }
    .wp-box span {
        margin-top: 40px;
        text-align: center;
        float: left;
        width: 100%;
    }
    .small {
        width:100px;
        height:100px;
        font-size: 20px;
    }
    .small .dashicons {
        font-size: 40px;
    }
    .small span {
        margin-top: 10px;
        font-size: 14px;
    }
    </style>
    <div class="wrap about-wrap">
        <div style="float:right;">
            <a href="'.get_edit_user_link().'" title="'.__('Your Profile').'">
            <span class="dashicons dashicons-admin-users"></span>
            </a>
            <a href="'.wp_logout_url().'" title="'.__('Log out').'"><span class="dashicons dashicons-migrate"></span></a>
        </div>
        <h1>';
    printf(  __( 'Howdy, %1$s' ), $name );
    echo '!</h1>
        <div class="about-text">Welcome to Your dashboard.</div>
        <hr>
    ';

    global $menu;//print_r($menu);

    foreach($menu as $menuitem) {
        if ( current_user_can( $menuitem[1] ) && easy_dashboard_is_custom_post($menuitem[2]) == 1 ) {
            echo '<a href="'.admin_url( $menuitem[2] ).'"><div class="wp-box wp-ui-primary">';
            echo '<div class="dashicons '.$menuitem[6].'"></div>';
            echo '<span>' . get_post_type_object( dahsboarder_get_posttype( $menuitem[2] ) )->labels->name . '</span>';
            echo '</div></a>';
        }
    }

    echo '<div class="clear"></div>';

    foreach($menu as $menuitem) {
        if ( current_user_can( $menuitem[1] ) && easy_dashboard_is_custom_post($menuitem[2]) == 0 ) {
            echo '<a href="'.admin_url( easy_dashboard_format_link($menuitem[2]) ).'"><div class="wp-box small wp-ui-primary">';
            echo '<div class="dashicons '.$menuitem[6].'"></div>';
            echo '<span>' . $menuitem[0] . '</span>';
            echo '</div></a>';
        }
    }

}

function easy_dashboard_is_custom_post($slug) {
    if (strpos($slug, 'separator') !== false) { return -1; }

    if ($slug == 'edit.php' || $slug == 'upload.php' || strpos($slug, 'post_type') ) {
        return 1;
    }
        return 0;
}
function easy_dashboard_format_link($slug) {
    if (strpos($slug, 'php') !== false) {
        return $slug;
    } else {
        return 'admin.php?page='.$slug;
    }
}
function dahsboarder_get_posttype($slug) {

    switch ($slug) {

        case 'edit.php':
            return 'post';
            break;

        case 'upload.php':
            return 'attachment';
            break;

        default:
            return str_replace('edit.php?post_type=', null, $slug);
            break;


    }
}
?>

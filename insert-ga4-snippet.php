<?php
/**
 * Plugin Name: Insert GA4 Snippet
 * Description: This plugin inserts GA4 snippets into the site.
 * Version: 1.0.0
 * Author: Osamu Takahashi
 * Author URI: https://profiles.wordpress.org/osamunize/
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: insert-ga4-snippet
 * Domain Path: /languages
 */
defined( 'ABSPATH' ) || exit;

// メニューを追加
    add_action( 'admin_menu', 'register_insert_ga4_snippet_menu_page' );
    function register_insert_ga4_snippet_menu_page(){
        add_menu_page( 'Insert GA4 Snippet', 'Insert GA4 Snippet','manage_options', 'custompage', 'mt_insert_ga4_snippet_settings_page', ''); 
    }
    function mt_insert_ga4_snippet_settings_page() {

// ユーザーが必要な権限を持つか確認
    if (!current_user_can('manage_options'))
    {
        wp_die( __('You do not have sufficient permissions to access this page.') );
    }

// フィールドとオプション名の変数
    $opt_name = 'ga4_snippet';
    $hidden_field_name = 'mt_submit_hidden';
    $data_field_name = 'ga4_snippet';

// DBから既存のオプション値を取得
    $opt_val = get_option( $opt_name );

// ユーザーが何か情報を POST したかどうかを確認
// POST していれば、隠しフィールドに 'Y' が設定されている
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
        // POST されたデータを取得
        $opt_val = $_POST[ $data_field_name ];
        // POST された値をDBに保存
        update_option( $opt_name, $opt_val );
        // 画面に「Setting Saves」メッセージを表示
        ?>
        <div class="updated"><p><strong><?php _e('Settings Saved.', 'ga4_snippet_menu' ); ?></strong></p></div>
        <?php
    }

    echo '<div class="wrap">';
    echo "<h2>" . __( 'Insert GA4 Snippet', 'ga4_snippet_menu' ) . "</h2>";
    ?>
    <form name="form1" method="post" action="">
    <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
    <p><?php _e("GA4 Measurement ID (G-XXXXXXXXXX):", 'ga4_snippet_menu' ); ?> 
    <input type="text" name="<?php echo $data_field_name; ?>" value="<?php echo $opt_val; ?>" size="20">
    <p>If the value is empty or does not exist, no snippet is output.</p>
    <p class="submit">
    <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
    </p>
    </form>
    </div>
    <?php
    }

function ga4_inserter_head(){
    $opt_val = get_option( 'ga4_snippet' );
    if ( !null == $opt_val ){
        echo '
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id='.$opt_val.'"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}';
        echo "
          gtag('js', new Date());";
        echo "       
          gtag('config', '".$opt_val."');
        </script>        
        "."\n";}
    }
    add_action('wp_head', 'ga4_inserter_head' , 1);

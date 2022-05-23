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
        add_menu_page( 'Insert GA4 Snippet', 'Insert GA4 Snippet','manage_options', 'insert_ga4_snippet', 'mt_insert_ga4_snippet_settings_page', ''); 
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
    $opt_val = esc_js( get_option( $opt_name ));

// ユーザーが何か情報を POST したかどうかを確認
// nonceをチェック
    if ( ! empty( $_POST ) && check_admin_referer( 'insert_ga4_snippet_action','insert_ga4_snippet_nonce_field' ) ) {
        // POST されたデータを取得
        $opt_val = esc_attr($_POST[ $data_field_name ]);
        // 入力された値が不正の場合
        if ( !preg_match('/^G-[0-9A-Z]{10}$/',$opt_val) ){
        ?>
            <div class="error"><p><strong><?php esc_attr_e('Invalid Data.', 'ga4_snippet_menu' ); ?></strong></p></div>
            <?php
        }else{
            // POST された値をDBに保存
            update_option( $opt_name, $opt_val );
            // 画面に「Setting Saves」メッセージを表示
            ?>
            <div class="updated"><p><strong><?php esc_attr_e('Settings Saved.', 'ga4_snippet_menu' ); ?></strong></p></div>
            <?php
        }
    }

    echo '<div class="wrap">';
    echo "<h2>" . __( 'Insert GA4 Snippet', 'ga4_snippet_menu' ) . "</h2>";
    ?>
    <form name="insert-ga4-snippet-form" method="post" action="">
        <?php wp_nonce_field( 'insert_ga4_snippet_action', 'insert_ga4_snippet_nonce_field' ); ?>
        <input type="hidden" name="<?php echo esc_attr($hidden_field_name); ?>" value="Y">
        <p><?php esc_attr_e("Measurement ID (G-XXXXXXXXXX):", 'ga4_snippet_menu' ); ?> 
        <input type="text" name="<?php echo esc_attr($data_field_name); ?>" value="<?php echo esc_attr($opt_val); ?>" size="20">
        <p class="submit">
        <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
        </p>
    </form>
    </div>
    <?php
    }

function ga4_inserter_head(){
    $opt_val = esc_js(get_option( 'ga4_snippet' ));
    if ( !null == $opt_val ){
        echo '
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id='.esc_attr($opt_val).'"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}';}
        echo "
        gtag('js', new Date());

        gtag('config', '".esc_attr($opt_val)."');
        </script>
        "."\n";}
    
    add_action('wp_head', 'ga4_inserter_head' , 1);

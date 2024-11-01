<?php
    /**
     * @package Solaborate
     */
    /*
    Plugin Name: Solaborate Share
    Plugin URI: https://solaborate.com/
    Description: The Solaborate Share button allows users to add a personalized message to links before sharing them on solaborate.
    Version: 1.0.0
    Author: Petrit Selmani ( Solaborate )
    Author URI: https://solaborate.com/petrit-selmani/
    License: GPLv2 or later
    Text Domain: solaborate
    */
    
    //Declare globals
    
    $solaborate_share_positions = array('top', 'bottom');
    
    $solaborate_share_settings = array();
    
    function get_wp_verion() {
        return (float)substr(get_bloginfo('version'),0,3);
    }
    
    //Register default settings for the plugin
    function ssb_default_settings(){
    
          register_setting('solaborate_share', 'solaborate_share_show_at_bottom');
          register_setting('solaborate_share', 'solaborate_share_show_at_top');
          register_setting('solaborate_share', 'solaborate_share_color');
          register_setting('solaborate_share', 'solaborate_share_align');
          register_setting('solaborate_share', 'solaborate_share_position');
          register_setting('solaborate_share', 'solaborate_share_feed');
          register_setting('solaborate_share', 'solaborate_share_post');
          register_setting('solaborate_share', 'solaborate_share_page');
          register_setting('solaborate_share', 'solaborate_share_home');
          register_setting('solaborate_share', 'solaborate_share_search');
          register_setting('solaborate_share', 'solaborate_share_archive');
    }
    
    //Initalize the plugin
    function ssb_init_share_plugin(){
    
        if (get_wp_verion() >= 2.7) {
        if ( is_admin() ) {
            add_action('admin_init', 'solaborate_share_default_settings');
            }
        }
    
        global $solaborate_share_settings;
    
        add_filter('the_content', 'solaborate_share');
        add_filter('admin_menu', 'solaborate_admin_menu');
    
        $plugin = plugin_basename(__FILE__); 
        add_filter("plugin_action_links_$plugin", 'solaborate_settings_link' );
    
        add_option('solaborate_share_show_at_top', 'true');
        add_option('solaborate_share_show_at_bottom', 'true');
        add_option('solaborate_share_color', 'blue');
        add_option('solaborate_share_align', 'left');
        add_option('solaborate_share_position', 'top');
        add_option('solaborate_share_post', 'true');
        add_option('solaborate_share_page', 'true');
        add_option('solaborate_share_home', 'false');
        add_option('solaborate_share_search', 'false');
        add_option('solaborate_share_archive', 'false');
    
        $solaborate_share_settings['showbottom'] = get_option('solaborate_share_show_at_bottom') === 'true';
        $solaborate_share_settings['showtop'] = get_option('solaborate_share_show_at_top') === 'true';
        $solaborate_share_settings['showatposition'] = get_option('solaborate_share_position');
        $solaborate_share_settings['showatalign'] = get_option('solaborate_share_align');
        $solaborate_share_settings['showonpost'] = get_option('solaborate_share_post') === 'true';
        $solaborate_share_settings['showonpage'] = get_option('solaborate_share_page') === 'true';
        $solaborate_share_settings['showonhome'] = get_option('solaborate_share_home') === 'true';
        $solaborate_share_settings['showonsearch'] = get_option('solaborate_share_search') === 'true';
        $solaborate_share_settings['showonarchive'] = get_option('solaborate_share_archive') === 'true';
    
        add_action('wp_enqueue_scripts', 'solaborate_embed_script');
    }
    
    function ssb_solaborate_embed_script(){
        wp_register_script('share', 'https://www.solaborate.com/JS/solaborateshare.js', array(), FALSE);
    }
    
    //Adding Solaborate share button to the content
    function ssb_solaborate_share($content){
    
        global $solaborate_share_settings;
    
        if(is_single() ){
            if(!$solaborate_share_settings["showonpost"]){
                //wp_dequeue_script('share');
                return $content;
            }else{
                wp_enqueue_script('share');
            }
        } else if (is_home() or is_front_page()) {
            if (!$solaborate_share_settings['showonhome']) {	
                return $content; 
            }
        } elseif( is_page() ){
            if(!$solaborate_share_settings["showonpage"]){
                 //wp_dequeue_script('share');
                 return $content;
            }else{
                wp_enqueue_script('share');
            }
        }else if (is_search()) {
            if (!$solaborate_share_settings['showonsearch']) { return $content; }
        }else{
          //wp_dequeue_script('share');
          return $content; 
        }
    
        $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    
        $btn = '<div id="slc" data-url="'.$url.'" data-layout="1"></div>';
    
        $align = $solaborate_share_settings['showatalign']=='left'?'left':'right';
    
        // position to the right
        if($align == 'right'){
            $btn = '<div style="float: right; clear: both; text-align: right">'.$btn.'</div>';
        }
    
        // position to the left
        if($align == 'left'){
            $btn = '<div style="clear: both; text-align: left">'.$btn.'</div>';
        }
    
        $position = $solaborate_share_settings['showatposition']=='top'?'top':'bottom';
    
        if($position == 'top')
            $content = $btn.$content;
        if($position == 'bottom')
            $content .= $btn;
    
        return $content;
    }
    
    function ssb_solaborate_admin_menu()
    {
        add_options_page('Share Plugin Options', 'Solaborate Share', 'manage_options', 'share.php', 'solaborate_settings');
    }
    
    function ssb_solaborate_settings_link($links) {
        $settings_link = '<a href="options-general.php?page=share.php">Settings</a>'; 
        array_unshift($links, $settings_link); 
        return $links; 
    } 
    
    function ssb_solaborate_settings(){
        global $solaborate_share_colors;
        global $solaborate_share_aligns;
        global $solaborate_share_positions;
    
?>
<table>
    <tr>
        <td>
            <div class="wrap">
                <div id="solaborateLogo">
                    <a target="_blank" href="https://www.solaborate.com">
                        <img alt="Solaborate logo" src="https://www.solaborate.com/img/logo.png">
                    </a>
                    <h2>Share button</h2>
                </div>
                <form method="post" action="options.php">
                    <?php
                        if (get_wp_verion() < 2.7) {
                            wp_nonce_field('update-option');
                        } else {
                            settings_fields('solaborate_share');
                            do_settings_sections( 'solaborate_share' );
                        }
                    ?>

                    <table class="form-table">
                        <tr>
                            <th scope="row"><h2>Settings:</h2></th>
                        </tr>

                        <tr>
                            <th scope="row">Align:</th>
                            <td>
                                <select name="solaborate_share_align">
                                    <?php
                                        $curmenutype = get_option('solaborate_share_align');
                                        foreach ($solaborate_share_aligns as $type)
                                        {
                                            echo "<option value=\"$type\"". ($type == $curmenutype ? " selected":""). ">$type</option>";
                                        }
                                    ?>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">Show button at:</th>
                            <td>
                                <select name="solaborate_share_position">
                                    <?php
                                        $curmenutype = get_option('solaborate_share_position');
                                        foreach ($solaborate_share_positions as $type)
                                        {
                                            echo "<option value=\"$type\"". ($type == $curmenutype ? " selected":""). ">$type</option>";
                                        }
                                    ?>
                                </select>
                            </td>
                            <td>of content page</td>
                        </tr>

                        <tr>
                            <th scope="row">Show on post:</th>
                            <td><input type="checkbox" name="solaborate_share_post" value="true" <?php echo (get_option('solaborate_share_post') == 'true' ? 'checked' : ''); ?> /></td>
                        </tr>

                        <tr>
                            <th scope="row">Show on page:</th>
                            <td><input type="checkbox" name="solaborate_share_page" value="true" <?php echo (get_option('solaborate_share_page') == 'true' ? 'checked' : ''); ?> /></td>
                        </tr>
                        <tr>
                            <th scope="row"><h2>Help & Support</h2></th>
                        <tr>
                            <td>
                                <a href="mailto:support@solaborate.com" title="help">Solaborate support</a>
                            </td>

                        </tr>
                        <tr>
                            <td><a href="https://www.solaborate.com/public/aboutsite" title="help">About Solaborate</a></td>
                        </tr>
                    </table>

                    <?php if (get_wp_verion() < 2.7) : ?>
                    <input type="hidden" name="action" value="update" />
                    <input type="hidden" name="page_options" value="solaborate_share_search, solaborate_share_archive, solaborate_share_home, solaborate_share_page, solaborate_share_post, solaborate_share_feed" />
                    <?php endif; ?>
                    <p class="submit">
                        <input type="submit" name="Submit" value="Save Changes" />
                    </p>

                </form>
            </div>
        </td>
    </tr>
</table>
<?php
    }
    
    init_share();
    
?>
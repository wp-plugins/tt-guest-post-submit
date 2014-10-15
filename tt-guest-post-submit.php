<?php
/*
Plugin Name: TT Guest Post Submit
Plugin URI: http://www.technologiestoday.com.au/guide-to-use-tt-guest-post-submit-plugin-for-wordpress/
Description: Enables your visitors to submit posts and images from anywhere on your site.
Tags: add post, content submission, guest blog, guest blogging, guest posting, wordpress guest post, post, submit post, submit, guest, guest post, admin, anonymous post, guest author, guest author plugin, post from front end, visitor post, captcha, secured post submit, user submitted post
Author: Rashed Latif
Author URI: http://www.technologiestoday.com.au/rashed-latif
Donate link: http://www.technologiestoday.com.au/donate
Requires at least: 3.0.1
Tested up to: 4.0
Version: 2.2
Stable tag: 2.2
License: GPL v2
*/
include dirname( __FILE__ ).'/ttgps-functions.php';
class TT_GuestPostSubmit{
    
    public function __construct(){
        wp_enqueue_style('ttgps-style', plugins_url('ttgps-style.css',__FILE__));
        if (is_admin()){
	    add_action( 'admin_menu', array($this, 'ttgps_add_settings_menu') );
            add_action( 'admin_init', array($this, 'ttgps_init_settings') );
	}
	$this->options = get_option( 'ttgps_options' );
        $this->enable_shortcode();
	add_action( 'template_redirect', array($this, 'ttgps_template_redirection')  );
    }
    
    public function ttgps_template_redirection( $template ) {	
	if ( !empty( $_POST['ttgps_form_submitted'] ) ) {	    
	    $this->ttgps_process_submit_form();
	} else {
	    return $template;
	}		
    }
    
    public function ttgps_add_settings_menu() {
	//add_options_page( __('TT Guest Post Submit Options', 'ttgps_text_domain'), __('TT Guest Post Submit', 'ttgps_text_domain'), 'administrator', __FILE__, array($this, 'ttgps_display_menu_page') );
	add_options_page( 'TT Guest Post Submit Options', 'TT Guest Post Submit', 'administrator', __FILE__, array($this, 'ttgps_display_menu_page') );
    }
    
    public function ttgps_display_menu_page(){

	?>
	<div id="tt-general" class="wrap">
            <h2><?php _e('TT Guest Post Submit Options','ttgps_text_domain'); ?></h2>
            <form name="ttgps_options_form_settings_api" method="post" action="options.php">
		<?php settings_fields( 'ttgps_settings' ); ?>
		<?php do_settings_sections( 'ttgps_settings_section' ); ?> 
		<input type="submit" value="Submit" class="button-primary" id="ttgps" />
            </form>
	</div>
	<?php
    }
    
    public function ttgps_init_settings(){
	
        register_setting( 'ttgps_settings', 'ttgps_options');
		
	add_settings_section( 'ttgps_general_settings_section', __('General Settings', 'ttgps_text_domain'), array($this, 'ttgps_general_setting_section_callback'), 'ttgps_settings_section' );
	add_settings_section( 'ttgps_field_selection_section', __('Field Selection', 'ttgps_text_domain'), array($this, 'ttgps_field_selection_section_callback'), 'ttgps_settings_section' );
        
	add_settings_field( 'ttgps_chk_notifyfield', __('Send Notification via Email', 'ttgps_text_domain'), array($this,'ttgps_display_check_box'), 'ttgps_settings_section', 'ttgps_general_settings_section', array('name' => 'ttgps_chk_notifyfield' ));
        add_settings_field( 'ttgps_txt_contact_email', __('Email for Notification', 'ttgps_text_domain'), array($this,'ttgps_display_text_field'), 'ttgps_settings_section', 'ttgps_general_settings_section', array( 'name' => 'ttgps_txt_contact_email', 'txt_type' => 'email', 'place_holder' =>'Email Address For Sending Notification'  ) );
        add_settings_field( 'ttgps_txt_confirmation_msg', __('Post Submit Confirmation Message', 'ttgps_text_domain'), array($this,'ttgps_display_text_field'), 'ttgps_settings_section', 'ttgps_general_settings_section', array( 'name' => 'ttgps_txt_confirmation_msg', 'txt_type' => 'text', 'place_holder' =>'Type Message To Show When Post Submit Successfull'  ) );
        add_settings_field( 'ttgps_txt_failure_msg', __('Post Submit Failure Message', 'ttgps_text_domain'), array($this,'ttgps_display_text_field'), 'ttgps_settings_section', 'ttgps_general_settings_section', array( 'name' => 'ttgps_txt_failure_msg', 'txt_type' => 'text', 'place_holder' =>'Type Message To Show When Post Submit Fails'  ) );
	add_settings_field( 'ttgps_txt_redirect', __('Redirect To', 'ttgps_text_domain'), array($this,'ttgps_display_text_field'), 'ttgps_settings_section', 'ttgps_general_settings_section', array( 'name' => 'ttgps_txt_redirect', 'txt_type' => 'text', 'place_holder' =>'URL to Redirect After Post Submit'  ) );
        add_settings_field( 'ttgps_drp_status', __('Publish Status', 'ttgps_text_domain'), array($this,'ttgps_display_dropdown'), 'ttgps_settings_section', 'ttgps_general_settings_section', array( 'name' => 'ttgps_drp_status', 'drp_type' => 'post_status' ) );
	add_settings_field( 'ttgps_drp_account', __('Guest Account', 'ttgps_text_domain'), array($this,'ttgps_display_dropdown'), 'ttgps_settings_section', 'ttgps_general_settings_section', array( 'name' => 'ttgps_drp_account', 'drp_type' => 'guest_account' ) );
	add_settings_field( 'ttgps_txt_maxlength', __('Maximum Length of the post', 'ttgps_text_domain'), array($this,'ttgps_display_text_field'), 'ttgps_settings_section', 'ttgps_general_settings_section', array( 'name' => 'ttgps_txt_maxlength', 'txt_type' => 'number', 'place_holder' =>'Number of characters'  ) );
	add_settings_field( 'ttgps_chk_filter', __('Enable Filter', 'ttgps_text_domain'), array($this,'ttgps_display_check_box'), 'ttgps_settings_section', 'ttgps_general_settings_section', array('name' => 'ttgps_chk_filter' ));
	add_settings_field( 'ttgps_txta_filter', __('Add Filtered Words', 'ttgps_text_domain'), array($this,'ttgps_display_text_area'), 'ttgps_settings_section', 'ttgps_general_settings_section', array( 'name' => 'ttgps_txta_filter', 'txt_type' => 'area', 'place_holder' =>'Add Filtered Words'  ) );
	
        add_settings_field( 'ttgps_chk_titlefield', __('Add Title Field', 'ttgps_text_domain'), array($this,'ttgps_display_check_box'), 'ttgps_settings_section', 'ttgps_field_selection_section', array('name' => 'ttgps_chk_titlefield', 'req' => true));
	add_settings_field( 'ttgps_chk_contentfield', __('Add Post Content Field', 'ttgps_text_domain'), array($this,'ttgps_display_check_box'), 'ttgps_settings_section', 'ttgps_field_selection_section', array('name' => 'ttgps_chk_contentfield', 'req' => true));
	add_settings_field( 'ttgps_chk_categoryfield', __('Add Category Field', 'ttgps_text_domain'), array($this,'ttgps_display_check_box'), 'ttgps_settings_section', 'ttgps_field_selection_section', array('name' => 'ttgps_chk_categoryfield', 'req' => true));
	add_settings_field( 'ttgps_chk_tagsfield', __('Add Tags Field', 'ttgps_text_domain'), array($this,'ttgps_display_check_box'), 'ttgps_settings_section', 'ttgps_field_selection_section', array('name' => 'ttgps_chk_tagsfield', 'req' => true));
	add_settings_field( 'ttgps_chk_namefield', __('Add Author\'s Name Field', 'ttgps_text_domain'), array($this,'ttgps_display_check_box'), 'ttgps_settings_section', 'ttgps_field_selection_section', array('name' => 'ttgps_chk_namefield', 'req' => true));
        add_settings_field( 'ttgps_chk_emailfield', __('Add Authors\'s Email Field', 'ttgps_text_domain'), array($this,'ttgps_display_check_box'), 'ttgps_settings_section', 'ttgps_field_selection_section', array('name' => 'ttgps_chk_emailfield', 'req' => true));
	add_settings_field( 'ttgps_chk_websitefield', __('Add Website Field', 'ttgps_text_domain'), array($this,'ttgps_display_check_box'), 'ttgps_settings_section', 'ttgps_field_selection_section', array('name' => 'ttgps_chk_websitefield', 'req' => true));
        add_settings_field( 'ttgps_chk_captchafield', __('Add Captcha Field', 'ttgps_text_domain'), array($this,'ttgps_display_check_box'), 'ttgps_settings_section', 'ttgps_field_selection_section', array('name' => 'ttgps_chk_captchafield', 'req' => true));
        add_settings_field( 'ttgps_chk_uploadafield', __('Add Upload Field', 'ttgps_text_domain'), array($this,'ttgps_display_check_box'), 'ttgps_settings_section', 'ttgps_field_selection_section', array('name' => 'ttgps_chk_uploadfield', 'req' => true));
	
    }
    
    public function ttgps_general_setting_section_callback() {
	echo "<p>".__("General configuration section", 'ttgps_text_domain')."</p>";
    }
	
    public function ttgps_field_selection_section_callback() {
        echo "<p>".__("Select fields which you want to be appear on post submit form", 'ttgps_text_domain')."</p>";
    }
    
    public function ttgps_display_text_field( $data = array() ) {
	extract( $data );
	//$options = get_option( 'ttgps_options' ); 
	
	?>
	<input type="<?php echo $txt_type ?>" name="ttgps_options[<?php echo $name; ?>]" placeholder="<?php echo $place_holder; ?>" size="50" value="<?php echo esc_html( $this->options[$name] ); ?>"/><br />
	<?php
    }

    
    public function ttgps_display_text_area( $data = array() ){
	extract( $data );
	?>
	<textarea rows="5" cols="45" maxlength="5000" name="ttgps_options[<?php echo $name; ?>]" placeholder="<?php echo $place_holder; ?>" ><?php echo esc_html( $this->options[$name]);?></textarea>
	<?php
    }
    

    public function ttgps_display_check_box( $data = array() ) {
	extract ( $data );
	$required_item = $name . "_req";
	?>
	<input type="checkbox" name="ttgps_options[<?php echo $name; ?>]" <?php if (isset($this->options[$name])) echo ' checked="checked" '; ?>/>
	<?php
	
	if (isset($req) && $req==true){
	?>
            <label id="required-label"><?php _e('Required', 'ttgps_text_domain'); ?></label> <input type="checkbox" name="ttgps_options[<?php echo $required_item; ?>]" <?php if (isset( $this->options[$required_item] )) echo ' checked="checked" '; ?>/>
	<?php
	}
    }

    public function ttgps_display_dropdown( $data = array() ) {
	extract($data);
	if ($drp_type == 'post_status'){
	    $drp_array = array('Publish', 'Pending', 'Draft');	
	}else if($drp_type == 'guest_account'){
	    $drp_array = get_users();  
	    
	}
	?>
	<select name="ttgps_options[<?php echo $name; ?>]" >
	    <?php
	    foreach($drp_array as $drp_item){ 
		if($drp_type == 'guest_account'){?>
		    <option value="<?php echo $drp_item->display_name; ?>" <?php echo selected( $this->options['ttgps_drp_account'], $drp_item->display_name ); ?> > <?php echo $drp_item->display_name; ?></option>			    
		<?php }else{ ?>
		    <option value="<?php echo $drp_item; ?>" <?php echo selected( $this->options['ttgps_drp_status'], $drp_item ); ?> ><?php echo $drp_item; ?></option>	
	    <?php }
	    } ?>
	</select>
	<?php
    }
    
    public function enable_shortcode(){
        add_shortcode('tt-submit-post', array($this, 'ttgps_guest_submit_post_shortcode') );
    }
    
    public function ttgps_guest_submit_post_shortcode($atts){
	
	$user = get_user_by('login', $this->options['ttgps_drp_account']);
	extract(shortcode_atts(array(
                            'author' => $user->ID,
                            'redirect_url' => $this->options['ttgps_txt_redirect'], //get_permalink(),
                            ), $atts )
                );
        if (is_user_logged_in()){
            $author = get_current_user_id();    
        }else{
	    
	    $user = get_user_by('login', $this->options['ttgps_drp_account']);
	    $author = $user->ID;
	}
	
	$to_mail = "";
	if(empty($this->options['ttgps_txt_contact_email'])){
	    $to_mail = get_option('admin_email');
	}else{
	    $to_mail = $this->options['ttgps_txt_contact_email'];
	}
	
	$template_str = "";

	//Display confirmation message to users who submit a book review
	if ( isset ( $_GET['submission_success'] ) && $_GET['submission_success'] ) {
	    
	    $template_str = '<div class="message-box">' . 
				$this->options['ttgps_txt_confirmation_msg'] .
			    '</div>';
	}

	//Post variable to indicate user-submitted items
	$template_str .= '<input type="hidden" name="ttgps_form_submitted" value="1" />';
        $template_str .= '<form id="ttgps-form" action="" method="post" enctype="multipart/form-data">
			    <div id="wrapping" class="clearfix">
                                <section id="aligned">';
                                            
				    if(isset($this->options['ttgps_chk_titlefield']) && $this->options['ttgps_chk_titlefield'] == "on"){
					$template_str .= '<input  type="text" class="txtinput" id="title" name="title" title= "'.__("Please Enter a Post Title","ttgps_text_domain").'" x-moz-errormessage="'.__("Please Enter a Post Title","ttgps_text_domain").'" size="72"';
					$template_str .= (isset($this->options['ttgps_chk_titlefield_req']) && $this->options['ttgps_chk_titlefield_req']=="on") ? ' required="required" ' : ' ';
					$template_str .= 'placeholder="'.__("Post Title Here", "ttgps_text_domain").'">';// . wp_nonce_field();
				    }
                                    if(isset($this->options['ttgps_chk_contentfield']) && $this->options['ttgps_chk_contentfield'] == "on"){   
				        $template_str .= '<textarea class="txtblock" name="content" title="'.__("Please Enter Contents", "ttgps_text_domain").'" x-moz-errormessage="'.__("Please Enter Contents", "ttgps_text_domain").'" rows="15" cols="72" maxlength="'.$this->options['ttgps_txt_maxlength'].'"';
					$template_str .= (isset($this->options['ttgps_chk_contentfield_req']) && $this->options['ttgps_chk_contentfield_req']=="on") ? ' required="required" ' : ' ';
					$template_str .= 'placeholder="'.__("Write Your Post Contents", "ttgps_text_domain").'"></textarea>';
				    }
				    if(isset($this->options['ttgps_chk_categoryfield']) && $this->options['ttgps_chk_categoryfield'] == "on"){   
					$args = array(
						'orderby' => 'name',
						'order' => 'ASC'
						);
					$categories = get_categories($args);
					$template_str .= '<select name="catdrp" class="postform" id="catdrp" ';
					$template_str .= (isset($this->options['ttgps_chk_categoryfield_req']) && $this->options['ttgps_chk_categoryfield_req']=="on") ? ' required="required" ' : ' ';
					$template_str .= '> <option value="">'.__("Select a Category", "ttgps_text_domain").'</option>';
					foreach($categories as $category) { 
					    $template_str .= '<option value="' . $category->cat_ID . '">'.$category->name.'</option>';
					}
					$template_str .= '</select>';
				    }
				    if(isset($this->options['ttgps_chk_tagsfield']) && $this->options['ttgps_chk_tagsfield'] == "on"){
				        $template_str .= '<input type="text" class="txtinput" id="tags" name="tags" size="72"';
					$template_str .= (isset($this->options['ttgps_chk_tagsfield_req']) && $this->options['ttgps_chk_tagsfield_req']=="on") ? ' required="required" ' : ' ';
					$template_str .= 'placeholder="'.__("Comma Separated Tags", "ttgps_text_domain").'">';
				    }
				    if(isset($this->options['ttgps_chk_namefield']) && $this->options['ttgps_chk_namefield'] == "on"){
					$template_str .= '<input type="text" class="txtinput" title="'.__("Please Enter Author Name", "ttgps_text_domain").'" x-moz-errormessage="'.__("Please Enter Author Name", "ttgps_text_domain").'"  id="author"  name="author" size="72"';
					$template_str .= (isset($this->options['ttgps_chk_namefield_req']) && $this->options['ttgps_chk_namefield_req']=="on") ? ' required="required" ' : ' ';
					$template_str .= 'placeholder="'.__("Your Name Here", "ttgps_text_domain").'">';       
				    }
				    if(isset($this->options['ttgps_chk_emailfield']) && $this->options['ttgps_chk_emailfield'] == "on"){
					$template_str .= '<input type="email" class="txtinput" title="'.__("Please Enter a Valid Email Address", "ttgps_text_domain").'" x-moz-errormessage="'.__("Please Enter a Valid Email Address", "ttgps_text_domain").'" id="email" name="email" size="72"';
					$template_str .= (isset($this->options['ttgps_chk_emailfield_req']) && $this->options['ttgps_chk_emailfield_req']=="on") ? ' required="required" ' : ' ';
					$template_str .= 'placeholder="'.__("Your Email Address Here", "ttgps_text_domain").'">';
				    }
				    if( isset($this->options['ttgps_chk_websitefield']) && $this->options['ttgps_chk_websitefield'] == "on"){
				        $template_str .= '<input type="text" class="txtinput" id="site" name="site" size="72"';
					$template_str .= (isset($this->options['ttgps_chk_websitefield_req']) && $this->options['ttgps_chk_websitefield_req']=="on") ? ' required="required" ' : ' ';
					$template_str .= 'placeholder="'.__("Your Website Address Here", "ttgps_text_domain").'">';
				    }
				    if(isset($this->options['ttgps_chk_uploadfield']) && $this->options['ttgps_chk_uploadfield'] == "on"){
					$template_str .= '<p id="fi-title">'. __("Upload Featured Image and Additional Images","ttgps_text_domain") . '</p>
							    <div class="featured-img">
								<input name="featured-img[]" type="file" id="featured-img"';
								$template_str .= (isset($this->options['ttgps_chk_uploadfield_req']) && $this->options['ttgps_chk_uploadfield_req']=="on") ? ' required="required" ' : ' ';
								$template_str .= ' multiple="multiple"><br>
							    </div>';
				    }
				    if(isset($this->options['ttgps_chk_captchafield']) && $this->options['ttgps_chk_captchafield'] == "on"){
					$template_str .= '<img src="' . plugins_url( 'EasyCaptcha/easycaptcha.php', __FILE__ ) . '" id="captcha-code" />' .
							 '<input type="text" class="" placeholder="'.__("Captcha Code Here", "ttgps_text_domain").'" x-moz-errormessage="Please Enter Correct Captcha Code" id="code" name="ttgps_captcha" size="16"';
					$template_str .= (isset($this->options['ttgps_chk_captchafield_req']) && $this->options['ttgps_chk_captchafield_req']=="on") ? ' required="required" ' : ' ';
					$template_str .= ' />';
				    }            
				        $template_str .= '<input type="hidden" value="'. $author .'" name="authorid">
							  <input type="hidden" value="'. $redirect_url .'" name="redirect_url">
							  <input type="hidden" value="'. $this->options["ttgps_drp_status"] .'" name="post_status">
							  
							  <input type="hidden" value="';
							  $template_str .= isset($this->options["ttgps_chk_notifyfield"])?$this->options["ttgps_chk_notifyfield"]:"";
							  $template_str .= '" name="notify_flag">
							  
							  <input type="hidden" value="'. $this->check_and_set_value("ttgps_chk_captchafield") .'" name="capf">
							  
							  <input type="hidden" value="'. $this->check_and_set_value("ttgps_chk_captchafield_req") .'" name="capr">
							  
							  <input type="hidden" value="'. $this->check_and_set_value("ttgps_chk_filter") .'" name="enable_filter">

							  <input type="hidden" value="'. $this->check_and_set_value("ttgps_txta_filter") .'" name="filter_items">
							  
							  
							  <input type="hidden" value="'. $to_mail .'" name="to_email">
							  <input type="hidden" name="ttgps_form_submitted" value="1" />' . 
                                '</section>
                                <section id="buttons">
                                        <input type="reset" name="reset" id="resetbtn" class="resetbtn" value="'.__("Reset", "ttgps_text_domain").'">
                                        <input type="submit" name="submit" id="submitbtn" class="submitbtn" tabindex="7" value="'.__("Submit Post", "ttgps_text_domain").'">
                                        <br style="clear:both;">
                                </section>
                            </div>
                        </form>';
        return $template_str; 
    }
    
    public function check_and_set_value($val){
	if(isset($this->options[$val])){
	    return $this->options[$val];
	}else{
	    return "";
	}
	
    }
    
    public function ttgps_process_submit_form(){
	submit_post_function();
    } // End of function

} // End of Class



add_action( 'init', 'ttgps_plugin_init' );
function ttgps_plugin_init() {
	$ttgpsObj = new TT_GuestPostSubmit();
	load_plugin_textdomain( 'ttgps_text_domain', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

?>
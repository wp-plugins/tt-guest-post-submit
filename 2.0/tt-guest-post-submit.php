<?php
/*
Plugin Name: TT Guest Post Submit
Plugin URI: http://www.knowhowto.com.au/use-tt-guest-post-submit-plugin-wordpress/
Description: Enables your visitors to submit posts and images from anywhere on your site.
Tags: add post, content submission, guest blog, guest blogging, guest posting, wordpress guest post, post, submit post, submit, guest, guest post, admin, anonymous post, guest author, guest author plugin, post from front end, visitor post, captcha, secured post submit, user submitted post
Author: Rashed Latif
Author URI: http://www.knowhowto.com.au/rashed-latif
Donate link: http://www.knowhowto.com.au/donate
Requires at least: 3.0.1
Tested up to: 3.9.1
Version: 2.0
Stable tag: 2.0
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
	add_options_page( 'TT Guest Post Submit Options', 'TT Guest Post Submit', 'administrator', __FILE__, array($this, 'ttgps_display_menu_page') );
    }
    
    public function ttgps_display_menu_page(){

	?>
	<div id="tt-general" class="wrap">
            <h2>TT Guest Post Submit Options</h2>
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
		
	add_settings_section( 'ttgps_general_settings_section', 'General Settings', array($this, 'ttgps_general_setting_section_callback'), 'ttgps_settings_section' );
	add_settings_section( 'ttgps_field_selection_section', 'Field Selection', array($this, 'ttgps_field_selection_section_callback'), 'ttgps_settings_section' );
        
	add_settings_field( 'ttgps_chk_notifyfield', 'Send Notification via Email', array($this,'ttgps_display_check_box'), 'ttgps_settings_section', 'ttgps_general_settings_section', array('name' => 'ttgps_chk_notifyfield' ));
        add_settings_field( 'ttgps_txt_contact_email', 'Email for Notification', array($this,'ttgps_display_text_field'), 'ttgps_settings_section', 'ttgps_general_settings_section', array( 'name' => 'ttgps_txt_contact_email', 'txt_type' => 'email', 'place_holder' =>'Email Address For Sending Notification'  ) );
        add_settings_field( 'ttgps_txt_confirmation_msg', 'Post Submit Confirmation Message', array($this,'ttgps_display_text_field'), 'ttgps_settings_section', 'ttgps_general_settings_section', array( 'name' => 'ttgps_txt_confirmation_msg', 'txt_type' => 'text', 'place_holder' =>'Type Message To Show When Post Submit Successfull'  ) );
        add_settings_field( 'ttgps_txt_failure_msg', 'Post Submit Failure Message', array($this,'ttgps_display_text_field'), 'ttgps_settings_section', 'ttgps_general_settings_section', array( 'name' => 'ttgps_txt_failure_msg', 'txt_type' => 'text', 'place_holder' =>'Type Message To Show When Post Submit Fails'  ) );
	add_settings_field( 'ttgps_txt_redirect', 'Redirect To', array($this,'ttgps_display_text_field'), 'ttgps_settings_section', 'ttgps_general_settings_section', array( 'name' => 'ttgps_txt_redirect', 'txt_type' => 'text', 'place_holder' =>'URL to Redirect After Post Submit'  ) );
        add_settings_field( 'ttgps_drp_status', 'Publish Status', array($this,'ttgps_display_dropdown'), 'ttgps_settings_section', 'ttgps_general_settings_section', array( 'name' => 'ttgps_drp_status', 'drp_type' => 'post_status' ) );
	add_settings_field( 'ttgps_drp_account', 'Guest Account', array($this,'ttgps_display_dropdown'), 'ttgps_settings_section', 'ttgps_general_settings_section', array( 'name' => 'ttgps_drp_account', 'drp_type' => 'guest_account' ) );
	
        add_settings_field( 'ttgps_chk_titlefield', 'Add Title Field', array($this,'ttgps_display_check_box'), 'ttgps_settings_section', 'ttgps_field_selection_section', array('name' => 'ttgps_chk_titlefield', 'req' => true));
	add_settings_field( 'ttgps_chk_contentfield', 'Add Post Content Field', array($this,'ttgps_display_check_box'), 'ttgps_settings_section', 'ttgps_field_selection_section', array('name' => 'ttgps_chk_contentfield', 'req' => true));
	add_settings_field( 'ttgps_chk_categoryfield', 'Add Category Field', array($this,'ttgps_display_check_box'), 'ttgps_settings_section', 'ttgps_field_selection_section', array('name' => 'ttgps_chk_categoryfield', 'req' => true));
	add_settings_field( 'ttgps_chk_tagsfield', 'Add Tags Field', array($this,'ttgps_display_check_box'), 'ttgps_settings_section', 'ttgps_field_selection_section', array('name' => 'ttgps_chk_tagsfield', 'req' => true));
	add_settings_field( 'ttgps_chk_namefield', 'Add Author\'s Name Field', array($this,'ttgps_display_check_box'), 'ttgps_settings_section', 'ttgps_field_selection_section', array('name' => 'ttgps_chk_namefield', 'req' => true));
        add_settings_field( 'ttgps_chk_emailfield', 'Add Authors\'s Email Field', array($this,'ttgps_display_check_box'), 'ttgps_settings_section', 'ttgps_field_selection_section', array('name' => 'ttgps_chk_emailfield', 'req' => true));
	add_settings_field( 'ttgps_chk_websitefield', 'Add Website Field', array($this,'ttgps_display_check_box'), 'ttgps_settings_section', 'ttgps_field_selection_section', array('name' => 'ttgps_chk_websitefield', 'req' => true));
        add_settings_field( 'ttgps_chk_captchafield', 'Add Captcha Field', array($this,'ttgps_display_check_box'), 'ttgps_settings_section', 'ttgps_field_selection_section', array('name' => 'ttgps_chk_captchafield', 'req' => true));
        add_settings_field( 'ttgps_chk_uploadafield', 'Add Upload Field', array($this,'ttgps_display_check_box'), 'ttgps_settings_section', 'ttgps_field_selection_section', array('name' => 'ttgps_chk_uploadfield', 'req' => true));
	
    }
    
    public function ttgps_general_setting_section_callback() {
	echo "<p>General configuration section</p>";
    }
	
    public function ttgps_field_selection_section_callback() {
        echo "<p>Select fields which you want to be appear on contact form</p>";
    }
    
    public function ttgps_display_text_field( $data = array() ) {
	extract( $data );
	//$options = get_option( 'ttgps_options' ); 
	
	?>
	<input type="<?php echo $txt_type ?>" name="ttgps_options[<?php echo $name; ?>]" placeholder="<?php echo $place_holder; ?>" size="50" value="<?php echo esc_html( $this->options[$name] ); ?>"/><br />
	<?php
    }


    public function ttgps_display_check_box( $data = array() ) {
	extract ( $data );
	$required_item = $name . "_req";
	?>
	<input type="checkbox" name="ttgps_options[<?php echo $name; ?>]" <?php if ( $this->options[$name] ) echo ' checked="checked" '; ?>/>
	<?php
	if ($req==true){
	?>
            <label id="required-label">Required</label> <input type="checkbox" name="ttgps_options[<?php echo $required_item; ?>]" <?php if ( $this->options[$required_item] ) echo ' checked="checked" '; ?>/>
	<?php
	}
    }

    public function ttgps_display_dropdown( $data = array() ) {
	extract($data);
	if ($drp_type == 'post_status'){
	    $drp_array = array('Published', 'Pending', 'Draft');	
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
                                            
				    if($this->options['ttgps_chk_titlefield'] == "on"){
					$template_str .= '<input  type="text" class="txtinput" id="title" name="title" title="Please Enter a Post Title" x-moz-errormessage="Please Enter a Post Title" size="72"';
					$template_str .= ($this->options['ttgps_chk_titlefield_req']=="on") ? ' required="required" ' : ' ';
					$template_str .= 'placeholder="Post Title Here">' . wp_nonce_field();
				    }
                                    if($this->options['ttgps_chk_contentfield'] == "on"){   
				        $template_str .= '<textarea class="txtblock" name="content" title="Please Enter Contents" x-moz-errormessage="Please Enter Contents" rows="15" cols="72"';
					$template_str .= ($this->options['ttgps_chk_contentfield_req']=="on") ? ' required="required" ' : ' ';
					$template_str .= 'placeholder="Write Your Post Contents"></textarea>';
				    }
				    if($this->options['ttgps_chk_categoryfield'] == "on"){   
					$args = array(
						'orderby' => 'name',
						'order' => 'ASC'
						);
					$categories = get_categories($args);
					$template_str .= '<select name="catdrp" class="postform" id="catdrp" ';
					$template_str .= ($this->options['ttgps_chk_categoryfield_req']=="on") ? ' required="required" ' : ' ';
					$template_str .= '> <option value="">Select a Category</option>';
					foreach($categories as $category) { 
					    $template_str .= '<option value="' . $category->cat_ID . '">'.$category->name.'</option>';
					}
					$template_str .= '</select>';
				    }
				    if($this->options['ttgps_chk_tagsfield'] == "on"){
				        $template_str .= '<input type="text" class="txtinput" id="tags" name="tags" size="72"';
					$template_str .= ($this->options['ttgps_chk_tagsfield_req']=="on") ? ' required="required" ' : ' ';
					$template_str .= 'placeholder="Comma Separated Tags">';
				    }
				    if($this->options['ttgps_chk_namefield'] == "on"){
					$template_str .= '<input type="text" class="txtinput" title="Please Enter Author Name" x-moz-errormessage="Please Enter Author Name"  id="author"  name="author" size="72"';
					$template_str .= ($this->options['ttgps_chk_namefield_req']=="on") ? ' required="required" ' : ' ';
					$template_str .= 'placeholder="Your name here">';       
				    }
				    if($this->options['ttgps_chk_emailfield'] == "on"){
					$template_str .= '<input type="email" class="txtinput" title="Please Enter a Valid Email Address" x-moz-errormessage="Please Enter a Valid Email Address " id="email" name="email" size="72"';
					$template_str .= ($this->options['ttgps_chk_emailfield_req']=="on") ? ' required="required" ' : ' ';
					$template_str .= 'placeholder="Your Email address Here">';
				    }
				    if($this->options['ttgps_chk_websitefield'] == "on"){
				        $template_str .= '<input type="text" class="txtinput" id="site" name="site" size="72"';
					$template_str .= ($this->options['ttgps_chk_websitefield_req']=="on") ? ' required="required" ' : ' ';
					$template_str .= 'placeholder="Your Website Address Here">';
				    }
				    if($this->options['ttgps_chk_uploadfield'] == "on"){
					$template_str .= '<p id="fi-title">Upload Featured Image</p>
							    <div class="featured-img">
								<input name="featured-img[]" type="file" id="featured-img"';
								$template_str .= ($this->options['ttgps_chk_uploadfield_req']=="on") ? ' required="required" ' : ' ';
								$template_str .= ' multiple="multiple"><br>
							    </div>';
				    }
				    if($this->options['ttgps_chk_captchafield'] == "on"){
					$template_str .= '<img src="' . plugins_url( 'EasyCaptcha/easycaptcha.php', __FILE__ ) . '" id="captcha-code" />' .
							 '<input type="text" class="" title="Please Enter Correct Captcha Code" x-moz-errormessage="Please Enter Correct Captcha Code" id="code" name="ttgps_captcha" size="10"';
					$template_str .= ($this->options['ttgps_chk_captchafield_req']=="on") ? ' required="required" ' : ' ';
					$template_str .= ' />';
				    }            
				        $template_str .= '<input type="hidden" value="'. $author .'" name="authorid">
							  <input type="hidden" value="'. $redirect_url .'" name="redirect_url">
							  <input type="hidden" value="'. $this->options["ttgps_drp_status"] .'" name="post_status">
							  <input type="hidden" value="'. $this->options["ttgps_chk_notifyfield"] .'" name="notify_flag">
							  
							  <input type="hidden" value="'. $this->options["ttgps_chk_captchafield"] .'" name="capf">
							  <input type="hidden" value="'. $this->options["ttgps_chk_captchafield_req"] .'" name="capr">
							  
							  <input type="hidden" value="'. $to_mail .'" name="to_email">
							  <input type="hidden" name="ttgps_form_submitted" value="1" />' . 
                                '</section>
                                <section id="buttons">
                                        <input type="reset" name="reset" id="resetbtn" class="resetbtn" value="Reset">
                                        <input type="submit" name="submit" id="submitbtn" class="submitbtn" tabindex="7" value="Submit Post">
                                        <br style="clear:both;">
                                </section>
                            </div>
                        </form>';
        return $template_str ; 
    }
    
    public function ttgps_process_submit_form(){
	submit_post_function();
    } // End of function

} // End of Class

$ttgpsObj = new TT_GuestPostSubmit();

?>
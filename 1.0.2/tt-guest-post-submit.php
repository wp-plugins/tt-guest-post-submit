<?php
/*
Plugin Name: TT Guest Post Submit
Plugin URI: http://www.knowhowto.com.au/use-tt-guest-post-submit-plugin-wordpress/
Description: Enables your visitors to submit posts and images from anywhere on your site.
Tags: add post, content submission, guest blog, guest blogging, guest posting, wordpress guest post, post, submit post, submit, guest, guest post, admin, anonymous post, guest author, guest author plugin, post from front end, visitor post, captcha, secured post submit, user submitted post
Author: Rashed Latif
Author URI: http://www.knowhowto.com.au/rashed-latif
Donate link: http://www.knowhowto.com.au/donate
Requires at least: 3.5
Tested up to: 3.8
Version: 1.0.1
Stable tag: 1.0.1
License: GPL v2
*/

class TT_GuestPostSubmit{
    
    public function __construct(){
        wp_enqueue_style('ttgps-style', plugins_url('ttgps-style.css',__FILE__));   
        $this->enable_shortcode();
    }
    
    public function enable_shortcode(){
        add_shortcode('tt-submit-post', array($this, 'tt_guest_submit_post_shortcode') );
    }
    
    public function tt_guest_submit_post_shortcode($atts){
        
        session_start();
        $string = '';
        for ($i = 0; $i < 5; $i++) {
            $string .= chr(rand(97, 122));
        }
        $_SESSION['rootpath'] = str_replace('/wp-content/themes', '', get_theme_root()) .'/wp-blog-header.php';
        $_SESSION['random_code'] = $string;
        extract(shortcode_atts(array(
                            //'cat' => '1',
                            'author' => '1',
                            //'redirect_url' => get_bloginfo('home'),
                            'redirect_url' => get_permalink(),
                            ), $atts )
                );
        
        if (is_user_logged_in()){
            $author = get_current_user_id();    
        }

        $template_str = '<form id="ttgps-form" action="'. plugin_dir_url("tt-guest-post-submit.php") .'tt-guest-post-submit/tt-guest-post-submit-submit.php" method="post" enctype="multipart/form-data">
                            <div id="wrapping" class="clearfix">
                                <section id="aligned">
                                        <input  type="text" class="txtinput" id="title" name="title" title="Please Enter a Post Title" x-moz-errormessage="Please Enter a Post Title" size="72" required="required" placeholder="' . 'Post Title Here' . '">'
                                        . wp_nonce_field() .
                                        '<textarea class="txtblock" name="content" title="Please Enter Contents" x-moz-errormessage="Please Enter Contents" rows="15" cols="72" required="required" placeholder="' . 'Write Your Post Contents ' . '"></textarea>'
                                        . wp_dropdown_categories('show_option_none=Select Category...&name=catdrp&taxonomy=category&hide_empty=0&echo=0') .
                                        
                                        '<input type="text" class="txtinput" id="tags" name="tags" size="72" placeholder="' . 'Comma Separated Tags' . '">
                                        <input type="text" class="txtinput" title="Please Enter Author Name" x-moz-errormessage="Please Enter Author Name"  id="author"  name="author" size="72" required="required" placeholder="' . 'Your name here' . '">       
                                        <input type="email" class="txtinput" title="Please Enter a Valid Email Address" x-moz-errormessage="Please Enter a Valid Email Address " id="email" name="email" size="72" required="required" placeholder="' . 'Your Email address Here' . '">
                                        <input type="text" class="txtinput" id="site" name="site" size="72" placeholder="' . 'Your Website Address Here' . '">
                                        <p id="fi-title">Upload Featured Image</p>
                                        <div class="featured-img">
                                            <input name="featured-img" type="file" id="featured-img"><br>
                                        </div>' .
                                        '<img id="captcha-code" src="'. plugin_dir_url("tt-guest-post-submit.php") . 'tt-guest-post-submit/captcha.php' .'" />
                                        <input type="text" class="" title="Please Enter Correct Captcha Code" x-moz-errormessage="Please Enter Correct Captcha Code" id="code" name="code" size="30" pattern="'. $_SESSION['random_code'] . '" required="required" placeholder="' . 'Type the word' . '">' .
                                        '<input type="hidden" value="'. $author .'" name="authorid">
                                        <input type="hidden" value="'. $redirect_url .'" name="redirect_url">' . 
                                '</section>
                                
                                <section id="buttons">
                                        <input type="reset" name="reset" id="resetbtn" class="resetbtn" value="Reset">
                                        <input type="submit" name="submit" id="submitbtn" class="submitbtn" tabindex="7" value="Submit Post">
                                        <br style="clear:both;">
                                </section>
                            </div>
                        </form>';
                        
        return $template_str;
    } //End of Function "tt_guest_submit_post_shortcode"
} //End of class

add_action('wp', 'initiate_ttgsp_object');
function initiate_ttgsp_object(){
	$ttgspObj = new TT_GuestPostSubmit();
}

?>
<?php

    function submit_post_function(){
    
        if ((isset($_POST['capf']) && $_POST['capf']== "on") && (isset($_POST['capr']) && $_POST['capr'] == "on")){    
            $valid = false;
                if ( isset( $_COOKIE['Captcha'] ) ) {
                    list( $hash, $time ) = explode( '.', $_COOKIE['Captcha'] );
                    
                    // The code under the md5 first section needs to match the code
                    // entered in easycaptcha.php
                    if ( md5( 'HDBHAYYEJKPWIKJHDD' . $_REQUEST['ttgps_captcha'] . $_SERVER['REMOTE_ADDR'] . $time ) != $hash ) {
                            $abortmessage = __('Captcha code is wrong. Go back and try to get it right or reload to get a new captcha code.', 'ttgps_text_domain');
                            wp_die( $abortmessage );
                            exit;
                    }elseif (( time() - 5 * 60 ) > $time ){
                            $abortmessage = __('Captcha timed out. Please go back, reload the page and submit again.', 'ttgps_text_domain');
                            wp_die( $abortmessage );
                            exit;
                    }else{
                            // Set flag to accept and store user input
                            $valid = true;
                    }
                } else {
                    $abortmessage = __('No captcha cookie given. Make sure cookies are enabled.', 'ttgps_text_domain');
                    wp_die( $abortmessage );
                    exit;
                } // End of if (isset($_COOKIE['Captcha']))
        }
        else{
	    
            $valid = true;
        }
	
	//Checking Filtered Key words//
	if(isset($_POST['enable_filter']) && $_POST['enable_filter']=="on"){
	    
	    $filter_array = explode(',', $_POST['filter_items']);
	    $filtered_words_found = array_filter($filter_array, 'filtered_word_check');
	    if(count($filtered_words_found)>0){
		$abortmessage = __('Following Filtered Messeged are found in your Post. Please go back and Edit your Post before submit');
		$abortmessage .= "<br><br> <strong>";
		$abortmessage .= __('Filtered Words List: ');
		$abortmessage .=  implode(', ', $filtered_words_found ) . "</strong>";
		wp_die($abortmessage);
	    }
	}
	//====================================//
	if ( $valid ) {
	    
	    $title = isset($_POST["title"]) ? $_POST["title"] : "";
	    $content = isset($_POST["content"]) ? $_POST["content"] : "";
	    $tags = isset($_POST["tags"]) ? $_POST["tags"] : "";
	    $author = isset($_POST["author"]) ? $_POST["author"] : "";
	    $email = isset($_POST["email"]) ? $_POST["email"] : "";
	    $site = isset($_POST["site"]) ? $_POST["site"] : "";
	    $authorid = isset($_POST["authorid"]) ? $_POST["authorid"] : "" ;
	    if(isset($_POST['catdrp'])){
	    $category = $_POST['catdrp']==-1 ? array(1) : array($_POST['catdrp']);
	    }else{
	    $category = "";	
	    }
	    $redirect_location = isset($_POST["redirect_url"]) ? $_POST["redirect_url"] : "";
            $to_email = isset($_POST["to_email"]) ? $_POST["to_email"] : "";

            //$nonce=$_POST["_wpnonce"];
	    $poststatus = $_POST["post_status"];
	
	    if (isset($_POST['submit'])){
		$new_post = array(
		    'post_title'    => $title,
		    'post_content'  => $content,
		    'post_category' => $category,  // Usable for custom taxonomies too
		    'tags_input'    => $tags,
		    'post_status'   => $poststatus,           // Choose: publish, preview, future, draft, etc.
		    'post_type' => 'post',  //'post',page' or use a custom post type if you want to
		    'post_author' => $authorid //Author ID
		);
		
		$pid = wp_insert_post($new_post);
		add_post_meta($pid, 'author', $author, true);
		add_post_meta($pid, 'author-email', $email, true);
		add_post_meta($pid, 'author-website', $site, true);
		    
		if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );

                if ( $_FILES ) {
                    $files = $_FILES['featured-img'];
                    foreach ($files['name'] as $key => $value) {
                        if ($files['name'][$key]) {
                            $file = array(
			    'name'     => $files['name'][$key],
                            'type'     => $files['type'][$key],
                            'tmp_name' => $files['tmp_name'][$key],
                            'error'    => $files['error'][$key],
                            'size'     => $files['size'][$key]
                            );
 
                            $_FILES = array("featured-img" => $file);
                                
                            $counter = 1;    
                            foreach ($_FILES as $file => $array) {
                                if($counter == 1){
                                    $newupload = insert_attachment($file,$pid, true);
                                }else{
                                    $newupload = insert_attachment($file,$pid, false);    
                                }
                                ++$counter;
                            }   // End of inner foreach
                        }       // End of if
                    }           // End of outer foreach
                }               // End of if($_FILES)
            }                   // End of if (isset($_POST['submit']))
	    
            if($_POST['notify_flag']=="on"){
                ttgps_send_confirmation_email($to_email);
            }
            
	    // Redirect browser to review submission page
	    //$redirectaddress = ( empty( $_POST['_wp_http_referer'] ) ? site_url() : $_POST['_wp_http_referer'] );
	    $redirectaddress = ( !empty( $redirect_location ) ? $redirect_location : $_POST['_wp_http_referer'] );
            wp_redirect( add_query_arg( __('submission_success','ttgps_text_domain'), '1', $redirectaddress ) );
	    exit;
	} // End of if ($valid)
    }
    
    function filtered_word_check($var){
	if(strpos(" ".$_POST["content"], $var)){
	    return true;
	}
    }
    
    function insert_attachment($file_handler, $post_id, $setthumb) {
 
        // check to make sure its a successful upload
        if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();
       
        require_once(ABSPATH . "wp-admin" . '/includes/image.php');
        require_once(ABSPATH . "wp-admin" . '/includes/media.php');
       
        $attach_id = media_handle_upload( $file_handler, $post_id );
       
        if ($setthumb) update_post_meta($post_id,'_thumbnail_id',$attach_id);
        return $attach_id;
    }
    
    function check_and_set_value($val){
	if(isset($_POST[$val])){
	    return $_POST[$val];
	}else{
	    return "";
	}
	
    }
    
    function ttgps_send_confirmation_email($to_email) {

        $headers = 'Content-type: text/html';
        $message = __('A user submitted a new post to your Wordpress site database.','ttgps_text_domain').'<br /><br />';
        $message .= __('Post Title: ','ttgps_text_domain') . check_and_set_value('title') ;
        $message .= '<br />';
        $message .= '<a href="';
        $message .= add_query_arg( array(
                                'post_status' => $_POST["post_status"],
                                'post_type' => 'post' ),
                                admin_url( 'edit.php' ) );
        $message .= '">'.__('Moderate new post', 'ttgps_text_domain').'</a>';
        $email_title = htmlspecialchars_decode( get_bloginfo(), ENT_QUOTES ) . __(" - New Post Added: ", "ttgps_text_domain") . htmlspecialchars( check_and_set_value('title') );
        // Send e-mail
        wp_mail( $to_email, $email_title, $message, $headers );
        
        
    }


?>
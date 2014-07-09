<?php

    function submit_post_function(){
    
        if ($_POST['capf'] == "on" && $_POST['capr'] == "on"){    
            $valid = false;
            // Check if captcha text was entered
            if ( empty( $_POST['ttgps_captcha'] ) )  {
                wp_die( 'Captcha code is missing. Go back and provide the code.' );
                exit;
            } else {
            // Check if captcha cookie is set
                if ( isset( $_COOKIE['Captcha'] ) ) {
                    list( $hash, $time ) = explode( '.', $_COOKIE['Captcha'] );
                    
                    // The code under the md5 first section needs to match the code
                    // entered in easycaptcha.php
                    if ( md5( 'HDBHAYYEJKPWIKJHDD' . $_REQUEST['ttgps_captcha'] . $_SERVER['REMOTE_ADDR'] . $time ) != $hash ) {
                            $abortmessage = 'Captcha code is wrong. Go back ';
                            $abortmessage .= 'and try to get it right or reload ';
                            $abortmessage .= 'to get a new captcha code.';
                            wp_die( $abortmessage );
                            exit;
                    }elseif (( time() - 5 * 60 ) > $time ){
                            $abortmessage = 'Captcha timed out. Please go back, ';
                            $abortmessage .= 'reload the page and submit again.';
                            wp_die( $abortmessage );
                            exit;
                    }else{
                            // Set flag to accept and store user input
                            $valid = true;
                    }
                } else {
                    $abortmessage = 'No captcha cookie given. Make sure ';
                    $abortmessage .= 'cookies are enabled.';
                    wp_die(	$abortmessage );
                    exit;
                } // End of if (isset($_COOKIE['Captcha']))
            } // End of if (empty( $_POST['ttgps_captcha']))
        }
        else{
            $valid = true;
        }
	if ( $valid ) {
	    
	    $title = $_POST["title"];
	    $content = $_POST["content"];
	    $tags = $_POST["tags"];
	    $author = $_POST["author"];
	    $email = $_POST["email"];
	    $site = $_POST["site"];
	    $authorid = $_POST["authorid"];
	    $category = ($_POST['catdrp']==-1) ? array(1) : array($_POST['catdrp']);
	    $redirect_location = $_POST["redirect_url"];
            $to_email = $_POST["to_email"];

            $nonce=$_POST["_wpnonce"];
	    $poststatus = $_POST["post_status"];
	
	    if (isset($_POST['submit'])){
		if (! wp_verify_nonce($nonce) ) die('Security check');
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
            wp_redirect( add_query_arg( 'submission_success', '1', $redirectaddress ) );
	    exit;
	} // End of if ($valid)
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
    
    function ttgps_send_confirmation_email($to_email) {

        $headers = 'Content-type: text/html';
        $message = 'A user submitted a new post to your ';
        $message .= 'Wordpress site database.<br /><br />';
        $message .= 'Post Title: ' . $_POST['title'] ;
        $message .= '<br />';
        $message .= '<a href="';
        $message .= add_query_arg( array(
                                'post_status' => $_POST["post_status"],
                                'post_type' => 'post' ),
                                admin_url( 'edit.php' ) );
        $message .= '">Moderate new post</a>';
        $email_title = htmlspecialchars_decode( get_bloginfo(), ENT_QUOTES ) . " - New Post Added: " . htmlspecialchars( $_POST['title'] );
        // Send e-mail
        wp_mail( $to_email, $email_title, $message, $headers );
        
        
    }


?>
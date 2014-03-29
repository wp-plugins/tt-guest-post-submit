<?php
    session_start();
    require_once($_SESSION['rootpath']);
    $title = $_POST["title"];
    $content = $_POST["content"];
    $tags = $_POST["tags"];
    $author = $_POST["author"];
    $email = $_POST["email"];
    $site = $_POST["site"];
    $authorid = $_POST["authorid"];
    $category = ($_POST['catdrp']==-1) ? array(1) : array($_POST['catdrp']);
    $redirect_location = $_POST["redirect_url"];
    $nonce=$_POST["_wpnonce"];
    
    if(isset($_POST['submit'])) {
        
        if (! wp_verify_nonce($nonce) ) die('Security check'); 
        $new_post = array(
            'post_title'    => $title,
            'post_content'  => $content,
            'post_category' => $category,  // Usable for custom taxonomies too
            'tags_input'    => $tags,
            'post_status'   => 'pending',           // Choose: publish, preview, future, draft, etc.
            'post_type' => 'post',  //'post',page' or use a custom post type if you want to
            'post_author' => $authorid //Author ID
        );
        
        $pid = wp_insert_post($new_post);
        add_post_meta($pid, 'author', $author, true);
        add_post_meta($pid, 'author-email', $email, true);
        add_post_meta($pid, 'author-website', $site, true);
                
        if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
                
        $uploadedfile = $_FILES['featured-img'];
        $upload_overrides = array( 'test_form' => false );
        $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
                
        if ($movefile){
            $wp_filetype = $movefile['type'];
            $filename = $movefile['file'];
            $wp_upload_dir = wp_upload_dir();
            $attachment = array(
                        'guid' => $wp_upload_dir['url'] . '/' . basename( $filename ),
                        'post_mime_type' => $wp_filetype,
                        'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                        'post_content' => '',
                        'post_status' => 'inherit'
                    );
            $attach_id = wp_insert_attachment( $attachment, $filename, $pid);
                    
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
            wp_update_attachment_metadata( $attach_id, $attach_data );
            set_post_thumbnail($pid, $attach_id);     
        }
        header("Location: $redirect_location");
    }  
?>

=== Plugin Name ===
Contributors: rashed.latif
Tags: add post, content submission, guest blog, guest blogging, guest posting, wordpress guest post, post, submit post, submit, guest, guest post, admin, anonymous post, guest author, guest author plugin,post from front end, visitor post, captcha, secured post submit, user submitted post
Donate link: http://www.knowhowto.com.au/donate
Requires at least: 3.0.1
Tested up to: 3.8.1
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Submit your post and featured image without registering and logged in.

== Description ==

TT Guest Post Submit is a plugin that enables you to submit posts with the featuted image as a guest or visitor. Author doesn't have to log in or register to the site. This plugin is very handy for the blog sites where different authors are encouraged to submit posts.
Once the post is submitted it has to be 
Features included in this plugins are:

* Anyone can post from anywhere in the site
* Category selection
* Attach featured image
* Captcha enabled which protects spams
* Use shortcode to display the submission form anywhere
* Post submissions may include title, tags, category, author, url, post and image
* Redirect user to current page after successful post submission
* HTML5 submission form with streamlined CSS styles

= How to Use: =

1. Plugin needs to be downloaded and installed.
2. From admin menu create a post or page where you want this post submission form to be displayed.
3. Use the shortcode *`[tt-submit-post]`* in the post or page.
4. Submission form will be available on that page or post. Form has got few fields where some of them are required.

* Title (Required)
* Post Content (Required)
* Select Category (Not required - If no category is selected the first category in the blog will be used as default category for that post)
* Comma seperated tags (Not required)
* Authors Name (Required - This name will be displayed as author name. But need to tweak code in theme which is explained below. Otherwise "admin" is the default author)
* Email address (Required)
* Website Address (Not Required)
* Featured Image (Not Required)
* Captcha (Required)
* Reset button is used to clear any inputs in the form fields.
* Submit button is used to submit post.
5. Once the post is submitted the admin will moderate the post and if admin publishes the post it will be available in the blog

= How to display author name in the post =

By default "admin" will be displayed as author name. If the guest provides the name and admin decides to display actual author's name in the post then admin needs to chnage the code in theme that the site is using.
But if you know what you are doing then chnage the code otherwise you may end up with something wrong. Depending on which theme you are using it may differ on which file you need to make change.
For example if you use twentytwelve theme then you need to modify in function.php.

* First open function.php
* Find the function twentytwelve_entry_meta()
* Find the following code
`$author = sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>', esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
	esc_attr( sprintf( __( 'View all posts by %s', 'twentytwelve' ), get_the_author() ) ), get_the_author()`
* replace the last bit of code which is `get_the_author()` with following code
`!empty(get_post_custom_values('author', get_the_ID())[0]) ?  get_post_custom_values('author', get_the_ID())[0] : get_the_author()`

Thats all you need to do and that will display the name that guest author provided as the author name.

== Installation ==

1. Unzip & upload the plugin directory inside your /wp-content/plugins/ directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Plugin is ready to use. In which ever post or page you want this submit form to be available the shortcode needs to be applied there.

== Screenshots ==

1. TT Guest Post Submit Form

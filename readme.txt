=== Plugin Name ===
Contributors: rashed.latif
Tags: add post, content submission, guest blog, guest blogging, guest posting, wordpress guest post, post, submit post, submit, guest, guest post, admin, anonymous post, guest author, guest author plugin,post from front end, visitor post, captcha, secured post submit, user submitted post
Donate link: http://www.knowhowto.com.au/donate
Requires at least: 3.0.1
Tested up to: 3.9.1
Stable tag: 2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Submit your post as guest user. Add featured image and other media to the post. Easy to use but very usefull.

== Description ==

*I Appreciate if you please give reviews and any suggestions after using this plugin.*

TT Guest Post Submit is a plugin that enables you to submit posts with the featuted image as a guest or visitor. Author doesn't have to log in or register to the site. This plugin is very handy for the blog sites where different authors are encouraged to submit posts.

Features included in this plugins are:

* Plugin option page - User can customize settings.
* Email notification to admin eamil or any other email after successfull post submission.
* Customized Post Submission message.
* Field Selection ability.
* Anyone can post from anywhere in the site
* Category selection
* Attach featured image. More than one image and other media items can be added to the post.
* Captcha enabled which protects spams
* Use shortcode to display the submission form anywhere
* Post submissions may include title, tags, category, author, url, post and image
* Redirect user to current page or any other page (which can be set from option page) after successful post submission
* HTML5 submission form with streamlined CSS styles

= How to Use: =

First I will explain how TT Guest Post Submit Options page works.
There are two section on opion page. 

1. General Settings

* Send Notification via Email: If selected then notification email will be sent to admin email or other selected email.
* Email for Notification: Email address for post submission notification. If empty then email will be sent to admin email.
* Post Submit Confirmation Message: Custom message after post is submitted successfully.
* Post Submit Failure Message: Custom message after post is not submitted successfully. 
* Redirect to: Url for redirection. If empty page will be redirected to submit page.
* Publish Status: Selection for post publish status.
* Guest Account: Selection for post Author. This dropdown menu will display all registered author name. If a user is not logged into the site then selected guest account will be used as author. 

2. Field Selection

This section will give ability to select which field are to be displayed in the site. Also there is an option to select if a field is a required field or not. If a required check box next to any field name is checked then that field will must be filled by the user before the post submission.

Here is use of the plugin:
 
1. Plugin needs to be downloaded and installed.
2. From admin menu create a post or page where you want this post submission form to be displayed.
3. Use the shortcode *`[tt-submit-post]`* in the post or page.
4. Submission form will be available on that page or post. Form has got few fields. From plugin option page you can select which fields are to be displayed and also if the selected field is a required field. Available fields are 

* Title
* Post Content
* Select Category
* Comma seperated tags
* Authors Name (This name will be displayed as author name. But need to tweak code in theme which is explained below. Otherwise "admin" is the default author)
* Email address
* Website Address
* Featured Image (Multiple images or media files can be uploaded. FOR FEATURED IMAGES select the image that you want it to be as featured image first while selecting multiple files)
* Captcha
* Reset button is used to clear any inputs in the form fields.
* Submit button is used to submit post.
5. Once the post is submitted the post needs to be moderated if publish status is set to "Pending" or "Draft" in the plugin option page. If publish status is set to "Published" then it will be available on the site straight away.

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

== Changelog ==

= 1.0.1 =
* A potential security threat has been resolved

= 1.0.2 =
* Changed code to resolve a warning that some users were getting

= 2.0 =

* Plugin option page is implemented for customizing the plugin
* Email notification can be sent after post submission.
* Ability to set customized post submission message.
* Ability to set redirection url after post submission.
* Field selection ability.
* Multiple files can be uploaded for a post. Featured image can be set as well.
* Better Captcha image.

= 2.1 =

* Published status was not working. It'd fixed now.
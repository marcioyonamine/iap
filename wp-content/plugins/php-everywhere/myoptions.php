<?php
/** Step 1. */
function php_everywhere_menu() {
	add_options_page( 'PHP Everywhere', 'PHP Everywhere', 'manage_options', 'php-everywhere-identifier', 'php_everywhere_options' );
}

/** Step 3. */
function php_everywhere_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( __('You do not have sufficient permissions to access this page.', 'phpeverywhere') ) );
	}
	
	if(isset($_POST['php_everywhere_roles']))
	{
	update_option('php_everywhere_option_roles',$_POST['php_everywhere_roles']);
	}

	?>
<div class="wrap">
<h1>PHP Everywhere</h1>
<p><?php _e('Thanks for using PHP Everywhere. If you have any questions, feel free to ask <a href="mailto:alexandria96gmx.de">me</a>.<br />I created this plugin because I have not found a Wordpress PHP plugin which is simple to use and provides a good user experience while being able to use PHP or HTML in Posts, Pages or Widgets.', 'phpeverywhere'); ?></p>
<h1><?php _e('User role management', 'phpeverywhere')?></h1>
<p><?php _e('Who can modify the PHP in posts and pages?', 'phpeverywhere')?></p>
<form method="post">
<select name="php_everywhere_roles">
  <option value="everyone"><?php _e('Administrator, editor, author', 'phpeverywhere')?></option>
  <option value="admin" <?php if(get_option('php_everywhere_option_roles')=='admin'){echo('selected');}?>><?php _e('Administrator only', 'phpeverywhere')?></option>
</select>
<?php submit_button(); ?>
</form> 
<h1><?php _e('Usage', 'phpeverywhere')?></h1>
<h3><?php _e('Widgets', 'phpeverywhere')?></h3>
<p><?php _e('Simply activate the <pre>PHP + HTML</pre> Widget. in your sidebar and paste your PHP code including the PHP Tags like this:
<pre>&lt;?php  echo("Hello, World!"); ?&gt;</pre>
You code may contain HTML Elements or have multiple lines.', 'phpeverywhere')?></p>
<h3><?php _e('Posts & Pages', 'phpeverywhere')?></h3>
<p><?php _e('Edit or create a new post or page and simply put your PHP Code including the PHP Tags into the side options_box, which is labeled "PHP Everywhere". Then put the <pre>[php_everywhere]</pre> shortcode where you want the code to appear. Your code may contain multiple lines or HTML Tags.', 'phpeverywhere')?></p>
<h3><?php _e('Multiple PHP instances', 'phpeverywhere')?></h3>
<p><?php _e('If you want to use multiple PHP instances use the shortcode with the instance parameter like this:', 'phpeverywhere')?><br><pre>[php_everywhere instance="1"]</pre><br><?php _e('Your PHP snippet should look like this:', 'phpeverywhere')?><br><pre>&lt;?php
if($instance==&quot;1&quot;)
{
echo(&quot;Number one!&quot;);
}
if($instance==&quot;2&quot;)
{
echo(&quot;Number two!&quot;);
}
?&gt;</pre></p>
<h1><?php _e('Changelog', 'phpeverywhere')?></h1>
<p><?php _e('Go to <a href="http://www.alexander-fuchs.net/php-everywhere/" target="_blank">http://www.alexander-fuchs.net/php-everywhere/</a> to view the changelog and more.', 'phpeverywhere')?></p>
<h1><?php _e('Like this Plugin? Support me :)', 'phpeverywhere')?></h1>
<h3><?php _e('Follow me', 'phpeverywhere')?></h3>
<p><!-- Fügen Sie dieses Tag in den head-Abschnitt oder direkt vor dem schließenden body-Tag ein. -->
<script src="https://apis.google.com/js/platform.js" async defer>
  {lang: 'en'}
</script><!-- Fügen Sie dieses Tag an der Stelle ein, an der die Widget erscheinen soll. -->
<div class="g-person" data-href="//plus.google.com/u/0/114963560467924796169" data-layout="landscape" data-rel="author"></div>
<iframe src="//www.facebook.com/plugins/likebox.php?href=https%3A%2F%2Fwww.facebook.com%2Fpages%2FAlexander-Fuchs%2F211377875552443&amp;width=300&amp;height=62&amp;colorscheme=light&amp;show_faces=false&amp;header=true&amp;stream=false&amp;show_border=true&amp;appId=141996939194259" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:300px; height:62px;" allowTransparency="true"></iframe></p>
<h3><?php _e('Subscribe', 'phpeverywhere')?></h3>
<p><?php _e('Please visit <a href="http://www.alexander-fuchs.net" target="_blank">http://www.alexander-fuchs.net</a> to subscribe to my newsletter.', 'phpeverywhere')?></p>
<h3><?php _e('Donate', 'phpeverywhere')?></h3>
<p><form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="VXCVDFMDZCX78">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1" height="1">
</form>
</p>
</div>
<?php
}
?>
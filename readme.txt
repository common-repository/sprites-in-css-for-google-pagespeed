==== Plugin Name ===
Contributors: anduriell
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5CME78CMJ5H5L
Tags: sprite, sprite css, images, sprites, SEO, pagespeed, yslow
Requires at least: 3.9.1
Tested up to: 3.9.1
Stable tag: 2.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin create Sprites CSS within any wordpress site.

== Description ==

This plugin create CSS Sprites for wordpress blogs.
It uses my cloud external servers (in http://api.arturoemilio.com ) to create the sprites as they are sent to the blog thru push request. It's compatible with caching plugins like [Autoptimize](http://wordpress.org/plugins/autoptimize/), [Wp Supercache](https://wordpress.org/plugins/wp-super-cache/), [W3 Total Cache](https://wordpress.org/plugins/w3-total-cache/).
It uses javascript to create responsive sprites, in case the browser hasn't got javascipt enabled it will degrade gracefully to the same page without the sprites applied.

This plugin is yet in beta stage, althougt it is working fine in [this site](http://www.arturoemilio.es/) it hasn't being tested in all the possible enviroments.

Please keep in mind that is still in delevopment so if you find any bug please report it.

The actual process is as follows:
 * Your blog send the information about the actual web as you see in your browser to my external cloud service.
 * Then it will create an sprite with the information that your Wordpress installation will download. The size of this sprite will depend on the images required to use. 
 * Depending on the procesing queue after some time will be created a new sprite much more small in size and then your Wordpress installation will replace your old sprite with.
 
Please keep in mind that:
 * You will be sendind the request to my servers to allow them to create the sprite, this process it is impossible to do it in a shared server.
 * Depending on the pending requests it can take time to create the sprites. Your blog will be not be affected by this, althouh it may take time to show the actual sprites. The first sprite usually takes second, but the compressed one take alround 5 minutes each.
 * The plugin does not send pages inside wp-admin
 * The plugin sends the requests inside the ob_start() event. This means it is send before the user can enter any data in the webpage so no sensible data should be accesed. However the data is not stored in the remote servers in any case and all the data is destroyed as soon as it is used because of the space limitations in the VPS.
 * Because is using a limited resources it can take time to create the sprites depending on the actual queue. 

Here are some [screenshot with a long explanation of what this plugin does in Spanish].(http://www.arturoemilio.es/project/css-sprite-para-google-pagespeed/)

Privacy disclaimer

Personal Information Collection (PIC) Statement
Information collected, including email address and blog url, will only be used for the purpose of processing  or general enquires related to this plugin, and will be treated in confidence and not be disclosed to any other party. If you wish to access or correct your personal data, please contact thru this contact form [Contact](http://www.arturoemilio.es/contactar/)
No other data is kept in the server, being destroyed as soon as it's being processed and it's .
This privacy policy may change from time to time particularly as new rules, regulations and industry codes are introduced.

Limitation of Liability

You agree by accessing the Service that under no circumstances or any theories of liability under international or civil, common or statutory law including but not limited to strict liability, negligence or other tort theories or contract, patent or copyright laws, will the developer and this service provider be liable for damages of any kind occurring from the use of the Service or any information, goods or services obtained on the Service including direct, indirect, consequential, incidental, or punitive damages (even if the developer and provider of this service has been advised of the possibility of such damages), to the fullest extent permitted by law.
The provider of this service will be not respon
I do not vet and I am not responsible for any information processed for this service. All content is viewed and used by you at your own risk and we do not warrant the accuracy or reliability of any of the information. 

Disclaimer of warranty and liability from GPL licensing:

Disclaimer of Warranty.

THERE IS NO WARRANTY FOR THE PROGRAM, TO THE EXTENT PERMITTED BY APPLICABLE LAW. EXCEPT WHEN OTHERWISE STATED IN WRITING THE COPYRIGHT HOLDERS AND/OR OTHER PARTIES PROVIDE THE PROGRAM "AS IS" WITHOUT WARRANTY OF ANY KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE. THE ENTIRE RISK AS TO THE QUALITY AND PERFORMANCE OF THE PROGRAM IS WITH YOU. SHOULD THE PROGRAM PROVE DEFECTIVE, YOU ASSUME THE COST OF ALL NECESSARY SERVICING, REPAIR OR CORRECTION.

Limitation of Liability.

	IN NO EVENT UNLESS REQUIRED BY APPLICABLE LAW OR AGREED TO IN WRITING WILL ANY COPYRIGHT HOLDER, OR ANY OTHER PARTY WHO MODIFIES AND/OR CONVEYS THE PROGRAM AS PERMITTED ABOVE, BE LIABLE TO YOU FOR DAMAGES, INCLUDING ANY GENERAL, SPECIAL, INCIDENTAL OR CONSEQUENTIAL DAMAGES ARISING OUT OF THE USE OR INABILITY TO USE THE PROGRAM (INCLUDING BUT NOT LIMITED TO LOSS OF DATA OR DATA BEING RENDERED INACCURATE OR LOSSES SUSTAINED BY YOU OR THIRD PARTIES OR A FAILURE OF THE PROGRAM TO OPERATE WITH ANY OTHER PROGRAMS), EVEN IF SUCH HOLDER OR OTHER PARTY HAS BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES.

== Installation ==

This is pretty standard.

1. Upload Css Sprite plugin to the `/wp-content/plugins/` directory or Install it thru the admin panel.
2. Activate it
3. In General-> CSSSPRITE use a valid email address to register the blog.
4. You'll recibe a registration email with the link to activate the account and the token to introduce in the settings.
5. Enjoy the improvement in speed and SEO.


== Frequently Asked Questions ==

= Is this plugin free? =

Yes, it is free.

= Why should i register to use it if it's free? =

To avoid the service being abused. It could be used another methods but by this way you are also conscious that there are some limitations within the fair use.

= It takes too long to get the sprites.. (althougth the site loads fine i can't see the new sprites) =

That can happen if there are too many request by minute so it has to proccess the queue first. Nothing we can do there, just wait.

= I want to help = 
In that case you may invite me for a coffe using the donation button or clicking here: [Donate](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5CME78CMJ5H5)
also you may like the [facebook page](http://www.facebook.com/Desarrollo.web.Arturo.Emilo) and spread the word. Also you may give positive reviews int he wordpress repository.

= I have some questions = 

You may leave a message in wordpress boards,  
or leave a message in the facebook page (English or Spanish only please): [Arturo Emilio - Programación y Diseño](http://www.facebook.com/Desarrollo.web.Arturo.Emilo)
or send me a message usign this form: [Contact](http://www.arturoemilio.es/contactar/)


== Screenshots ==
1. NEW panel with information about the sprites requested and their status.
2. Option panel with the plugin set up.
3. Comparison between using sprites and don't. View the full report [here](http://gtmetrix.com/compare/ytmfW3Bh/jIbcMYqi)


== Changelog ==
= 2.9 =
* some minor improvement for enhanced readbility within the options panel.
* Added information about las message recieved from the blog. This is helpfull in cases like headers with 301 or 404 responses when the sprites are sent to the blog.

= 2.8 =
* Fix bug with complex urls.
= 2.7 =
* Impoved sprite request detection.
* Improved stability.

= 2.0 =
* -- Changes --
* Complete code rewrite to fix some pending bugs with queues and options.
* Fixed no caching when used ajax galleries where images may change order.
* Fixed problems with spaming the service when there are many visitors in the blog.
* Improved speed when requesting sprites, now is 80% faster.
* -- NEW Features added 
* Added control panel where you can see actually the request being made and different stats.
* Now the sprites are requested thru ccron events. Please make sure your theme can fire cron requests when a page is loaded.
* Generally improved speed in serving the sprites within the service.

= 1.4 =
* Fixed bug with registration process. Now you will be able to register.
= 1.2 =
* Fixed a nasty bug that prevented the plugin to work as expected. 
= 1.1 = 
* Corrections to comply the Wordpress Codex and Guidelines.
= 1.0 =
* First version

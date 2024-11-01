<?php	
if (!defined('ABSPATH')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	die('ERROR');
}
			global $wpdb;
			$table_name = $wpdb->prefix . 'sprites';
			
			$path = WP_CONTENT_DIR.'/cache/CSS_Sprite/';
			
			$opciones = json_decode(get_option('Sprites_AE'),1);

			if ($_POST["accion"] == 'clean') {
				
				css_clean($path);
				$table_name = $wpdb->prefix . 'sprites';
				$wpdb->query($wpdb->prepare( "TRUNCATE TABLE $table_name",$path));

				echo '<div id="message" class="updated fade">
				<p><strong>Cache Deleted.</strong></p>
				</div>' ;
			}elseif($_POST["accion"] == 'resend'){
				$web = array_filter(explode("\n", trim($_POST['exec'])), 'trim');	
				
				$ch = curl_init();
				curl_setopt($ch,CURLOPT_URL, 'http://api.arturoemilio.com/?email='.$opciones['email'].'&host='.get_site_url().'&resend=1');
				curl_setopt($ch,CURLOPT_POST, true);
				curl_setopt($ch,CURLOPT_POSTFIELDS, $post);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
				curl_setopt($ch, CURLOPT_HEADER, false); 
				$response = curl_exec($ch);  
				curl_close($ch);
				
				//echo  file_get_contents('http://api.arturoemilio.com/?email='.$opciones['email'].'&host='.get_site_url().'&resend=1'); 
			}elseif($_POST["accion"] == 'clean'){
					$text = trim($_POST['exec']);
					$textAr = explode("\n", $text);
					$textexec = array_filter($textAr, 'trim'); // remove any extra \r characters left behind
					$text = trim($_POST['weburl']);
					$textAr = explode("\n", $text);
					$textwebc = array_filter($textAr, 'trim'); // remove any extra \r characters left behind
				
			}elseif(isset($_POST['accion']) &&  $_POST['accion'] != 'none'){
				
				if(is_array($_POST)){
					
					foreach ($_POST as $key => $value) {
						$opciones[$key] = $value;
					}
					$opciones['exec'] = array_filter(explode("\n", trim($_POST['exec'])), 'trim'); 
					$opciones['web'] = array_filter(explode("\n", trim($_POST['web'])), 'trim');
					update_option('Sprites_AE', json_encode($opciones));
				
				?>
					<div id="message" class="updated fade">
						<p><strong>Saved.</strong></p>
					</div>
			<?php 
					$opciones = json_decode(get_option('Sprites_AE'),1);

				}
			} 
?>
			
			<?php 	
					$siz =  'The cache directory size is: ' . format_size(foldersize($path)); 
					?>	  					
<div class="wrap">
	
				<div style="width: 33%;float: left; display: inline-block;">
					<h2>CSSPRITE by Arturo Emilio </h2>
					<small><a href="http://arturoemilio.es">(click here to visit my website for support o questions)</a></small>
				</div>
				<div>
					<div style="width: 165px;float: left; display: inline-block;">
					<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
						<input type="hidden" name="cmd" value="_s-xclick">
						<input type="hidden" name="hosted_button_id" value="5CME78CMJ5H5L">
						<input type="image" src="https://www.paypalobjects.com/es_ES/ES/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal. La forma rápida y segura de pagar en Internet.">
						<img alt="" border="0" src="https://www.paypalobjects.com/es_ES/i/scr/pixel.gif" width="1" height="1">
						</form>
					</div>
					<div style="width: 300px;float: left; display: inline-block;">
						<strong></strong></string><p>This will be used to improve the service, better servers and more of them to distribute the load.</p></strong>
					</div>
				</div>
					
				</style>
				<div class="clear"></div>

	<div class="tabs">
		
		<div class="tab">
			   <input type="radio" id="tab-1" name="configuracion" value = "" checked>
			   <label for="tab-1">Service Status & Stats</label>
			       <div class="content">			
			   			
			   			
			   			
			   			<div style="float:left;width:45%;display: inline-block;">
				   			<div><h3>Service Status</h3>
				   				<?php echo file_get_contents('http://cdn.arturoemilio.com/estatus.php'); ?>
				   			</div>
				   			<div >
				   				<h3>ACTIVATION STATUS</h3>
									<?php if($response){ ?>
									<small><?php  echo $response;?></small><br/>
									<?php } ?>
									<small><?php echo file_get_contents('http://cdn.arturoemilio.com/?email='.$opciones['email'].'&host='.get_site_url().'&token='.$opciones['token']); ?></small></br>
									<small>DB Version: <?php echo $opciones['version'];?></small>
				   			</div>
			   			</div>
			   			
			   			<div style="float:rigth;width:45%;display: inline-block;">
			   				<h3>Header from this Blog from las Request</h3>
			   				<small><?php echo file_get_contents('http://cdn.arturoemilio.com/?email='.$opciones['email'].'&host='.get_site_url().'&token='.$opciones['token'].'&last=1'); ?></small></br>
			   			</div>
			   			
				
						<div>
								<table style="width:100%;">
									<tr>
									  <th>Cron</th>
									  <th>Url</th> 
									  <th>Time</th>
									  <th>File</th>
									</tr>
							<?php 
								
									$eventos = array('api_sprite_externo');
									$crons = get_option('cron');
									if(is_array($crons)){
											foreach($crons as $time => $cron){
												if(!is_array($cron))continue;												
												foreach($cron as $name => $event){
													if(!in_array($name, $eventos)) continue;
													foreach($event as $id){
														$args = $id['args'];
														$cron[]['cron'] = $id;
														$cron[]['url'] = $args[0];
														$cron[]['time'] = date('Y-m-d H:i:s', $time);
														$cron[]['file']	=	$args[4];											
													}
												}				
											}
									}
								if(is_array($cron)){
									
									foreach ($cron as $key => $value) {
										
										if(empty($value['file'])) continue;
										
										$color = 'blanchedalmond';
										
										echo '<tr style="background-color: '.$color.';">';
										$array_row[4] =   $value['mensaje'];
										$array_row[5] = $value['ids'];
										echo '<td style="width:auto;">'.implode('</td><td style="width:auto;padding-right: 10px;padding-left: 10px;">',$array_row).'</td>';
										echo '</tr>';
									}							
								}else{
									echo '<tr><td style="width:auto;"></td><td style="width:auto;"></td><td style="width:auto;"></td><td style="width:auto;"></td></tr>';

								}
							?>
							
				</table>							
									<table style="width:100%;">
									<tr>
									  <th>File</th>
									  <th>Urls</th> 
									  <th>Request</th>
									  <th>Time </th>
									  <th>Message</th>
									  <th>RQ</th>
									</tr>
							<?php 
								$stat = $wpdb->get_results( "SELECT * FROM $table_name",ARRAY_A);
								if(count($stat) > 1){
									foreach ($stat as $key => $value) {
														if ($stats[$value['hash']])
																	$stats[$value['hash']]['urls'] .= ','.$value['urls']; 
														else
																$stats[$value['hash']] = $value;       
														
									}
									sort ($stats);
									foreach ($stats as $key => $value) {
										if(empty($value['ubicacion'])) continue;
										
										if($value['end'])
											$color = 'mediumseagreen';
										elseif($value['mensaje'])
											$color = 'darksalmon';
										else 
											$color = 'blanchedalmond';
										
										echo '<tr style="background-color: '.$color.';">';
										$array_row[0] = $value['hash'];	
										
									  $array_row[1] = str_ireplace(',', '<br/>', ltrim ($value['urls'],',')) ;	
										
										
										$array_row[2] = date('Y-m-d H:i:s', $value['start']);
										
										if($value['end'] && $value['start'])
											$array_row[3] = human_time_diff( $value['start'], $value['end']);
										else 
											$array_row[3] = 'Waiting...';
										
										$array_row[4] =   $value['mensaje'];
										$array_row[5] = $value['ids'];
										echo '<td style="width:auto;">'.implode('</td><td style="width:auto;padding-right: 10px;padding-left: 10px;">',$array_row).'</td>';
										echo '</tr>';
									}							
								}else{
									echo '<tr><td style="width:auto;"></td><td style="width:auto;"></td><td style="width:auto;"></td><td style="width:auto;"></td><td style="width:auto;"></td><td style="width:auto;"></td></tr>';

								}
							?>
							
				</table>
				
						</div>
				   </div>				
		</div>
				
		<div class="tab">
			 	<input type="radio" id="tab-2" name="configuracion" value = "X">
		 		<label for="tab-2">Settings</label>
			       <div class="content">			

						<form name="clean" method="post" action="<?php echo admin_url( "admin.php?page=".$_GET["page"] ) ?>">
						<hr>
						<button type="submit" name = "accion" class="button-primary" value="none" hidden="true" style="visibility: hidden;">_</button>
						<div style="display: inline-block;width: 45%;float: left;">	
							<h3>CLEAN CACHE DIRECTORY</h3>
							<p></p>To delete all the sprites already generated.<br/><b><?php echo $siz ?></b></p>
							<button type="submit" name = "accion" class="button-primary" value="clean" >Clean Caché</button>
							
						</div>
						<div style="display: inline-block;width: 45%;float: right;">
							<h3>EXCLUDE URLS WITH TEXTS:</h3>
							Strings in image url to not include in CSSPRITE (separated by colon ( , )):
							<textarea cols="50" rows="4" name="excluir"><?php echo $opciones['excluir']; ?></textarea>
						</div>
						<div class="clear"></div>
						<h3>ACTIVATION STATUS</h3>
						<?php if($response){ ?>
						<small><?php  echo $response;?></small><br/>
						<?php } ?>
						<small><?php echo file_get_contents('http://cdn.arturoemilio.com/?email='.$opciones['email'].'&host='.get_site_url().'&token='.$opciones['token']); ?></small></br>
						<div style="display: inline-block;width: 45%;">
								<h4>EMAIL ACTIVATION</h4>
								<p>
								<input type="text" name="email" value="<?php echo $opciones['email']; ?>" ><br/>
								<button type="submit" name = "accion" class="button-primary" value="email" >Register Email</button>
								<button type="submit" name = "accion" class="button-primary" value="resend" style="float: right;" >Resend Registration Email</button>
								</p>
						</div>
						<div style="display: inline-block;width: 45%;">
								<h4>TOKEN</h4>
								<p>
								<input type="text" name="token" value="<?php echo $opciones['token']; ?>" ><br/>
								<button type="submit" name = "accion" class="button-primary" value="token" style="float: right;">Activate Token</button>
								</p>

						</div>
						<div class="clear"></div>
						<h3>CDN Option</h3>
						Replace domain name if you want to use <b>PULL</b> CDN (the urls must be the same, only change allowed is the host):<br/>
						<input type="text" name="cdn" value="<?php echo $opciones['cdn']; ?>" ><br/>
					    <button type="submit" name = "accion" class="button-primary" value="cdn" >Save CDN</button>
					  <div class="clear"></div>
						<h3>CACHES REFRESHING<h3>
						<small> Here you can write the commands to execute after the sprites are retrieved from the server, mainly to refresh the caches.Each command in every line.</small>
						<div style="display: inline-block;width: 45%;float: left;">	
						<p>
							Here the SYSTEM commands (executed by shell_exec)<br/>
							<textarea cols="50" rows="4" name="exec"><?php if(is_array($opciones['exec'])) implode("\n", $opciones['exec']) ?></textarea>
						</p> 
						</div>
						<div style="display: inline-block;width: 45%;float: left;">	
						<p>
							Here the WEB URLS to call with CURL<br/>
							<textarea cols="50" rows="4" name="web"><?php if(is_array($opciones['web'])) echo implode("\n", $opciones['web']); ?></textarea>
						</p>
						</div>
						<button type="submit" name = "accion" class="button-primary" value="comandos" >Save Commands</button>

						</form>
						
						<div>
						<hr>
						<div>
						<p>This will be used to improve the service, better servers and more of them to distribute the load.<br />
						Also you may like the facebook page and share your thougths: <a href="https://www.facebook.com/Desarrollo.web.Arturo.Emilo">Facebook Page</a><br />
						With you find any problem you may lieave a message in wordpress forums or my facebook page.<br />
						Alternatively you may send me an email using <a href ="http://www.arturoemilio.es/contactar/"> this contact form.</a></p>
						<hr>
						<h3>Legal Disclaimers</h3>
						<p>
							Privacy disclaimer<br />
							Personal Information Collection (PIC) Statement
							Information collected, including email address and blog url, will only be used for the purpose of processing  or general enquires related to this plugin, and will be treated in confidence and not be disclosed to any other party. If you wish to access or correct your personal data, please contact thru this contact form [Contact](http://www.arturoemilio.es/contactar/)<br />
							No other data is kept in the server, being destroyed as soon as it's being processed and it's .<br />
							This privacy policy may change from time to time particularly as new rules, regulations and industry codes are introduced.<br />
							<br />
							<br />
							Limitation of Liability<br />
							You agree by accessing the Service that under no circumstances or any theories of liability under international or civil, common or statutory law including but not limited to strict liability, negligence or other tort theories or contract, patent or copyright laws, will the developer and this service provider be liable for damages of any kind occurring from the use of the Service or any information, goods or services obtained on the Service including direct, indirect, consequential, incidental, or punitive damages (even if the developer and provider of this service has been advised of the possibility of such damages), to the fullest extent permitted by law.<br />
							The provider of this service will be not respon<br />
							I do not vet and I am not responsible for any information processed for this service. All content is viewed and used by you at your own risk and we do not warrant the accuracy or reliability of any of the information.<br /> 
							<br />
							Disclaimer of warranty and liability from GPL licensing:<br />
							Disclaimer of Warranty.<br />
							THERE IS NO WARRANTY FOR THE PROGRAM, TO THE EXTENT PERMITTED BY APPLICABLE LAW. EXCEPT WHEN OTHERWISE STATED IN WRITING THE COPYRIGHT HOLDERS AND/OR OTHER PARTIES PROVIDE THE PROGRAM "AS IS" WITHOUT WARRANTY OF ANY KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE. THE ENTIRE RISK AS TO THE QUALITY AND PERFORMANCE OF THE PROGRAM IS WITH YOU. SHOULD THE PROGRAM PROVE DEFECTIVE, YOU ASSUME THE COST OF ALL NECESSARY SERVICING, REPAIR OR CORRECTION.<br />
							<br />
							Limitation of Liability.<br />
							IN NO EVENT UNLESS REQUIRED BY APPLICABLE LAW OR AGREED TO IN WRITING WILL ANY COPYRIGHT HOLDER, OR ANY OTHER PARTY WHO MODIFIES AND/OR CONVEYS THE PROGRAM AS PERMITTED ABOVE, BE LIABLE TO YOU FOR DAMAGES, INCLUDING ANY GENERAL, SPECIAL, INCIDENTAL OR CONSEQUENTIAL DAMAGES ARISING OUT OF THE USE OR INABILITY TO USE THE PROGRAM (INCLUDING BUT NOT LIMITED TO LOSS OF DATA OR DATA BEING RENDERED INACCURATE OR LOSSES SUSTAINED BY YOU OR THIRD PARTIES OR A FAILURE OF THE PROGRAM TO OPERATE WITH ANY OTHER PROGRAMS), EVEN IF SUCH HOLDER OR OTHER PARTY HAS BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES.<br />
		
						</p>
						</div>
			</div> 	
		</div> 
	</div> 
</div> 
<div class="clear"></div>
				<?php


function css_clean($path) {
		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::CHILD_FIRST);
		$result = array();
		foreach ($files as $file)
		{
				$file_extension = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
				if(file_exists($file->getRealPath()) && !is_dir($file->getRealPath())) unlink($file->getRealPath());
		
		}
}
function foldersize($path) {
		$total_size = 0;
		
		if (!file_exists($path)) 
					mkdir($path,0755,true);
				
		$files = scandir($path);
		$cleanPath = rtrim($path, '/'). '/';
		foreach($files as $t) {
			if ($t<>"." && $t<>"..") {
				$currentFile = $cleanPath . $t;
				if (is_dir($currentFile)) {
					$size = foldersize($currentFile);
					$total_size += $size;
				}else {
					$size = filesize($currentFile);
					$total_size += $size;
				}
			}
		}
	return $total_size;
}

function format_size($size) {
			$units = explode(' ', 'B KB MB GB TB PB');
			$mod = 1024;
			for ($i = 0; $size > $mod; $i++) {
				$size /= $mod;
			}
			$endIndex = strpos($size, ".")+3;
			return substr( $size, 0, $endIndex).' '.$units[$i];
}	
?>
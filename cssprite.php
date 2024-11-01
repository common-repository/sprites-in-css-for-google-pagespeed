<?php
/*
Plugin Name: CSS Sprite for Google PageSpeed
Description: Convert all your images from your blog in ccs sprites for google pagespeed!! Setup and forget!
Version: 2.9.2
License: GPL
Author: Arturo Emilio
Author URI: http://arturoemilio.es

*/




/*  Copyright 2009  Arturo Emilio  (email : info@arturoemilio.es)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if (!defined('ABSPATH')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	die();
}

add_action('admin_menu', 'css_menu');
function css_menu() {
	add_options_page('CSSSPRITE', 'CSSSPRITE', 10, dirname(__FILE__).'/options.php');
}

add_action( 'init' , 'cssprite');

add_action('wp_enqueue_scripts','script_cssprite',1);
add_action( 'admin_enqueue_scripts', 'sprite_admin' );

function sprite_admin(){
	wp_enqueue_style( 'ae_cssprite', plugins_url( '' , __FILE__ ).'/admin_css_sprite.css', false,'1.1','all');
}

function script_cssprite(){
	global $wpdb;
	$table_name = $wpdb->prefix . 'sprites';
	wp_enqueue_style( 'ae_cssprite_css', plugins_url( '' , __FILE__ ).'/cssprite.css', false,'1.1','all');
	wp_enqueue_script( 'ae_cssprite_js', plugins_url( '' , __FILE__ ).'/cssprite.js', array('jquery'), '1.0', true );
	
	if (!stristr( $_SERVER["REQUEST_URI"],'wp-admin/')){
		//$ubicacion = strtok($_SERVER["REQUEST_URI"],'?');	
		$ubicacion = md5($_SERVER["REQUEST_URI"]);				
		$hashes = $wpdb->get_row($wpdb->prepare( "SELECT * FROM $table_name WHERE ubicacion = %s",$ubicacion), ARRAY_A);
		if($hashes['hash']){
			$css_file =  '/cache/CSS_Sprite/'.$hashes['hash'];	
			if(file_exists(WP_CONTENT_DIR.$css_file.'.dom')){
				wp_enqueue_style( $hashes['hash'], WP_CONTENT_URL.$css_file.'.css', false,'1.1','all');
			}
		}
		
	}

	 
}
//CRONS

function do_api_sprite_externo( $file,$hash,$direccion,$ubicacion,$url,$buffer) {

							$opciones = json_decode(get_option('Sprites_AE'),1);
							
				    	$datos['cdn'] = $opciones['cdn'];
							$datos['ping'] = get_site_url();  						//plugin_dir_url(__FILE__);
							$datos['dir'] = WP_CONTENT_DIR.'/cache/CSS_Sprite/';
							$datos['url'] = WP_CONTENT_URL.'/cache/CSS_Sprite/';
							$datos['exclude'] = $opciones['excluir'];
							$datos['host'] = get_site_url();
							$datos['email'] = $opciones['email'];
							$datos['token'] = $opciones['token'];
							$datos['salt'] = wp_salt();
							$datos['file'] = $file;
							$datos['hash'] = $hash;
							$datos['direccion'] = $direccion;
							$datos['html'] = $buffer;
							$datos['id'] = $hash;
							$server = 'http://api.arturoemilio.com/css_sprite.php?id='.$datos['id'];		
							$post = http_build_query($datos);

							$ch = curl_init();		
							curl_setopt($ch,CURLOPT_URL, $server);
							curl_setopt($ch,CURLOPT_POST, true);
							curl_setopt($ch,CURLOPT_POSTFIELDS, $post);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
							curl_setopt($ch, CURLOPT_HEADER, false); 
							curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
							curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
							curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
							$response = curl_exec($ch);   
							curl_close($ch);
							
							global $wpdb;
							$table_name = $wpdb->prefix . 'sprites';
							$stat = $wpdb->get_row($wpdb->prepare( "SELECT * FROM $table_name WHERE ubicacion = %s",$ubicacion), ARRAY_A);
							
							sprite_estado($ubicacion,$hash, $url,'Resquest sent',time(),null,$response,null);

}
function sprite_estado($ubicacion , $hash = null , $direccion = null , $mensaje = null , $start = null , $end = null , $response = null, $set = null ) {
							
							if(!$ubicacion) wp_die('ERROR');
							
							global $wpdb;
							$table_name = $wpdb->prefix . 'sprites';
							$stat = $wpdb->get_row($wpdb->prepare( "SELECT * FROM $table_name WHERE ubicacion = %s",$ubicacion), ARRAY_A);
												
							if($stat['ubicacion']){
								$update['ubicacion'] = $ubicacion;
							} 
							
							$stat['ubicacion'] = $ubicacion;
							$stat['hash'] = $hash;
														
							if($stat['hash'] != $hash)
											$stat['ids']++;
												
							if($start)
									$stat['start'] = $start;
							elseif(!$stat['start'])
									$stat['start'] = time();
							
							if($end)
									$stat['end'] = $end;
							
							if($mensaje)
									$stat['mensaje'] = $mensaje;
							
							if($response)
									$stat['response'] = $response;
							
							if($set)
									$stat['refresh'] = $set;
							
							if($direccion)
											$stat['urls'] = $direccion;

							if($update)
									$error = $wpdb->update( $table_name, $stat,$update)	;
							else
									$error= $wpdb->insert( $table_name, $stat);
							
							if($error === false)
									return 'ERROR DB.';
							else
									return 'Correcto';
}

add_action( 'api_sprite_externo', 'do_api_sprite_externo', 1, 6 );

function cssprite(){
			if (!stristr( $_SERVER["REQUEST_URI"],'wp-admin/')
					&& !stristr( $_SERVER["REQUEST_URI"],'xmlrpc.php')
					&& !stristr( $_SERVER["REQUEST_URI"],'wp-login.php')
					&& !stristr( $_SERVER["REQUEST_URI"],'sprite=false')
					&& !stristr( $_SERVER["REQUEST_URI"],'wp-content/')
					&& !stristr( $_SERVER["REQUEST_URI"],'wp-cron.php')
					&& !headers_sent()
					){
							 if(stristr( $_SERVER["REQUEST_URI"],'api_css_sprite=yes')){
							 	api_css_sprite();
							 }else{

								ob_start("csprite");
								ob_flush(); 
								flush();
							 }
					}
}
function api_css_sprite(){
		
			if(!$_POST['salt'] || !$_POST['file'] || !$_POST['hash']){
						die('Forbidden');
			}else{
				if ($_POST['salt'] != wp_salt()){
						die('Error');
				}
			}
			
			$extensiones = array ('png','css','dom');	
			$options = json_decode(get_option('Sprites_AE'),1);
			$opciones = $options['opciones']; 
						
			$out =  WP_CONTENT_DIR.'/cache/CSS_Sprite/'.$_POST['hash'];
			
			//filename
			$message = '<br/>FILE: '.$_POST['hash'];
			$dir = dirname($out);
			if (!file_exists($dir ))
					mkdir($dir , 0755,true);
		
			foreach($extensiones as $ext){
					
				echo   PHP_EOL.'<hr>URL: '.$_POST['check'].'&ext='.$ext;
				$ch = curl_init($_POST['check'].'&ext='.$ext);
				curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
				curl_setopt($ch, CURLOPT_NOBODY, true);    // we don't need body
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
				$output = curl_exec($ch);
				$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);
				echo PHP_EOL.'<hr>HEADER: '.$code.PHP_EOL.'BODY: '.$output;
				if ($code == '200'){
					  $ch = curl_init();
					  curl_setopt($ch, CURLOPT_URL, $_POST['file'].'&ext='.$ext);
					  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					  curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
				  	  $data = curl_exec($ch);
					  curl_close($ch);
					  file_put_contents($out.'.'.$ext,$data);
					 
						//Response from the cloud server. Extension, file, Http header Code from cloud server.
					  $message .= PHP_EOL.$ext.':'.$out.'.'.$ext.PHP_EOL.$code.PHP_EOL. $header.'<br/>';
				}
			 }
			  
			  global $wpdb;
				$table_name = $wpdb->prefix . 'sprites';
			  $stat = $wpdb->get_row($wpdb->prepare( "SELECT * FROM $table_name WHERE hash = %s",$_POST['hash']), ARRAY_A);
				if($stat['hash'] == $_POST['hash']){
						$message .= 'Actualizacion DB '.sprite_estado($stat['ubicacion'],$stat['hash'],null,'Sprite Retrieved',null,time(),null,1);
			  }
				  
				if(is_array($opciones['web'])){
					foreach($opciones['web'] as $comando){
								$ch = curl_init();
							  curl_setopt($ch, CURLOPT_URL, $comando);
							  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							  curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
							  curl_setopt($ch, CURLOPT_HEADER, 1);
						    $data = curl_exec($ch);
							  $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
							  $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
							  $header = substr($data, 0, $header_size);
							  $body = substr($data, $header_size);
							  curl_close($ch);
							  $message .= PHP_EOL.'<hr>Command web executed';
					}	
				}	
				if(is_array($opciones['exec'])){
					foreach($opciones['exec'] as $comando){
							  $shell = shell_exec ($comando);
							  $message .= PHP_EOL.'<hr>Command exec executed';
					}	
				}	
				
				
				echo $message;
				die('FIN');
}            
               
function csprite($buffer) {
	$inicio = microtime();
				
 	if(empty($buffer) || !$buffer) return ;
 	
 	$hash = false;

	$opciones = json_decode(get_option('Sprites_AE'),1);
	
	if(!$opciones['email'] || !$opciones['token']) return $buffer;
	
	if (($no_sprite = preg_replace('/\[NO_SPRITE\]/s', '', $buffer)) != $buffer)return  $buffer;
		
		//$ubicacion = strtok($_SERVER["REQUEST_URI"],'?');
	  $ubicacion = md5($_SERVER["REQUEST_URI"]);
		
		$matches = array();	
		preg_match_all('/<img.*?(http([^"]*(.?:jpe?g|png|gif|jpg))).*?>/i' , $buffer , $matches);
		$matches = $matches[1];
		sort($matches);
		
		foreach ($matches as $match) {
			if(!$match && !stristr( $match,get_site_url()) && !stristr( $match,$opciones['cdn'])) continue;
			$ret .=  $match.'<br/>';
			$string .=  trim($match);
		}
		
		
		$file = $_SERVER["SERVER_NAME"].strtok($_SERVER["REQUEST_URI"],'?');
		$file = str_ireplace('/', '.', $file).'__'.$opciones['cdn'];
		$hash = sha1($string.$opciones['cdn']);
		$new_hash = $hash;

		global $wpdb;
		$table_name = $wpdb->prefix . 'sprites';
		$hashes = $wpdb->get_row($wpdb->prepare( "SELECT * FROM $table_name WHERE ubicacion = %s",$ubicacion), ARRAY_A);
		
		
		if(count($matches) <= 2 ){
			sprite_estado($ubicacion,null,$_SERVER["REQUEST_URI"],'Page must have at least 3 images to sprite',time(),null,null,null);
			return $buffer;
		}

		if($hash['refresh'] == 1){
			global $post;
			$error =  wp_update_post( $post->ID);	
			sprite_estado($hash['ubicacion'],$hash['hash'],null,null,null,null,null,2);
		}
		
		$dom_file =  WP_CONTENT_DIR.'/cache/CSS_Sprite/'.$hashes['hash'].'.dom';

		if($hashes['hash'] != $hash || !file_exists($dom_file)){
					
				$dom_file =  WP_CONTENT_DIR.'/cache/CSS_Sprite/'.$hash.'.dom';
				$css_file =  WP_CONTENT_DIR.'/cache/CSS_Sprite/'.$hash.'.css';	
				$png_file =  WP_CONTENT_DIR.'/cache/CSS_Sprite/'.$hash.'.png';	
				$css_url =   WP_CONTENT_URL.'/cache/CSS_Sprite/'.$hash.'.css';	

				if(!file_exists($dom_file)){
							$direccion = $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];	
							
							wp_schedule_single_event( time(), 'api_sprite_externo', array( $file,$hash,$direccion,$ubicacion,$_SERVER["REQUEST_URI"],$buffer) );							
							$conexion = microtime() - $inicio;
							
				}else{
						 $mes = sprite_estado($ubicacion,$hash, $_SERVER["REQUEST_URI"],'Sprite Retrieved',null,time(),null,1);
				}
				
		}
		
		$hash = $hashes['hash'];

		$dom_file =  WP_CONTENT_DIR.'/cache/CSS_Sprite/'.$hash.'.dom';
		$css_file =  WP_CONTENT_DIR.'/cache/CSS_Sprite/'.$hash.'.css';	
		$png_file =  WP_CONTENT_DIR.'/cache/CSS_Sprite/'.$hash.'.png';	
		$css_url =   WP_CONTENT_URL.'/cache/CSS_Sprite/'.$hash.'.css';	
		
		$images = json_decode(file_get_contents($dom_file), true);	
			
		if(is_array($images)){
			foreach ($images as $key => $value) {
				$buffer = preg_replace($key, $value, $buffer);
			}
		}else{
			unlink($dom_file);
		}
		$inicio = microtime() - $inicio;
		
		return fin($buffer,$conexion,$hash,$new_hash,$stat[$ubicacion]['response'],$inicio,$ret);
		
}
function fin($buffer,$conexion=null,$hash=null,$new_hash=null,$response=null,$inicio=null){
	$estat = '<!-- CSS Sprites Generated by Css Sprites For Wordpress. Learn more: http://www.arturoemilio.es 
	';
		if($conexion){
			$estat .= ' Conexion to CSS Sprites Service: '.$conexion.' segs, HASH: '.$hash.'-'.$new_hash.':
			';
			$estat .= $response;
		}else{
			$estat .= ' 
			The sprite is already cached, HASH: '.$hash;
		}
		$estat .=  ' 
		Total Time: '.$inicio.' segs ---- Css Sprites for Wordpress -->';
		
		
		return $buffer.$estat;
}
function actual_url() {
	 $pageURL = 'http';
	 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	 $pageURL .= "://";
	 if ($_SERVER["SERVER_PORT"] != "80") {
	  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].strtok($_SERVER["REQUEST_URI"],'?');
	 } else {
	  $pageURL .= $_SERVER["SERVER_NAME"].strtok($_SERVER["REQUEST_URI"],'?');
	 }
	 return $pageURL;
}

function sprites_ae_install() {
			global $wpdb;
			$table_name = $wpdb->prefix . 'sprites';
			
			
			$opciones = json_decode(get_option('Sprites_AE'),1);
						
			$options = json_decode(get_option('AE_Css_Sprite'),1);
			if(is_array($options)){
					$hashes = json_decode(get_option('AE_Css_Sprite_urls'),1);
					$refresh = json_decode(get_option('AE_Css_Sprite_urls_refresh'),1);	
					delete_option('AE_Css_Sprite');
					delete_option('AE_Css_Sprite_urls');
					delete_option('AE_Css_Sprite_urls_refresh');
					$opciones = $options;

			}
			unset($options);
			$options = json_decode(get_option('Sprites_AE'),1);
			if(is_array($options['opciones'])){
				$hashes = $options['urls'];
				$refesh = $options['refresh'];
				$opciones = $options['opciones'];
			}	
			
			$opciones['version'] = '1.5';
			update_option('Sprites_AE',json_encode($opciones));	
			
			$stat = $wpdb->get_results( "SELECT * FROM $table_name",ARRAY_A);
			$wpdb->query($wpdb->prepare( "DROP TABLE $table_name",$path));
			
			$sql = "CREATE TABLE $table_name (
					ubicacion varchar(50) NOT NULL,
					hash varchar(50) NOT NULL ,
					mensaje text,
					start text NOT NULL,
					end text,
					urls longtext  NOT NULL,
					response text,
					refresh varchar(1),
					ids int NOT NULL,
					PRIMARY KEY (ubicacion)
									);";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
			
			foreach($stat as $linea){
				$linea['ubicacion'] = md5($linea['url']);
				$wpdb->insert( $table_name, $linea);
			}
			
			
}
register_activation_hook( __FILE__, 'sprites_ae_install' );
function aesprite_update_db_check() {
		$opciones = json_decode(get_option('Sprites_AE'),1);
    if ( $opciones['version'] != '1.5' ) {
        sprites_ae_install();
    }
}
add_action( 'plugins_loaded', 'aesprite_update_db_check' );
?>
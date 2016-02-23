<?php
/**
 * Plugin Name: FQ Form Builder
 * Plugin URI: http://figoliquinn.github.io/fq-form-builder/
 * Description: A light-weight form-building tool.
 * Version: 1.0.0
 * Author: Bob Passaro & Tony Figoli
 * Author URI: http://figoliquinn.com
 * License: GPL2
*/







function test_form_builder() {

	require_once(dirname(__FILE__).'/fq-form-class.php');

	$form = new FQ_Form_Builder_Test();
	return $form->display();

}
add_shortcode( 'test_form_builder' , 'test_form_builder' );





/*

	See below the main class for 

*/


class FQ_Form_Builder {	


	// Form Settings
	public $form = array(
		'method' 		=> 'post',
		'action' 		=> '#',
		'class' 		=> 'my-form',
		'id' 			=> 'my-form',
		'enctype' 		=> 'multipart/form-data',
		'form_title' 	=> 'Contact Us',
	);


	// Email Settings
	public $send_to = ''; 
	public $reply_to = '';
	public $email_element = ''; // form element name to grab users email from


	// Save to Wordpress Settings
	public $save_to_post_type = ''; 

	
	// Debug mode?
	public $debug = false;


	// Form Elements
	public $elements = array();
	





	private $nonce_name = 'fq_nonce';

	private $nonce_value; // set automatically

	private $submitted = false;

	private $values = array();

	private $errors = array();




	
	function __construct() {
	
		include(dirname(__FILE__).'/fq-form-class-templates.php');
		$this->templates = $templates;
		
	}
	


	function process_form() {
		
		$this->mail_form();

		$this->save_form();

	}
	
	
	function mail_form() {
	
		if( $this->submitted && !$this->errors && $this->send_to ) {
			
			$message = "";
			foreach( $this->values as $key => $value ){
				$message .= "{$key} : {$value}\r\n";
			}
			$to			= 	$this->send_to;
			$subject	= 	'Form Submission';
			$headers	= 	'From: webmaster@example.com' . "\r\n" .
							'Reply-To: webmaster@example.com' . "\r\n" .
							'X-Mailer: PHP/' . phpversion();
			mail($to, $subject, $message, $headers);
			
			$this->set_form_message($this->templates['form_email_message']);
		}
		
	}
	
	
	function save_form(){
	
		if( $this->submitted && !$this->errors && $this->save_to_post_type ) {
			
			// save form to post type here...
			if( $error ) {
		
			} else {
		
				$this->set_form_message($this->templates['form_save_message']);
			}
		}
	
	}
	
	
	function set_values(){
		
		$values = $_GET;
		if($this->form['method']=='post') $values = $_POST;
		
		if($values[$this->nonce_name]==$this->nonce_value){
			
			$this->submitted = true;
			foreach( $this->elements as $n => $element ) {
				
				$this->elements[$n]['value'] = $this->values[$element['name']] = $this->clean_value($values[$element['name']]);
			}
			$this->values[$this->nonce_name] = $this->nonce_value;
		}

		$this->check_values();

		$this->process_form();

		return $this->values;
		
	}
	
	
	function check_values(){
		
		if($this->submitted){
			foreach( $this->elements as $n => $element ) {
			
				if( $element['required']==true && !$this->values[$element['name']] ) {
				
					$this->errors[$element['name']] = $this->elements[$n]['error'] = 'This is required.';
				}
				if( $element['validate']=='email' && !sanitize_email($this->values[$element['name']] )) {
					
					if( !$element['required'] && !$this->values[$element['name']] ) { /* do nothing */ }
					else { $this->errors[$element['name']] = $this->elements[$n]['error'] = 'This is not a valid email.'; }
				}
				if( function_exists($element['validate']) ) {
					
					$error = call_user_func($element['validate'],$this->values[$element['name']]);
					if($error) $this->errors[$element['name']] = $this->elements[$n]['error'] = $error;
				}
			}
			if($this->errors){
	
				$this->set_form_message($this->templates['form_error_message']);
			} else {

				$this->set_form_message($this->templates['form_success_message']);
			}
		}

	}
	
	
	function set_form_message($message) {
	
		$this->templates['form'] = str_replace('<form',$message.'<form',$this->templates['form']);
	}
	
	
	function display() {
		
		// create a unique nonce for this form only
		static $called; $called++;
		$this->nonce_value = md5(serialize($this->elements).$called);
		
		
		if(!$this->elements) return;	

		ob_start();

		$this->set_values();

		$this->debug_report();
		
		foreach( $this->elements as $n => $element ) {
			
			$pattern = '/({)(.+?)(})/';

			$template_name = $element['template'] ? $element['template'] : $element['type'];
			$template = $this->templates[$template_name];

			if( $template ) {
				
				switch( $element['type'] ){
					
					case "checkbox":
					case "radio":

						$x = preg_split('/\{REPEAT\}/',$template);
						$pre = $x[0];
						$template = $x[1];
						$post = $x[2];
						$options = array();
						foreach($element['options'] as $value => $label)
						{
							$opt = array(
								'label'=>$label,
								'value'=>$value,
								'name'=>$element['name'].( $element['type'] == 'checkbox' ? '[]' : '' ),
								'type'=>$element['type'],
							);
							$options[$value] = $this->do_replace( $template , $opt );
							if( $element['type'] == 'checkbox' && $element['value'] && in_array( $value , (array)$element['value'] ) ) {
								$options[$value] = str_replace('<input ','<input checked="checked" ',$options[$value]);
							}
							if( $element['type'] == 'radio' && $element['value'] && $value==$element['value'] ) {
								$options[$value] = str_replace('<input ','<input checked="checked" ',$options[$value]);
							}
						}
						$this->form['form_elements'] .= $this->do_replace( $pre.implode($options).$post , $element );

					break;

					case "select":

						$x = preg_split('/\{REPEAT\}/',$template);
						$pre = $x[0];
						$template = $x[1];
						$post = $x[2];
						$options = array();
						foreach($element['options'] as $value => $label)
						{
							$opt = array(
								'label'=>$label,
								'value'=>$value,
							);
							$options[$value] = $this->do_replace( $template , $opt );
							if( $element['value'] && $value==$element['value'] ) {
								$options[$value] = str_replace('<option ','<option selected="selected" ',$options[$value]);
							}
						}
						$this->form['form_elements'] .= $this->do_replace( $pre.implode($options).$post , $element );

					break;

					default:
						
						$this->form['form_elements'] .= $this->do_replace($template,$element);

					break;
				}			

			}
		
		}

		$html = $this->do_replace($this->templates['form'],$this->form);
		$html = str_replace("</form>",'<input type="hidden" name="'.$this->nonce_name.'" value="'.$this->nonce_value.'" /></form>',$html);
		
		echo $html;	
		return ob_get_clean();


	} // end display



	
	function do_replace($template,$element){
		
		$pattern = '/({)(.+?)(})/';
		preg_match_all( $pattern , $template , $matches);
		foreach($matches[2] as $n => $replace) {
			
			$template = str_ireplace( '{'.$replace.'}' , $element[strtolower($replace)] , $template );
		}
		return $template;
	
	}
	
	function clean_value($str) {
		
		if(is_array($str)){
			
			foreach($str as $k => $v){
				$str[$k] = stripslashes(htmlspecialchars(trim($v)));
			}
					
		} else{
			$str = stripslashes(htmlspecialchars(trim($str)));
		}
		return $str;
	}
	
	
	function debug_report( ) {
		
		if(!$this->debug) return;

		$report = "<pre>".print_r($this->values,true)."</pre>";
		$this->set_form_message($report);
	}


} // end class










class FQ_Form_Builder_Test extends FQ_Form_Builder {	


	public $elements = array(
		array(
			'template' => 'text-kooky', // optional - defaults to value of 'type'
			'type' => 'text',
			'label' => 'Text Element',
			'name' => 'text-kooky',
			'id' => 'text-kooky',
			'class' => '',
			'value' => '',
			'placeholder' => 'Kooky!',
			#'required' => true,
			'help' => 'This some help to add to the element.',
			#'validate' => 'custom_validation_function',
		),
		array(
			'template' => 'text', // optional - defaults to value of 'type'
			'type' => 'text',
			'label' => 'Text Element',
			'name' => 'text',
			'id' => 'text',
			'class' => '',
			'value' => 'John Doe',
			'placeholder' => 'Your Name',
		),
		array(
			'template' => 'text', // optional - defaults to value of 'type'
			'type' => 'text',
			'label' => 'Email Element',
			'name' => 'email',
			'id' => 'email',
			'class' => '',
			'value' => '',
			'placeholder' => 'name@example.com',
			'required' => false,
			'validate' => 'email',
		),
		array(
			'template' => 'text', // optional - defaults to value of 'type'
			'type' => 'file',
			'label' => 'File Element',
			'name' => 'file',
			'id' => 'file',
			'class' => '',
			'value' => 'John Doe',
			'placeholder' => 'Your Name',
		),
		array(
			'type' => 'textarea',
			'label' => 'Textarea Element',
			'name' => 'textarea',
			'id' => 'textarea',
			'class' => '',
			'value' => 'John Doe',
			'placeholder' => 'Your Name',
			'rows' => '5',
		),
		array(
			'type' => 'checkbox',
			'label' => 'Checkbox Element',
			'name' => 'checkbox',
			'id' => 'checkbox',
			'class' => '',
			'value' => '',
			'placeholder' => 'Your Name',
			'options' => array('small'=>'Small','medium'=>'Medium','Large'),
			#'required' => true,
		),
		array(
			'type' => 'radio',
			'label' => 'Radio Element',
			'name' => 'radio',
			'id' => 'radio',
			'class' => '',
			'value' => '',
			'placeholder' => 'Your Name',
			'options' => array(1=>'Small','Medium','Large'),
			#'required' => true,
		),
		array(
			'type' => 'select',
			'label' => 'Select Element',
			'name' => 'select',
			'id' => 'select',
			'class' => '',
			'value' => '',
			'placeholder' => 'Your Name',
			'options' => array(''=>'Select One',1=>'Small','Medium','Large'),
			#'required' => true,
		),
		array(
			'type' => 'html',
			'html' => '<a href="http://figoliquinn/jillamysager/form-test/">Reload</a>',
		),
		array(
			'type' => 'submit',
			'label' => 'Submit',
			'name' => 'submit_me',
			'id' => 'submit_me',
		),
	);


} // end test class



function custom_validation_function($value){
	
	if( $value !== "fart" ) return "No good!";
}





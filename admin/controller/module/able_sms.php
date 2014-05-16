<?php
################################################################################################
#  SMS Modules for OpenCart designed for ChakTak Foods Inc  		   #
################################################################################################
class ControllerModuleAbleSMS extends Controller {
	private $error = array(); 
	
	public function index() {   
		//Load the language file for this module
		$this->load->language('module/able_sms');

		//Set the title from the language file $_['heading_title'] string
		$this->document->setTitle($this->language->get('heading_title'));
		
		//Load the settings model. You can also add any other models you want to load here.
		$this->load->model('setting/setting');
		
		//Save the settings if the user has submitted the admin form (ie if someone has pressed save).
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('able_sms', $this->request->post);		
					
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}

		//This is how the language gets pulled through from the language file.
		//
		// If you want to use any extra language items - ie extra text on your admin page for any reason,
		// then just add an extra line to the $text_strings array with the name you want to call the extra text,
		// then add the same named item to the $_[] array in the language file.
		//
		// 'able_sms_example' is added here as an example of how to add - see admin/language/english/module/able_sms.php for the
		// other required part.
		
		$text_strings = array(
				'heading_title',
				'text_enabled',
				'text_disabled',
				'text_current_balance',
				'text_via_able_web',
				'text_via_chaktak',
				'button_save',
				'button_cancel',
				'button_add_module',
				'button_remove',
				'entry_username',
				'entry_password',
				'entry_sms_api_link'
		);
		
		foreach ($text_strings as $text) {
			$this->data[$text] = $this->language->get($text);
		}
		//END LANGUAGE
		
		//The following code pulls in the required data from either config files or user
		//submitted data (when the user presses save in admin). Add any extra config data
		// you want to store.
		//
		// NOTE: These must have the same names as the form data in your able_sms.tpl file
		//
		$config_data = array(
				'able_sms_username', //this becomes available in our view by the foreach loop just below.
				'able_sms_password',
				'able_sms_api_link'
		);
		
		foreach ($config_data as $conf) {
			if (isset($this->request->post[$conf])) {
				$this->data[$conf] = $this->request->post[$conf];
			} else {
				$this->data[$conf] = $this->config->get($conf);
			}
		}
	
		//This creates an error message. The error['warning'] variable is set by the call to function validate() in this controller (below)
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		//SET UP BREADCRUMB TRAIL. YOU WILL NOT NEED TO MODIFY THIS UNLESS YOU CHANGE YOUR MODULE NAME.
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/able_sms', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('module/able_sms', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
	
		//This code handles the situation where you have multiple instances of this module, for different layouts.
		$this->data['modules'] = array();
		
		if (isset($this->request->post['able_sms_module'])) {
			$this->data['modules'] = $this->request->post['able_sms_module'];
		} elseif ($this->config->get('able_sms_module')) { 
			$this->data['modules'] = $this->config->get('able_sms_module');
		}		

		$this->load->model('design/layout');
		
		$this->data['layouts'] = $this->model_design_layout->getLayouts();

		//Choose which template file will be used to display this request.
		$this->template = 'module/able_sms.tpl';
		$this->children = array(
			'common/header',
			'common/footer',
		);

		$this->checkBalance();

		//Send the output.
		$this->response->setOutput($this->render());
	}
	
	/*
	 * 
	 * This function is called to ensure that the settings chosen by the admin user are allowed/valid.
	 * You can add checks in here of your own.
	 * 
	 */
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/able_sms')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}

	private function checkBalance() {
			
			$post = array(
				"Username"=>$this->data['able_sms_username'],
				"ResponseID","Password"=>$this->data['able_sms_password']
				
			);

			$result = $this->sendRequest("GetXml_MessageTotal",$post);

			$xml = simplexml_load_string(htmlspecialchars_decode($result));

			$this->data['current_balance'] = $xml->MessageTotal->NowAmount;
			$this->data['via_able_web']		 = $xml->MessageTotal->NoAPICount;
			$this->data['via_chaktak']		 = $xml->MessageTotal->APICount;

	}

	private function sendRequest($action, $post){
		$toURL = $this->data['able_sms_api_link']."/".$action;

		$ch = curl_init();
		$options = array(
			CURLOPT_URL=>$toURL,
			CURLOPT_HEADER=>0,
			CURLOPT_VERBOSE=>0,
			CURLOPT_RETURNTRANSFER=>true, 
			CURLOPT_POST=>true, 
			CURLOPT_POSTFIELDS=>http_build_query($post),
		);

		curl_setopt_array($ch, $options);
		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	}

}
?>
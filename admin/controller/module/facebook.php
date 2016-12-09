<?php
class ControllerModuleFacebook extends Controller {
	private $error = array(); 
	
	public function index() {   
		$this->load->language('module/facebook');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');

		$opencartversion = (int)VERSION.'.'.str_replace('.',"",substr(VERSION,2));

		if((float)$opencartversion<1.51){
			if ($this->request->server['REQUEST_METHOD'] == 'POST') {			
				$module=array();
				$i=0;
				if(isset($this->request->post['facebook_module'])){
					foreach($this->request->post['facebook_module'] as $k=>$v){
						foreach($v as $key=>$value){
							$this->request->post['facebook_'.$k.'_'.$key]=$value;
						}
						$module[]=$i;
						$i++;
					}
				}
				$this->request->post['facebook_module']=implode(',',$module);
			}
		}
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			$this->model_setting_setting->editSetting('facebook', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			#$this->response->redirect($this->url->link('extension/captcha', 'token=' . $this->session->data['token'], 'SSL'));						

			$this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));

		}

		$this->load->model('localisation/language');

		$languages = $this->model_localisation_language->getLanguages();
		$data['languages'] = $languages;
				
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_content_top'] = $this->language->get('text_content_top');
		$data['text_content_bottom'] = $this->language->get('text_content_bottom');		
		$data['text_column_left'] = $this->language->get('text_column_left');
		$data['text_column_right'] = $this->language->get('text_column_right');
		
		$data['entry_creator'] = $this->language->get('entry_creator');
		$data['entry_version'] = $this->language->get('entry_version');
		$data['entry_updated'] = $this->language->get('entry_updated');
		$data['entry_contact'] = $this->language->get('entry_contact');

		$data['entry_apikey'] = $this->language->get('entry_apikey');
		$data['entry_apisecret'] = $this->language->get('entry_apisecret');
		$data['entry_pwdsecret'] = $this->language->get('entry_pwdsecret');
		$data['entry_pwdsecret_desc'] = $this->language->get('entry_pwdsecret_desc');
		$data['entry_button'] = $this->language->get('entry_button');
		$data['entry_button_desc'] = $this->language->get('entry_button_desc');
		
		$data['entry_layout'] = $this->language->get('entry_layout');
		$data['entry_position'] = $this->language->get('entry_position');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_add_module'] = $this->language->get('button_add_module');
		$data['button_remove'] = $this->language->get('button_remove');
		
 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->error['code'])) {
			$data['error_code'] = $this->error['code'];
		} else {
			$data['error_code'] = '';
		}
		
  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/facebook', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$data['action'] = $this->url->link('module/facebook', 'token=' . $this->session->data['token'], 'SSL');
		
		$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
		
		$data['modules'] = array();

		foreach ($languages as $language) {
			if (isset($this->request->post['facebook_button_' . $language['language_id']])) {
				$data['facebook_button_' . $language['language_id']] = $this->request->post['facebook_button_' . $language['language_id']];
			} else {
				$data['facebook_button_' . $language['language_id']] = $this->config->get('facebook_button_' . $language['language_id']);
			}
		}

		if (isset($this->request->post['facebook_apikey'])) {
			$data['facebook_apikey'] = $this->request->post['facebook_apikey'];
		} elseif ($this->config->get('facebook_apikey')) { 
			$data['facebook_apikey'] = $this->config->get('facebook_apikey');
		} else $data['facebook_apikey'] = '';

		if (isset($this->request->post['facebook_apisecret'])) {
			$data['facebook_apisecret'] = $this->request->post['facebook_apisecret'];
		} elseif ($this->config->get('facebook_apisecret')) { 
			$data['facebook_apisecret'] = $this->config->get('facebook_apisecret');
		} else $data['facebook_apisecret'] = '';

		if (isset($this->request->post['facebook_pwdsecret'])) {
			$data['facebook_pwdsecret'] = $this->request->post['facebook_pwdsecret'];
		} elseif ($this->config->get('facebook_pwdsecret')) { 
			$data['facebook_pwdsecret'] = $this->config->get('facebook_pwdsecret');
		} else $data['facebook_pwdsecret'] = '';

		if($opencartversion<1.51){
			$data['modules']=array();
			$toarray=$obj_get='';

			if(isset($this->request->post['facebook_module'])){
				$toarray=$this->request->post['facebook_module'];
				$obj_get='post';
			}
 			elseif ($this->config->get('facebook_module')!='') { 
				$toarray=$this->config->get('facebook_module');
				$obj_get='config';
			}

			if($toarray!=',' && $obj_get!=''){
				$i=count(explode(',',$toarray));
				$array_key=array('layout_id','position','status','sort_order');
				for($k=0; $k<$i; $k++){
					$array=array();
					foreach($array_key as $key){
						if($obj_get=="config")
							$array[$key]=$this->config->get('facebook_'.$k.'_'.$key);
						else
							$array[$key]=$this->request->post['facebook_'.$k.'_'.$key];
					}
					$data['modules'][] = $array;
				}
			}
		}
		else{	

			if (isset($this->request->post['facebook_module'])) {
				$data['modules'] = $this->request->post['facebook_module'];
			} elseif ($this->config->get('facebook_module')) { 
				$data['modules'] = $this->config->get('facebook_module');
			}

		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->load->model('design/layout');
		
		$data['layouts'] = $this->model_design_layout->getLayouts();

		$this->response->setOutput($this->load->view('module/facebook.tpl', $data));
				
		#$this->response->setOutput($this->render());
	}
	
	private function validate() {

		if (!$this->user->hasPermission('modify', 'module/facebook')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->request->post['facebook_apikey'] || !$this->request->post['facebook_apisecret'] || !$this->request->post['facebook_pwdsecret']) {
			$this->error['code'] = $this->language->get('error_code');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	

	}
}
?>
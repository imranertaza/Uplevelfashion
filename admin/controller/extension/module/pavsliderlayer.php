<?php
/**
 * @package Pav blog module for Opencart 1.5.x
 * @version 1.0
 * @author http://www.pavothemes.com
 * @copyright	Copyright (C) Feb 2013 PavoThemes.com <@emai:pavothemes@gmail.com>.All rights reserved.
 * @license		GNU General Public License version 2
 */

class ControllerExtensionModulePavsliderlayer extends Controller {
	private $error = array();

	private $mdata = array();

	public function getModel( $model='slider' ){
		$model = "model_pavsliderlayer_"+$model;
		return $this->{$model};
	}

	protected function preload(){
		$this->load->language('extension/module/pavsliderlayer');
		$this->load->model('tool/image');
		$this->load->model( 'extension/pavsliderlayer/slider' );
		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/module');

		// List Table
		$this->module("pavsliderlayer");

		$this->mdata['user_token'] = $this->session->data['user_token'];
	}

	public function module($extension){
		$module_data = array();
		$this->load->model('setting/extension');
		$this->load->model('setting/module');
		$extensions = $this->model_setting_extension->getInstalled('module');
		$modules = $this->model_setting_module->getModulesByCode($extension);
		foreach ($modules as $module) {
			$module_data[] = array(
				'module_id' => $module['module_id'],
				'name'      => $this->language->get('heading_title') . ' &gt; ' . $module['name'],
				'edit'      => $this->url->link('extension/module/' . $extension, 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module['module_id'], true),
				'delete'    => $this->url->link('extension/module/pavsliderlayer/delete', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module['module_id'], true)
			);
		}
		$this->mdata['extensions'][] = array(
			'name'      => $this->language->get('heading_title'),
			'module'    => $module_data,
			'edit'      => $this->url->link('extension/module/' . $extension, 'user_token=' . $this->session->data['user_token'], true)
		);
	}

	public function index() {
		$this->preload();
		$model = $this->model_extension_pavsliderlayer_slider;

		$model->checkInstall();
		// process input post to insert or update
		if ( ($this->request->server['REQUEST_METHOD'] == 'POST') ) {

			$module = array();

			if( !isset($this->request->post['pavsliderlayer_module']) ){
				$this->request->post['pavsliderlayer_module'] = array();
			}

			if( $this->request->post['action_mode'] == 'module-only' ) {


				unset($this->request->post['action_mode']);

			 	$module['pavsliderlayer_module'] = $this->request->post['pavsliderlayer_module'];

			 	if (!isset($this->request->get['module_id'])) {
					$this->model_setting_module->addModule('pavsliderlayer', $this->request->post);
					$this->response->redirect($this->url->link('extension/module/pavsliderlayer', 'user_token=' . $this->session->data['user_token'], true));
				} else {
					$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
					$this->response->redirect($this->url->link('extension/module/pavsliderlayer', 'user_token=' . $this->session->data['user_token'].'&module_id='.$this->request->get['module_id'], true));
				}

				$this->session->data['success'] = $this->language->get('text_success');

			} else {
				$this->mdata = array();
				$this->mdata['title']  = $this->request->post['slider']['title'] ;
				$this->mdata['params'] = serialize( $this->request->post['slider'] );
				$this->mdata['id']     = $this->request->post['id'];
				$this->mdata['id']     = $model->saveSliderGroupData( $this->mdata , $this->request->post['id'] );
				$id = 'id='.$this->mdata['id']."&";

				if( !empty($this->request->post['action_mode']) &&  $this->request->post['action_mode'] == 'create-new' ){
				 	$id = '';
				}

				$this->response->redirect($this->url->link('extension/module/pavsliderlayer', $id.'user_token=' . $this->session->data['user_token'], true));
			}

		}

		$this->mdata['objurl'] = $this->url;

		$this->mdata['objsession'] = $this->session;


 		if (isset($this->error['warning'])) {
			$this->mdata['error_warning'] = $this->error['warning'];
		} else {
			$this->mdata['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->mdata['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->mdata['success'] = '';
		}

		$this->mdata['success_msg'] = array();
		if( isset($this->request->get['msg_idone'])  ){
			if($this->request->get['msg_idone']){
				$this->mdata['success_msg'] =  $this->language->get('import_data_done');
			}else{
				$this->mdata['error_warning'] = $this->language->get('import_data_error');
			}
		}

  		$this->mdata['breadcrumbs'] = array();

   		$this->mdata['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true),
      		'separator' => false
   		);

   		$this->mdata['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'].'&type=module', true),
      		'separator' => ' :: '
   		);

   		$this->mdata['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/module/pavsliderlayer', 'user_token=' . $this->session->data['user_token'], true),
      		'separator' => ' :: '
   		);

		$this->mdata['actionImport'] = $this->url->link('extension/module/pavsliderlayer/import', 'user_token=' . $this->session->data['user_token'], true);

		$this->mdata['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'].'&type=module', true);
		$this->mdata['user_token'] = $this->session->data['user_token'];
		$this->mdata['modules'] = array();

		$this->mdata['yesno'] = array( 1=> $this->language->get('text_yes'), 0=>$this->language->get('text_no') );

		$this->mdata['shadow_types'] = array(
			0  	=> $this->language->get('text_no_shadow'),
			1   => 1,
			2   => 2,
			3   => 3
		);
		$this->mdata['linepostions'] = array(
			'bottom'  => $this->language->get('text_bottom'),
			'top'     => $this->language->get('text_top')
		);
		$this->mdata['navigator_types'] = array(
			'none'  => $this->language->get('text_none'),
			'bullet'     => $this->language->get('text_bullet'),
			'thumb'     => $this->language->get('text_thumbnail'),
			'both'     => $this->language->get('text_both')

		);
		$this->mdata['navigation_arrows'] = array(
			'none'    			 => $this->language->get('text_none'),
			'nexttobullets' 	 => $this->language->get('text_nexttobullets'),
			'verticalcentered'   => $this->language->get('text_verticalcentered')


		);

		$this->mdata['navigation_style'] = array(
			'round' 	    => $this->language->get('text_round'),
			'navbar'        => $this->language->get('text_navbar'),
			'round-old'     => $this->language->get('text_round_old') ,
			'square-old'    => $this->language->get('text_square_old') ,
			'navbar-old'    => $this->language->get('text_navbar_old')

		);

		// Get Data Store
		$this->load->model('setting/store');
		$action = array();
		$action[] = array(
			'text' => $this->language->get('text_edit'),
			'href' => $this->url->link('setting/setting', 'user_token=' . $this->session->data['user_token'], true)
		);
		$store_default = array(
			'store_id' => 0,
			'name'     => $this->config->get('config_name') . $this->language->get('text_default'),
			'url'      => HTTP_CATALOG,
		);
		$stores = $this->model_setting_store->getStores();
		array_unshift($stores, $store_default);

		foreach ($stores as &$store) {
			$url = '';
			if ($store['store_id'] > 0 ) {
				$url = '&store_id='.$store['store_id'];
			}
			$store['option'] = $this->url->link('extension/module/pavsliderlayer', $url.'&user_token=' . $this->session->data['user_token'], true);
		}
		$this->mdata['stores'] = $stores;
		$store_id = isset($this->request->get['store_id'])?$this->request->get['store_id']:0;
		$this->mdata['store_id'] = $store_id;
		// End GetData Store

		$d = array(
			'banner_image'=>array(),
			'width'=>940,
			'height'=>350,
			'image_navigator' => 0,
			'navimg_height'   =>97,
			'navimg_weight'   =>177
		);

		$id = isset($this->request->get['id']) ? $this->request->get['id']:0;
		$sliderGroup = $model->getSliderGroupById( $id );
	 	$this->mdata['manage_sliders_detail'] = array();

	 	if( isset($id) ) {
	 		$this->mdata['manage_sliders_detail'] = array(
	 				'id'                        => $id,
	 				'manage_sliders'            => $this->url->link('extension/module/pavsliderlayer/layer', 'group_id='.$id.'&user_token=' . $this->session->data['user_token'], true),
	 				'export_group_and_sliders'  => $this->url->link('extension/module/pavsliderlayer/export', 'id='.$id.'&user_token=' . $this->session->data['user_token'], true),
	 				'preview_sliders_in_group'  => $this->url->link('extension/module/pavsliderlayer/previewGroup', 'id='.$id.'&user_token=' . $this->session->data['user_token'], true),
	 				'delete_slider_group'       => $this->url->link('extension/module/pavsliderlayer/deleteGroup', 'id='.$id.'&user_token=' . $this->session->data['user_token'], true)
	 			);
	 	}

		$params = $sliderGroup['params'] ;

		// Get Setting Module
		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
		}

		if (isset($this->request->get['module_id'])) {
			$module_id = $this->request->get['module_id'];
			$url = '&module_id='.$module_id;
		} else {
			$module_id = '';
			$url = '';
		}
		$this->mdata['module_id'] = $module_id;

		$this->mdata['mdelete'] = $this->url->link('extension/module/pavsliderlayer/mdelete', 'user_token=' . $this->session->data['user_token'].$url, true);
		$this->mdata['action']  = $this->url->link('extension/module/pavsliderlayer', 'user_token=' . $this->session->data['user_token'].$url, true);

		if (isset($this->request->post['name'])) {
			$this->mdata['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$this->mdata['name'] = $module_info['name'];
		} else {
			$this->mdata['name'] = '';
		}

		if (isset($this->request->post['status'])) {
			$this->mdata['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$this->mdata['status'] = $module_info['status'];
		} else {
			$this->mdata['status'] = '';
		}
		if( !isset($module_info['group_id']) ){
			$module_info['group_id'] = '';
		}
		if (isset($this->request->post['group_id'])) {
			$this->mdata['group_id'] = $this->request->post['group_id'];
		} elseif (!empty($module_info)) {
			$this->mdata['group_id'] = $module_info['group_id'];
		} else {
			$this->mdata['group_id'] = '';
		}
		// End Get Setting

		$this->mdata['edit_slider_in_group'] = array();

		$slidergroups = $model->getListSliderGroups();
		foreach ($slidergroups as $groups) {
			$this->mdata['edit_slider_in_group'][]	= array(
					'id'    => $groups['id'],
					'title' => $groups['title'],
					'link1' => $this->url->link('extension/module/pavsliderlayer/layer', 'group_id='.$groups['id'].'&user_token=' . $this->session->data['user_token'], true),
					'link2' => $this->url->link('extension/module/pavsliderlayer', 'id='.$groups['id'].'&user_token=' . $this->session->data['user_token'], true)
				);
		}


		$this->mdata['slidergroups'] = $model->getListSliderGroups();
		$this->mdata['params'] = $params;
		$this->mdata['fullwidth'] = array('' 		   => $this->language->get('Boxed'),
										  'fullwidth'  => $this->language->get('Fullwidth'),
										  'fullscreen' => $this->language->get('Fullscreen') );



		$this->load->model('localisation/language');
		$this->mdata['languages'] = $this->model_localisation_language->getLanguages();

		$this->document->addScript('view/javascript/sliderlayer/script.js');
		$this->document->addStyle('view/stylesheet/sliderlayer/style.css');
		$this->document->addScript('view/javascript/sliderlayer/jquery-cookie.js');

		$this->mdata['header'] = $this->load->controller('common/header');
		$this->mdata['column_left'] = $this->load->controller('common/column_left');
		$this->mdata['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/sliderlayer/sliders', $this->mdata));
	}

	public function mdelete(){
		$this->load->model('setting/module');
		$this->load->language('extension/module/pavsliderlayer');
		if (isset($this->request->get['module_id'])) {
			$this->model_setting_module->deleteModule($this->request->get['module_id']);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('extension/module/pavsliderlayer', 'user_token=' . $this->session->data['user_token'], true));
		}
	}

	public function layer(){
		$this->preload();
		$id = isset($this->request->get['id'] ) ? $this->request->get['id'] : 0;
		$language_id = isset($this->request->get['lang'])?$this->request->get['lang']:1;

		if( !isset($this->request->get['group_id'])  || !$this->request->get['group_id']){
			$this->response->redirect( $this->url->link('extension/module/pavsliderlayer', 'user_token=' . $this->session->data['user_token'], true) );
		}
		$groupID = (int)$this->request->get['group_id'];

		$model = $this->model_extension_pavsliderlayer_slider;

	 	$this->mdata['success_msg'] = array();
		if (isset($this->error['warning'])) {
			$this->mdata['error_warning'] = $this->error['warning'];
		} else {
			$this->mdata['error_warning'] = '';
		}
  		$this->mdata['breadcrumbs'] = array();

   		$this->mdata['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true),
      		'separator' => false
   		);

   		$this->mdata['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'].'&type=module', true),
      		'separator' => ' :: '
   		);

   		$this->mdata['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/module/pavsliderlayer', 'user_token=' . $this->session->data['user_token'], true),
      		'separator' => ' :: '
   		);
		$this->mdata['action']         = $this->url->link('extension/module/pavsliderlayer/savedata', 'user_token=' . $this->session->data['user_token'], true);
		$this->mdata['cancel']         = $this->url->link('extension/module/pavsliderlayer', 'user_token=' . $this->session->data['user_token'], true);
		$this->mdata['preview_slider'] = $this->url->link('extension/module/pavsliderlayer/previewGroup', 'id='.$this->request->get['group_id'].'&lang='.$language_id.'&user_token=' . $this->session->data['user_token'], true);
		$this->mdata['back_to_edit']   = $this->url->link('extension/module/pavsliderlayer', 'id='.$this->request->get['group_id'].'&lang='.$language_id.'&user_token=' . $this->session->data['user_token'], true);

		$this->mdata['user_token'] = $this->session->data['user_token'];
		$actionUpdatePostURL = str_replace("&amp;", "&", $this->url->link('extension/module/pavsliderlayer/savepos', 'user_token=' . $this->session->data['user_token'], true));
		$this->mdata['actionUpdatePostURL'] = $actionUpdatePostURL;

		$sliderGroup = $model->getSliderGroupById( $groupID );
		if( !$sliderGroup ){
			$this->response->redirect( $this->url->link('extension/module/pavsliderlayer', 'user_token=' . $this->session->data['user_token'], true) );
		}

		$this->mdata['sliderGroups'] = $model->getListSliderGroups();
		$this->mdata['sliderGroup']  = $sliderGroup;
		$this->mdata['slide_url']    = $this->url->link('extension/module/pavsliderlayer','id='.$groupID.'&user_token=' . $this->session->data['user_token'], true);
		$this->mdata['create_new']   = $this->url->link('extension/module/pavsliderlayer/layer','group_id='.$groupID.'&user_token=' . $this->session->data['user_token'], true);

		$this->mdata['sliderHeight'] = (int) $sliderGroup['params']['height'];
		$this->mdata['sliderWidth']  = (int) $sliderGroup['params']['width'];
		$this->mdata['sliderDelay']  = (int) $sliderGroup['params']['delay'];
		//// get list  slider
		$this->mdata['sliders'] = array();
		$sliders = $model->getSlidersByGroupId( $groupID, $language_id );
		foreach ($sliders as $slide) {
			$this->mdata['sliders'][] = array(
					'id' 		   => $slide['id'],
		            'title' 	   => $slide['title'],
		            'parent_id'    => $slide['parent_id'],
		            'group_id' 	   => $slide['group_id'],
		            'params'       => $slide['params'],
		            'layersparams' => $slide['layersparams'],
		            'image'        => !defined('IMAGE_URL') ? HTTP_CATALOG.'image/'. $slide['image'] : IMAGE_URL. $slide['image'],
		            'status'       => $slide['status'],
		            'position'     => $slide['position'],
		            'language_id'  => $slide['language_id'],
		            'url_detail'   => $this->url->link('extension/module/pavsliderlayer/layer', 	'id='.$slide['id'].'&group_id='.$slide['group_id'].'&lang='.$slide['language_id'].'&user_token=' . $this->session->data['user_token'], true),
		            'copythis'     => $this->url->link('extension/module/pavsliderlayer/copythis', 'id='.$slide['id'].'&group_id='.$slide['group_id'].'&lang='.$slide['language_id'].'&user_token=' . $this->session->data['user_token'], true),
		            'deleteslider' => $this->url->link('extension/module/pavsliderlayer/deleteslider', 'id='.$slide['id'].'&group_id='.$slide['group_id'].'&lang='.$slide['language_id'].'&user_token=' . $this->session->data['user_token'], true)
				);
		}

		$this->mdata['group_id'] = $groupID;
		$dslider = array(
			'status' => 1,

		);

		$this->mdata['transtions'] = array(
			'random' 			   => 'Randdom',
			'slidehorizontal'	   => 'Slide Horizontal',
			'slidevertical' 	   => 'Slide Vertical',
			'boxslide' 			   => 'Box Slide',
			'boxfade' 			   => 'Box Fade',
			'slotzoom-horizontal'  => 'Slot Zoom Horizontal',
			'slotslide-horizontal' => 'Slot Slide Horizontal',
			'slotfade-horizontal'  => 'Slot Fade Horizontal',
			'slotzoom-vertical'	   => 'Slot Zoom Vertical',
			'slotslide-vertical'   => 'Slot Slide Vertical',
			'slotfade-vertical'	   => 'Slot Fade Vertical',
			'curtain-1' 		   => 'Curtain 1',
			'curtain-2' 		   => 'Curtain 2',
			'curtain-3' 		   => 'Curtain 3',
			'slideleft' 		   => 'Slide Left',
			'slideright' 		   => 'Slide Right',
			'slideup' 			   => 'Slide Up',
			'slidedown' 		   => 'Slide Down',
			'papercut' 			   => 'Page Cut',
			'3dcurtain-horizontal' => '3dcurtain Horizontal',
			'3dcurtain-vertical'   => '3dcurtain Vertical',
			'flyin'				   => 'Fly In',
			'turnoff' 		       => 'Turn Off',
			'custom-1' 			   => 'Custom 1',
			'custom-2' 			   => 'Custom 2',
			'custom-3' 			   => 'Custom 3',
			'custom-4' 			   => 'Custom 4'
		);

		$default = array(
			'title' 			=> '',
			'slider_link' 		=> '',
			'slider_usevideo' 	=> '0',
			'slider_videoid' 	=> '',
			'slider_videoplay' 	=> '0',
			'fullwidth'			=> '',
			'image' 			=> 'catalog/demo/slider.png',
			'layersparams'		=> '',
			'slider_transition' => 'random',
			'slider_delay'   	=> '0',
			'slider_status'  	=> 1,
			'slider_transition' => 'random',
			'slider_duration'   => '300',
 			'slider_rotation'   => '0',
			'slider_enable_link'=> 0,
			'slider_link'  		=> '',
			'slider_thumbnail' 	=> '',
			'slider_slot' 		=>'7',
			'slider_image'   	=> 'catalog/demo/slider.png',
			'slider_id'   		=> '',
			'id'  				=> '',
			'slider_title' 		=> '',
			'slider_enable_link'=> '',
			'params' 			=> array()

		);

		$this->mdata['usevideo'] = array( '0'=> $this->language->get('No'),'youtube'=>'Youtube','vimeo'=>'Vimeo');

		$slider = $model->getSliderById( $id );
		$times = array();
		$layers = array();

		$slider = array_merge( $default, $slider );

		if( $slider['layersparams'] ){
			$std = unserialize( $slider['layersparams'] );
			$layers = $std->layers;

			foreach( $layers as $k=>$l ){
				$layers[$k]['layer_caption'] = addslashes( str_replace("'",'"',html_entity_decode( $l['layer_caption'] , ENT_QUOTES, 'UTF-8')) );
				$layers[$k]['layer_caption'] = preg_replace( "#\n|\r|\t#","", $layers[$k]['layer_caption']);
			}
		}

		$params = $slider['params'] ? unserialize( $slider['params'] ) : array();
		$params = array_merge( $default, $params );


		if( $params['slider_thumbnail'] ){
			$this->mdata['slider_thumbnail'] =  $this->model_tool_image->resize(  $params['slider_thumbnail'],
						$sliderGroup['params']['thumbnail_width'], $sliderGroup['params']['thumbnail_height'] );
		}else {
			$this->mdata['slider_thumbnail'] = '';
		}

		// echo '<pre>'.print_r( $slider  , 1); die;
		$this->mdata['yesno'] = array( 1=> $this->language->get('text_yes'), 0=>$this->language->get('text_no') );
		$this->mdata['slider_title'] = $slider['title'];
		$this->mdata['params'] = $params;
		$this->mdata['layers'] = json_encode( $layers );
		$this->mdata['slider_id']  = $id;
		$this->mdata['slider_image'] = $slider['image'];
		$this->mdata['IMAGE_URL'] = defined('IMAGE_URL') ? IMAGE_URL : HTTP_CATALOG.'image/';
		$this->mdata['slider_image_src'] = defined('IMAGE_URL') ? IMAGE_URL . $slider['image'] : HTTP_CATALOG.'image/'.$slider['image'];

		$typoFile = 	HTTP_CATALOG."catalog/view/theme/default/stylesheet/sliderlayer/css/typo.css";
		if( file_exists( DIR_CATALOG ."view/theme/". $this->config->get('config_theme')."/stylesheet/sliderlayer/css/typo.css" ) ){
			$typoFile = 	HTTP_CATALOG."catalog/view/theme/". $this->config->get('config_theme')."/stylesheet/sliderlayer/css/typo.css";
		}
		$this->document->addStyle( $typoFile  );

		$this->document->addScript('view/javascript/jquery/jquery-ui/jquery-ui.min.js');
		$this->document->addStyle('view/javascript/jquery/jquery-ui/jquery-ui.min.css');

		$this->document->addScript('view/javascript/sliderlayer/script.js');
		$this->document->addStyle('view/stylesheet/sliderlayer/style.css');

		// #Mutiple language Layer
		$this->mdata['url_catalog'] = HTTP_CATALOG;
		$this->mdata['user_token'] = $this->session->data['user_token'];

		$this->mdata['lang'] = isset($this->request->get['lang'])?$this->request->get['lang']:1;


		$this->load->model("localisation/language");
		$this->mdata['show_languages'] = array();
		$languages = $this->model_localisation_language->getLanguages();

		foreach ($languages as $lang) {
			$this->mdata['show_languages'][] = array(
					'language_id' => $lang['language_id'],
					'name'        => $lang['name'],
					'image'       => 'language/'.$lang['code'].'/'.$lang['code'].'.png',
					'url'         => $this->url->link('extension/module/pavsliderlayer/layer','&group_id='.$this->request->get['group_id'].'&lang='.$lang['language_id'].'&user_token=' . $this->session->data['user_token'], true)
				);
		}

		$this->mdata['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		$this->mdata['header'] = $this->load->controller('common/header');
		$this->mdata['column_left'] = $this->load->controller('common/column_left');
		$this->mdata['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/sliderlayer/layer', $this->mdata));
	}

	public function savepos(){
		if (!$this->user->hasPermission('modify', 'extension/module/pavsliderlayer')) {
			$this->error['warning'] = $this->language->get('error_permission');
			die( $this->language->get('error_permission') );
		}
		if( isset($this->request->post['id'])  && is_array($this->request->post['id']) ){
			 $this->preload();
			 foreach( $this->request->post['id'] as $id => $pos ){
			 	 $this->model_extension_pavsliderlayer_slider->updatePost((int)$id, $pos );
			 }
		}
		die('done');
	}
	public function copythis(){
		if (!$this->user->hasPermission('modify', 'extension/module/pavsliderlayer')) {
			$this->error['warning'] = $this->language->get('error_permission');
			die( $this->language->get('error_permission') );
		}
	 	$this->preload();
	 	$model = $this->model_extension_pavsliderlayer_slider;
	 	if( isset($this->request->get['id']) ){
	 		$lang = isset($this->request->get['lang'])?$this->request->get['lang']:1;

	 		$id = (int) $this->request->get['id'];
	 		$slider = $slider = $model->getSliderById( $id );
	 		$slider['title'] = 'Copy Of ' . $slider['title'];
	 		$slider['id'] = 0;
	 		$slider['language_id'] = $lang;
	 		$id = $model->saveData( $slider );

	 		$url = $this->url->link('extension/module/pavsliderlayer/layer', 'id='.$id.'&group_id='.$slider['group_id'].'&lang='.$lang.'&user_token=' . $this->session->data['user_token'], true);
	 		$this->response->redirect( $url );
	 	}
	 	die("Having Error");
	}

	public function cloneGroupSliders(){

		if (!$this->user->hasPermission('modify', 'extension/module/pavsliderlayer')) {
			$this->error['warning'] = $this->language->get('error_permission');
			die( $this->language->get('error_permission') );
		}

		$get = $this->request->get;

		$groupID = $get['group_id'];
		$languageID = $get['lang'];
		$cloneGroupID = $get['clonegroup'];
		if($cloneGroupID == 0) {
			die("Please select a group sliders !!!!!");
		}
		$this->load->model('extension/pavsliderlayer/slider');
		$this->model_extension_pavsliderlayer_slider->cloneGroupSliders($groupID, $cloneGroupID, $languageID);
	}

 	public function import(){
 		if (!$this->user->hasPermission('modify', 'extension/module/pavsliderlayer')) {
			$this->error['warning'] = $this->language->get('error_permission');
			die( $this->language->get('error_permission') );
		}
 		$this->preload();
		$done = 0;
 		if( isset($_FILES['import_file']['name']) ){
 			$path = $_FILES['import_file']['tmp_name'];

 			$content = trim(file_get_contents( $path ));

 			$this->mdata = unserialize( $content );

 			if( is_object($this->mdata) && isset($this->mdata->group) && isset($this->mdata->sliders) ){

 				$id = $this->model_extension_pavsliderlayer_slider->saveSliderGroupData( $this->mdata->group );
 				if( $id ) {
 					foreach( $this->mdata->sliders as $slider ){
 						$slider['id'] = 0;
 						$slider['group_id'] = $id;
 						$this->model_extension_pavsliderlayer_slider->saveData( $slider );
 					}
 				}
				$done = 1;
 			}
 		}


 		$url = $this->url->link('extension/module/pavsliderlayer', 'user_token=' . $this->session->data['user_token'].'&msg_idone='.$done, true);
	 	$this->response->redirect( $url );
 	}
 	public function export(){
 		if (!$this->user->hasPermission('modify', 'extension/module/pavsliderlayer')) {
			$this->error['warning'] = $this->language->get('error_permission');
			die( $this->language->get('error_permission') );
		}
 		$this->preload();

 		if( isset($this->request->get['id']) ){
 			$id = (int) $this->request->get['id'];
 			$sliderGroup = $this->model_extension_pavsliderlayer_slider->getSliderGroupById( $id );
 			$sliderGroup['id'] = 0;
 			$sliderGroup['params'] = serialize( $sliderGroup['params'] );
 			$export = new stdClass();
 			$export->group = $sliderGroup;

 			$language_id = isset($this->request->get['lang'])?$this->request->get['lang']:1;
 			$export->sliders = $this->model_extension_pavsliderlayer_slider->getSlidersByGroupId( $id, $language_id );

 			header("Content-Type: plain/text");
			header("Content-Disposition: Attachment; filename=export_group_".time().".txt");
			header("Pragma: no-cache");

			echo  serialize($export);
 		}
 	}
 	public function deleteslider(){
 		if (!$this->user->hasPermission('modify', 'extension/module/pavsliderlayer')) {
			$this->error['warning'] = $this->language->get('error_permission');
			$group_id = 0;
		}else {
	 		$this->preload();
			if( isset($this->request->get['id']) ){
				$this->model_extension_pavsliderlayer_slider->deleteSlider( (int)($this->request->get['id']) );
			}
			$group_id = $this->request->get['group_id'];
		}

		$lang = isset($this->request->get['lang'])?$this->request->get['lang']:1;

		$url = $this->url->link('extension/module/pavsliderlayer/layer', 'group_id='.$group_id.'&lang='.$lang.'&user_token=' . $this->session->data['user_token'], true);
	 	$this->response->redirect( $url );
 	}

	public function savedata () {
		if (!$this->user->hasPermission('modify', 'extension/module/pavsliderlayer')) {
			$this->error['warning'] = $this->language->get('error_permission');
			die(  $this->language->get('error_permission') );
		}
		$this->preload();
	 	$output = new stdClass();
	 	$output->id =	0;
	 	$output->error = 1;
	 	$output->message = $this->language->get('text_could_not_save');
	 	$model = $this->model_extension_pavsliderlayer_slider;

	  	if( empty($this->request->post['slider_title']) ){
	  		$output->message = $this->language->get('error_missing_title');
	  		echo json_encode( $output );exit();
	  	}
	  	if( $this->request->post ){

	  		$layersparams = new stdClass();
	  		$layersparams->layers = array();
	  		$params = serialize( $this->request->post );

			if( isset($this->request->post['layers'])  && !empty($this->request->post['layers']) ){

				$layersparams = new stdClass();
				$times 		 	= $this->request->post['layer_time'];
				$tmp 			= $this->request->post['layers'];

				$layers = $this->request->post['layers'];

				foreach (  $layers as $key => $value ) {
						$value['time_start'] = $times[$value['layer_id']];
					 	$times[$value['layer_id']] = $value;
				}

				$k = 0;
				foreach( $times as $key => $value ) {
					if( is_array($times) ) {
						$value['layer_id'] = $k+1;
						$layersparams->layers[$k] = $value;
						$k++;
					}
				}

				unset( $this->request->post['layer_time'] );
				unset( $this->request->post['layers'] );


				$params = serialize( $this->request->post );
			}

			$this->mdata = array(
				'layersparams' => serialize($layersparams),
				'group_id'     => $this->request->post['slider_group_id'],
				'title'   	   => $this->request->post['slider_title'],
				'id'		   => $this->request->post['slider_id'],
				'image'        => $this->request->post['slider_image'],
				'params'	   =>  $params,
				'status'       => $this->request->post['slider_status'],
				'language_id'  => $this->request->post['slider_language_id'],

			);

			$id = $model->saveData( $this->mdata );
		 	$output->id     = $id;
		 	$output->error  = 0;
		}
 		echo json_encode( $output );exit();
	}

	/**
	 * Delete
	 */
	public function deleteGroup(){
		if (!$this->user->hasPermission('modify', 'extension/module/pavsliderlayer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}else {
			$this->preload();
			if( isset($this->request->get['id']) ){
				$this->model_extension_pavsliderlayer_slider->delete( (int)($this->request->get['id']) );
			}
		}
		$this->response->redirect($this->url->link('extension/module/pavsliderlayer', 'user_token=' . $this->session->data['user_token'], true));
	}

	public function previewLayer() {
		$this->preload();
		$this->mdata['objconfig'] = $this->config;

		$this->mdata['heading_title'] = $this->language->get("text_preview_this_slider");

		if( !isset($this->request->post['slider_preview_data']) ){
			die( $this->language->get('text_could_not_show_preview') );
		}
		$a =  trim( html_entity_decode($this->request->post['slider_preview_data']) ) ;
		$a= json_decode( $a );

		$sliderGroup = $this->model_extension_pavsliderlayer_slider->getSliderGroupById( $a->params->slider_group_id);

		$this->mdata['sliderParams'] = $sliderGroup['params'];

		$this->mdata['slider_layers'] = array();
		foreach ($a->layers as $layer) {
			$this->mdata['slider_layers'][] = array(
				'layer_video_type' 	 => $layer->layer_video_type,
				'layer_video_id' 	 => $layer->layer_video_id,
				'layer_video_height' => $layer->layer_video_height,
				'layer_video_width'  => $layer->layer_video_width,
				'layer_video_thumb'  => $layer->layer_video_thumb,
				'layer_id' 			 => $layer->layer_id,
				'layer_content' 	 => ! defined('IMAGE_URL') ? HTTP_CATALOG."image/".$layer->layer_content : IMAGE_URL.$layer->layer_content,
				'layer_type' 		 => $layer->layer_type,
				'layer_class' 		 => $layer->layer_class,
				'layer_caption' 	 => html_entity_decode( $layer->layer_caption, ENT_QUOTES, 'UTF-8'),
				'layer_animation' 	 => $layer->layer_animation,
				'layer_easing' 		 => $layer->layer_easing,
				'layer_speed' 		 => $layer->layer_speed,
				'layer_top' 		 => $layer->layer_top,
				'layer_left' 		 => $layer->layer_left,
				'layer_endtime' 	 => $layer->layer_endtime,
				'layer_endspeed' 	 => $layer->layer_endspeed,
				'layer_endanimation' => $layer->layer_endanimation,
				'layer_endeasing' 	 => $layer->layer_endeasing,
				'time_start' 		 => $layer->time_start,
				'main_image'         => ! defined('IMAGE_URL') ? HTTP_CATALOG."image/".$a->image : IMAGE_URL.$a->image
			);
		}

		$this->mdata['js_plugins']    = 	HTTP_CATALOG."catalog/view/javascript/layerslider/jquery.themepunch.plugins.min.js";
		$this->mdata['js_revolution'] = 	HTTP_CATALOG."catalog/view/javascript/layerslider/jquery.themepunch.revolution.js";

		$this->mdata['typoFile'] = 	HTTP_CATALOG."catalog/view/theme/default/stylesheet/sliderlayer/css/typo.css";
		if( file_exists( DIR_CATALOG ."view/theme/". $this->config->get('config_theme')."/stylesheet/sliderlayer/css/typo.css" ) ){
			$this->mdata['typoFile'] = 	HTTP_CATALOG."catalog/view/theme/". $this->config->get('config_theme')."/stylesheet/sliderlayer/css/typo.css";
		}

		$template = $this->load->view('extension/module/sliderlayer/preview', $this->mdata);

		$this->response->setOutput($template);
	}

	// Preview Group Layers
	public function previewGroup() {
		$this->preload();

		if( isset($this->request->get['id']) ){

			$sliderGroup = $this->model_extension_pavsliderlayer_slider->getSliderGroupById( (int)$this->request->get['id'] );
			$this->mdata['sliderParams'] = $sliderGroup['params'];

			$language_id = isset($this->request->get['lang'])?$this->request->get['lang']:1;

			$sliders =  $this->model_extension_pavsliderlayer_slider->getSlidersByGroupId( (int)$this->request->get['id'], $language_id );
			$slider_layersparams = array();
			foreach( $sliders as $key=> $slider ){
				$slider["layers"] = array();
				$slider['params'] = unserialize( $slider["params"] );
				$slider_layersparams = unserialize( $slider["layersparams"] );

				if( $sliderGroup['params']['image_cropping']) {
					 $slider['main_image'] = $this->model_extension_pavsliderlayer_slider->resize($slider['image'], $sliderGroup['params']['width'],
					 								$sliderGroup['params']['height'],'a');
				}else {
					 $slider['main_image'] = !defined('IMAGE_URL') ? HTTP_CATALOG."image/".$slider['image'] : IMAGE_URL.$slider['image'];
				}

				if( $slider['params']['slider_thumbnail'] ) {
					$slider['thumbnail'] = $this->model_extension_pavsliderlayer_slider->resize( $slider['params']['slider_thumbnail'], $sliderGroup['params']['thumbnail_width'],
					 								$sliderGroup['params']['thumbnail_height'],'a');
				}else {
					$slider['thumbnail'] = $this->model_extension_pavsliderlayer_slider->resize($slider['image'], $sliderGroup['params']['thumbnail_width'],
					 								$sliderGroup['params']['thumbnail_height'],'a');
				}

				$sliders[$key] = $slider;
			}

			$this->mdata['layersparams'] = array();

			foreach ($slider_layersparams->layers as $layer) {
				$this->mdata['layersparams'][] = array(
						'layer_video_type' 	 => $layer['layer_video_type'],
						'layer_video_id' 	 => $layer['layer_video_id'],
						'layer_video_height' => $layer['layer_video_height'],
						'layer_video_width'  => $layer['layer_video_width'],
						'layer_video_thumb'  => $layer['layer_video_thumb'],
						'layer_id' 			 => $layer['layer_id'],
						'layer_content' 	 => !defined('IMAGE_URL') ? HTTP_CATALOG."image/".$layer['layer_content'] : IMAGE_URL.$layer['layer_content'],
						'layer_type' 		 => $layer['layer_type'],
						'layer_class' 		 => $layer['layer_class'],
						'layer_caption' 	 => html_entity_decode( $layer['layer_caption'], ENT_QUOTES, 'UTF-8'),
						'layer_animation' 	 => $layer['layer_animation'],
						'layer_easing' 		 => $layer['layer_easing'],
						'layer_speed' 		 => $layer['layer_speed'],
						'layer_top' 		 => $layer['layer_top'],
						'layer_left' 		 => $layer['layer_left'],
						'layer_endtime' 	 => $layer['layer_endtime'],
						'layer_endspeed' 	 => $layer['layer_endspeed'],
						'layer_endanimation' => $layer['layer_endanimation'],
						'layer_endeasing' 	 => $layer['layer_endeasing'],
						'time_start' 		 => $layer['time_start']
					);
			}

			$slider['http_catalog'] = HTTP_CATALOG;

			$this->mdata['sliders'] = $sliders;

		}
		$this->mdata['typoFile'] = 	HTTP_CATALOG."catalog/view/theme/default/stylesheet/sliderlayer/css/typo.css";
		if( file_exists( DIR_CATALOG ."view/theme/". $this->config->get('config_theme')."/stylesheet/sliderlayer/css/typo.css" ) ){
			$this->mdata['typoFile'] = 	HTTP_CATALOG."catalog/view/theme/". $this->config->get('config_theme')."/stylesheet/sliderlayer/css/typo.css";
		}

		$this->mdata['js_plugins']    = 	HTTP_CATALOG."catalog/view/javascript/layerslider/jquery.themepunch.plugins.min.js";
		$this->mdata['js_revolution'] = 	HTTP_CATALOG."catalog/view/javascript/layerslider/jquery.themepunch.revolution.js";

		$this->mdata['randID'] = rand( 20,   rand() );

		$template = $this->load->view('extension/module/sliderlayer/previewgroup', $this->mdata);
		$this->response->setOutput($template);

	}

	public function typo(){
		$this->load->language('extension/module/pavsliderlayer');

		$this->mdata['objconfig'] = $this->config;

		$this->mdata['heading_title'] = $this->language->get("typo_management");

		if (isset($this->request->post['field'])) {
			$this->mdata['field'] = $this->request->post['field'];
		} else {
			$this->mdata['field'] = '';
		}

		if (isset($this->request->post['layer_id'])) {
			$this->mdata['layer_id'] = $this->request->post['layer_id'];
		} else {
			$this->mdata['layer_id'] = '';
		}

		if (isset($this->request->post['layer_class'])) {
			$this->mdata['layer_class'] = $this->request->post['layer_class'];
		} else {
			$this->mdata['layer_class'] = '';
		}

	 	$typoFile = 	HTTP_CATALOG."catalog/view/theme/default/stylesheet/sliderlayer/css/typo.css";
		if( file_exists( DIR_CATALOG ."view/theme/". $this->config->get('config_theme')."/stylesheet/sliderlayer/css/typo.css" ) ){
			$typoFile = 	HTTP_CATALOG."catalog/view/theme/". $this->config->get('config_theme')."/stylesheet/sliderlayer/css/typo.css";
		}
		$content = file_get_contents(  $typoFile );

		$this->mdata['typoFile'] = $typoFile;

		$data = preg_match_all("#\.tp-caption\.(\w+)\s*{\s*#", $content, $matches);


		$this->mdata['captions'] = array();

		if( isset($matches[1]) ){
			$this->mdata['captions']  = $matches[1];
		}


		$template = $this->load->view('extension/module/sliderlayer/typo', $this->mdata);

		$this->response->setOutput($template);
	}

	protected function validateSliderGroup() {

		if (!$this->user->hasPermission('modify', 'extension/module/pavsliderlayer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if( !isset($this->request->post['slider']) ){
			$this->error['warning'] = $this->language->get('error_missing_slider_data');
		}elseif(  $this->request->post['slider'] && empty($this->request->post['slider']['title']) ){
			$this->error['warning'] = $this->language->get('error_missing_slider_title');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	//filemanager
	public function filemanager() {
		$this->load->language('common/filemanager');

		if (isset($this->request->get['filter_name'])) {
			$filter_name = rtrim(str_replace(array('../', '..\\', '..', '*'), '', $this->request->get['filter_name']), '/');
		} else {
			$filter_name = null;
		}

		// Make sure we have the correct directory
		if (isset($this->request->get['directory'])) {
			$directory = rtrim(DIR_IMAGE . 'catalog/' . str_replace(array('../', '..\\', '..'), '', $this->request->get['directory']), '/');
		} else {
			$directory = DIR_IMAGE . 'catalog';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data_images = array();

		$this->load->model('tool/image');

		// Get directories
		$directories = glob($directory . '/' . $filter_name . '*', GLOB_ONLYDIR);

		if (!$directories) {
			$directories = array();
		}

		// Get files
		$files = glob($directory . '/' . $filter_name . '*.{jpg,jpeg,png,gif,JPG,JPEG,PNG,GIF}', GLOB_BRACE);

		if (!$files) {
			$files = array();
		}

		// Merge directories and files
		$images = array_merge($directories, $files);

		// Get total number of files and directories
		$image_total = count($images);

		// Split the array based on current page number and max number of items per page of 10
		$images = array_splice($images, ($page - 1) * 16, 16);

		foreach ($images as $image) {
			$name = str_split(basename($image), 14);

			if (is_dir($image)) {
				$url = '';

				if (isset($this->request->get['target'])) {
					$url .= '&target=' . $this->request->get['target'];
				}

				if (isset($this->request->get['thumb'])) {
					$url .= '&thumb=' . $this->request->get['thumb'];
				}

				$data_images[] = array(
					'thumb' => '',
					'name'  => implode(' ', $name),
					'type'  => 'directory',
					'path'  => utf8_substr($image, utf8_strlen(DIR_IMAGE)),
					'href'  => $this->url->link('extension/module/pavsliderlayer/filemanager', 'user_token=' . $this->session->data['user_token'] . '&directory=' . urlencode(utf8_substr($image, utf8_strlen(DIR_IMAGE . 'catalog/'))) . $url, true)
				);
			} elseif (is_file($image)) {
				// Find which protocol to use to pass the full image link back
				if ($this->request->server['HTTPS']) {
					$server = HTTPS_CATALOG;
				} else {
					$server = HTTP_CATALOG;
				}

                $image_url = defined('IMAGE_URL') ? IMAGE_URL . utf8_substr($image, utf8_strlen(DIR_IMAGE)) : $server . 'image/' . utf8_substr($image, utf8_strlen(DIR_IMAGE));

				$data_images[] = array(
					'thumb' => $this->model_tool_image->resize(utf8_substr($image, utf8_strlen(DIR_IMAGE)), 100, 100),
					'name'  => implode(' ', $name),
					'type'  => 'image',
					'path'  => utf8_substr($image, utf8_strlen(DIR_IMAGE)),
					'href'  => $image_url
				);
			}
		}

		$data['images'] = array_chunk($data_images, 4);

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->get['directory'])) {
			$data['directory'] = urlencode($this->request->get['directory']);
		} else {
			$data['directory'] = '';
		}

		if (isset($this->request->get['filter_name'])) {
			$data['filter_name'] = $this->request->get['filter_name'];
		} else {
			$data['filter_name'] = '';
		}

		// Return the target ID for the file manager to set the value
		if (isset($this->request->get['target'])) {
			$data['target'] = $this->request->get['target'];
		} else {
			$data['target'] = '';
		}

		// Return the thumbnail for the file manager to show a thumbnail
		if (isset($this->request->get['thumb'])) {
			$data['thumb'] = $this->request->get['thumb'];
		} else {
			$data['thumb'] = '';
		}

		// Parent
		$url = '';

		if (isset($this->request->get['directory'])) {
			$pos = strrpos($this->request->get['directory'], '/');

			if ($pos) {
				$url .= '&directory=' . urlencode(substr($this->request->get['directory'], 0, $pos));
			}
		}

		if (isset($this->request->get['target'])) {
			$url .= '&target=' . $this->request->get['target'];
		}

		if (isset($this->request->get['thumb'])) {
			$url .= '&thumb=' . $this->request->get['thumb'];
		}

		$data['parent'] = $this->url->link('extension/module/pavsliderlayer/filemanager', 'user_token=' . $this->session->data['user_token'] . $url, true);

		// Refresh
		$url = '';

		if (isset($this->request->get['directory'])) {
			$url .= '&directory=' . urlencode($this->request->get['directory']);
		}

		if (isset($this->request->get['target'])) {
			$url .= '&target=' . $this->request->get['target'];
		}

		if (isset($this->request->get['thumb'])) {
			$url .= '&thumb=' . $this->request->get['thumb'];
		}

		$data['refresh'] = $this->url->link('extension/module/pavsliderlayer/filemanager', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$url = '';

		if (isset($this->request->get['directory'])) {
			$url .= '&directory=' . urlencode(html_entity_decode($this->request->get['directory'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['target'])) {
			$url .= '&target=' . $this->request->get['target'];
		}

		if (isset($this->request->get['thumb'])) {
			$url .= '&thumb=' . $this->request->get['thumb'];
		}

		$pagination = new Pagination();
		$pagination->total = $image_total;
		$pagination->page = $page;
		$pagination->limit = 16;
		$pagination->url = $this->url->link('extension/module/pavsliderlayer/filemanager', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$this->response->setOutput($this->load->view('extension/module/sliderlayer/filemanager', $data));
	}

	public function upload() {
		$this->load->language('common/filemanager');

		$json = array();

		// Make sure we have the correct directory
		if (isset($this->request->get['directory'])) {
			$directory = rtrim(DIR_IMAGE . 'catalog/' . str_replace(array('../', '..\\', '..'), '', $this->request->get['directory']), '/');
		} else {
			$directory = DIR_IMAGE . 'catalog';
		}

		// Check its a directory
		if (!is_dir($directory)) {
			$json['error'] = $this->language->get('error_directory');
		}

		if (!$json) {
			if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) {
				// Sanitize the filename
				$filename = basename(html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8'));

				// Validate the filename length
				if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 255)) {
					$json['error'] = $this->language->get('error_filename');
				}

				// Allowed file extension types
				$allowed = array(
					'jpg',
					'jpeg',
					'gif',
					'png'
				);

				if (!in_array(utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1)), $allowed)) {
					$json['error'] = $this->language->get('error_filetype');
				}

				// Allowed file mime types
				$allowed = array(
					'image/jpeg',
					'image/pjpeg',
					'image/png',
					'image/x-png',
					'image/gif'
				);

				if (!in_array($this->request->files['file']['type'], $allowed)) {
					$json['error'] = $this->language->get('error_filetype');
				}

				// Check to see if any PHP files are trying to be uploaded
				$content = file_get_contents($this->request->files['file']['tmp_name']);

				if (preg_match('/\<\?php/i', $content)) {
					$json['error'] = $this->language->get('error_filetype');
				}

				// Return any upload error
				if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
					$json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
				}
			} else {
				$json['error'] = $this->language->get('error_upload');
			}
		}

		if (!$json) {
			move_uploaded_file($this->request->files['file']['tmp_name'], $directory . '/' . $filename);

			$json['success'] = $this->language->get('text_uploaded');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function folder() {
		$this->load->language('common/filemanager');

		$json = array();

		// Make sure we have the correct directory
		if (isset($this->request->get['directory'])) {
			$directory = rtrim(DIR_IMAGE . 'catalog/' . str_replace(array('../', '..\\', '..'), '', $this->request->get['directory']), '/');
		} else {
			$directory = DIR_IMAGE . 'catalog';
		}

		// Check its a directory
		if (!is_dir($directory)) {
			$json['error'] = $this->language->get('error_directory');
		}

		if (!$json) {
			// Sanitize the folder name
			$folder = str_replace(array('../', '..\\', '..'), '', basename(html_entity_decode($this->request->post['folder'], ENT_QUOTES, 'UTF-8')));

			// Validate the filename length
			if ((utf8_strlen($folder) < 3) || (utf8_strlen($folder) > 128)) {
				$json['error'] = $this->language->get('error_folder');
			}

			// Check if directory already exists or not
			if (is_dir($directory . '/' . $folder)) {
				$json['error'] = $this->language->get('error_exists');
			}
		}

		if (!$json) {
			mkdir($directory . '/' . $folder, 0777);

			$json['success'] = $this->language->get('text_directory');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function delete() {
		$this->load->language('common/filemanager');

		$json = array();

		if (isset($this->request->post['path'])) {
			$paths = $this->request->post['path'];
		} else {
			$paths = array();
		}

		// Loop through each path to run validations
		foreach ($paths as $path) {
			$path = rtrim(DIR_IMAGE . str_replace(array('../', '..\\', '..'), '', $path), '/');

			// Check path exsists
			if ($path == DIR_IMAGE . 'catalog') {
				$json['error'] = $this->language->get('error_delete');

				break;
			}
		}

		if (!$json) {
			// Loop through each path
			foreach ($paths as $path) {
				$path = rtrim(DIR_IMAGE . str_replace(array('../', '..\\', '..'), '', $path), '/');

				// If path is just a file delete it
				if (is_file($path)) {
					unlink($path);

				// If path is a directory beging deleting each file and sub folder
				} elseif (is_dir($path)) {
					$files = array();

					// Make path into an array
					$path = array($path . '*');

					// While the path array is still populated keep looping through
					while (count($path) != 0) {
						$next = array_shift($path);

						foreach (glob($next) as $file) {
							// If directory add to path array
							if (is_dir($file)) {
								$path[] = $file . '/*';
							}

							// Add the file to the files to be deleted array
							$files[] = $file;
						}
					}

					// Reverse sort the file array
					rsort($files);

					foreach ($files as $file) {
						// If file just delete
						if (is_file($file)) {
							unlink($file);

						// If directory use the remove directory function
						} elseif (is_dir($file)) {
							rmdir($file);
						}
					}
				}
			}

			$json['success'] = $this->language->get('text_delete');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function install() {
		$this->load->model( 'extension/pavsliderlayer/slider' );
		$this->model_extension_pavsliderlayer_slider->checkInstall();
	}
}
?>
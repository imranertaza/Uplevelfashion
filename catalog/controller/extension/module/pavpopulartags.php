<?php
/******************************************************
 * @package  : Pav Popular tags module for Opencart 1.5.x
 * @version  : 1.0
 * @author   : http://www.pavothemes.com
 * @copyright: Copyright (C) Feb 2013 PavoThemes.com <@emai:pavothemes@gmail.com>.All rights reserved.
 * @license  : GNU General Public License version 1
*******************************************************/

class ControllerExtensionModulePavpopulartags extends Controller {

	public $data = array();

	public function index($setting) {
		static $module = 1;
		$this->language->load('extension/module/pavpopulartags');

		$this->load->model('tool/image');

		$this->load->model( 'extension/pavpopulartags/tag' );


		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
		 	$this->data['base'] = $this->config->get('config_ssl');
		} else {
			$this->data['base'] = $this->config->get('config_url');
		}

		$language = $this->config->get('config_language_id');
		//get data tagclound
		$limit = isset($setting['limit_tags'])?$setting['limit_tags']:20;
		$min_font_size = isset($setting['min_font_size'])?$setting['min_font_size']:9;
		$max_font_size = isset($setting['max_font_size'])?$setting['max_font_size']:20;
		$font_weight = isset($setting['font_weight'])?$setting['font_weight']:'bold';

		$this->data['prefix'] = isset($setting['prefix'])?$setting['prefix']:'';


		if (isset($setting['title']) && !empty($setting['title'])) {
			$heading_title = $setting['title'][$language];
		} else {
			$heading_title = $this->language->get('heading_title');
		}
		
		$this->data['heading_title'] = $heading_title;

		$tags = $this->model_extension_pavpopulartags_tag->getRandomTags($limit, $min_font_size, $max_font_size, $font_weight);
		
		$this->data['data'] = $tags;

		$this->data['module'] = $module++;

		return $this->load->view('extension/module/pavpopulartags', $this->data);
	}
}
	
?>

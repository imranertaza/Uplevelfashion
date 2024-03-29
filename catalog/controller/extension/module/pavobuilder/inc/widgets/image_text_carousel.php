<?php
/**
 * @package Pavothemer for Opencart 3.x
 * @version 1.0
 * @author http://www.pavothemes.com
 * @copyright	Copyright (C) Feb 2017 PavoThemes.com <@emai:pavothemes@gmail.com>.All rights reserved.
 * @license		GNU General Public License version 2
 */

class PA_Widget_Image_Text_Carousel extends PA_Widgets {

	public function fields(){
		return array(
			'mask'		=> array(
				'icon'	=> 'fa fa-file-image-o',
				'label'	=> $this->language->get( 'entry_widget_image_text_carousel' )
			),
			'tabs'	=> array(
				'general'		=> array(
					'label'		=> $this->language->get( 'entry_general_text' ),
					'fields'	=> array(
						array(
							'type'	=> 'hidden',
							'name'	=> 'uniqid_id',
							'label'	=> $this->language->get( 'entry_row_id_text' ),
							'desc'	=> $this->language->get( 'entry_column_desc_text' )
						),
						array(
							'type'	=> 'select',
							'name'	=> 'layout',
							'label'	=> $this->language->get( 'entry_layout_text' ),
							'default' => 'false',
							'options'	=> $this->getLayoutsOptions(),
							'none' 	=> false
						),
						array(
							'type'	=> 'text',
							'name'	=> 'extra_class',
							'label'	=> $this->language->get( 'entry_extra_class_text' ),
							'desc'	=> $this->language->get( 'entry_extra_class_desc_text' )
						),
						array(
							'type'	=> 'text',
							'name'	=> 'image_size',
							'label'	=> $this->language->get( 'entry_image_size_text' ),
							'desc'	=> $this->language->get( 'entry_image_size_desc' ),
							'default'		=> 'full',
							'placeholder'	=> '200x200'
						),
						array(
							'type'	=> 'select',
							'name'	=> 'effect1',
							'label'	=> $this->language->get( 'entry_effect_image' ),
							'default' => 'default',
							'options'	=> array(
								array(
									'value'	=> 'default',
									'label'	=> 'Default'
								),
								array(
									'value'	=> 'cube',
									'label'	=> 'Cube'
								),
								array(
									'value'	=> 'flip',
									'label'	=> 'Flip'
								),
								array(
									'value'	=> 'fade',
									'label'	=> 'Fade'
								)
							),
						),
						array(
							'type'	=> 'group',
							'name'	=> 'items',
							'label'	=> $this->language->get( 'entry_item' ),
							'fields'	=> array(
								array(
									'type'	=> 'image',
									'name'	=> 'src',
									'label'	=> $this->language->get( 'entry_image_text' )
								),
								array(
									'type'	=> 'text',
									'name'	=> 'alt',
									'label'	=> $this->language->get( 'entry_alt_text' ),
									'default' => '',
									'desc'	=> $this->language->get( 'entry_alt_desc_text' )
								),
								array(
									'type'	=> 'text',
									'name'	=> 'title',
									'label'	=> $this->language->get( 'entry_title_text' ),
									'desc'	=> $this->language->get( 'entry_title_desc_text' ),
									'language'	=> true,
									'default' => ''
								),
								array(
									'type'		=> 'editor',
									'name'		=> 'content',
									'label'		=> $this->language->get( 'entry_content_text' ),
									'default'	=> '',
									'language'	=> true
								),
		                        array(
		                            'type'    => 'text',
		                            'name'    => 'button_text',
		                            'label'   => $this->language->get( 'entry_banner_button' ),
		                            'default' => '',
		                            'language'=> true
		                        ),
							   	array(
			                        'type'  => 'text',
		                            'name'  => 'button_link',
		                            'label' => $this->language->get( 'entry_button_link' ),
		                            'desc'  => $this->language->get( 'entry_banner_link_desc' ),
		                            'default'       => ''
		                        ),
		                        array(
									'type'		=> 'iconpicker',
									'name'		=> 'icon',
									'label'		=> $this->language->get( 'icon' ),
									'default'	=> ''
								),
							)
						),
					)
				),
				'style'				=> array(
					'label'			=> $this->language->get( 'entry_styles_text' ),
					'fields'		=> array(
						array(
							'type'	=> 'layout-onion',
							'name'	=> 'layout_onion',
							'label'	=> 'entry_box_text'
						),
					)
				)
			)
		);
	}

	public function render( $settings = array(), $content = '' ) {
		$this->load->model( 'localisation/language' );
		$this->load->model( 'tool/image' );
   		$language = $this->model_localisation_language->getLanguage( $this->config->get('config_language_id') );
		$settings['get_items'] = array ();

		if( defined("IMAGE_URL")){
            $server =  IMAGE_URL;
        } else  {
            $server = ($this->request->server['HTTPS'] ? HTTPS_SERVER : HTTP_SERVER).'image/';
        }

		if (!empty ($settings['items'])) {
			foreach ($settings['items'] as $value) {
				if ( ! empty( $value['src'] ) ) {
					$settings['image_size'] = strtolower( $settings['image_size'] );
					$src = empty( $settings['image_size'] ) || $settings['image_size'] == 'full' ? $server . $value['src'] : false;

					if ( strpos( $settings['image_size'], 'x' ) ) {
						$src = $this->getImageLink($value['src'], $settings['image_size']);
					}

					$value['src'] = $src ? $src : '';
				}

				if (!empty ($value['languages'][$language['code']]['content'])) {
				$description = $value['languages'][$language['code']]['content'];
				$description = html_entity_decode( htmlspecialchars_decode( $description ), ENT_QUOTES, 'UTF-8' );
				}
				$settings['get_items'][] = array (
					'image'		=> isset($value['src']) ? $value['src'] : '',
					'button_link'	=> isset($value['button_link']) ? $value['button_link'] : '',
					'icon'		=> isset($value['icon']) ? $value['icon'] : '',
					'alt'		=> isset($value['alt']) ? $value['alt'] : '',
					'title'		=> isset($value['languages'][$language['code']]['title']) ? $value['languages'][$language['code']]['title'] : '',
					'content'	=> isset($description) ? $description : '',
					'button_text'	=> isset($value['languages'][$language['code']]['button_text']) ? $value['languages'][$language['code']]['button_text'] : '',
				);
			}
		}

		if (!empty($settings['layout'])) {
			$args = $this->renderLayout($settings['layout']);
		} else {
			$args = 'extension/module/pavobuilder/pa_image_text_carousel/pa_image_text_carousel';
		}

		$carouselOptions = array(
			'loop' => isset( $settings['loop'] ) && $settings['loop'] === 'true' ? true : false,
			'responsiveClass' => true,
			'items' => !empty($settings['item']) ? (int)$settings['item'] : 1,
			'rows' => !empty($settings['rows']) ? (int)$settings['rows'] : 1,
			'nav' => !empty($settings['nav']) && $settings['nav'] === 'true' ? (int)$settings['nav'] : false,
			'dots' => !empty($settings['pagination']) && $settings['pagination'] === 'true',
			'autoplay' => !empty($settings['auto_play']) && $settings['auto_play'] == 'true' ? true : false,
			'autoplayTimeout' => !empty($settings['auto_play_time']) ? $settings['auto_play_time'] : 2000,
			'responsive' => array(
				0 => array(
					'items' => !empty($settings['mobile']) ? $settings['mobile'] : 1,
					'nav' => true
				),
				481 => array(
					'items' => !empty($settings['table']) ? $settings['table'] : 1,
					'nav' => true
				),
				769 => array(
					'items' => !empty($settings['item']) ? $settings['item'] : 1,
					'nav' => !empty($settings['nav']) ? (int)$settings['nav'] : false,
				)
			),
			'margin' => !empty($settings['margin']) ? $settings['margin'] : 0,
			'stagePadding' => !empty($settings['padding']) ? $settings['padding'] : 0
		);
		$settings['carousel'] = $carouselOptions;
		return $this->load->view( $args, array( 'settings' => $settings  ) );
	}
}
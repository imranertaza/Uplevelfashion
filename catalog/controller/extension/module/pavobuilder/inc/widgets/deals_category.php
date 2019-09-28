<?php
class PA_Widget_Deals_Category extends PA_Widgets {

	public function fields() {
		$this->load->model('catalog/category');
		$get_category = $this->model_catalog_category->getCategories();
		$categories = array();
		$categories[] = array(
			'value' => 'all',
			'label' => 'All'
		);
		foreach ($get_category as $cat_id) {
			$categories[] = array(
				'value'	=> $cat_id['category_id'],
				'label'	=> $cat_id['name']
			);
		}
		return array(
			'mask'		=> array(
				'icon'	=> 'fa fa-tags',
				'label'	=> $this->language->get( 'entry_deals_category' )
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
							'default' => 'pa_deals_category',
							'options'	=> $this->getLayoutsOptions(),
							'none' 	=> false
						),
						array(
                            'type'  => 'text',
                            'name'  => 'extra_class',
                            'label' => $this->language->get( 'entry_extra_class_text' ),
                            'default' => '',
                            'desc'  => $this->language->get( 'entry_extra_class_desc_text' )
                        ),
						array(
							'type'		=> 'select',
							'name'		=> 'category',
							'label'		=> $this->language->get( 'entry_list_category' ),
							'default' 	=> 'all',
							'options'	=> $categories,
						),
						array(
							'type'		  => 'text',
							'name'		  => 'image_size',
							'label'		  => $this->language->get( 'entry_product_image_size_text' ),
							'desc'		  => $this->language->get( 'entry_image_size_desc' ),
							'default'	  => 'full',
							'placeholder' => '200x400'
						),
						array(
							'type'	  => 'number',
							'name'    => 'item',
							'label'	  => $this->language->get( 'entry_item_text' ),
							'desc'    => $this->language->get( 'entry_item_desc_text' ),
							'default' => 4
						),
						array(
							'type'		=> 'number',
							'name'		=> 'rows',
							'label'		=> $this->language->get( 'entry_rows_text' ),
							'default'	=> 1
						),
						array(
							'type'		=> 'number',
							'name'		=> 'limit',
							'label'		=> $this->language->get( 'entry_limit_text' ),
							'default'	=> 8
						),
						array(
							'type'		=> 'select',
							'name'		=> 'loop',
							'label'		=> $this->language->get( 'entry_loop' ),
							'desc'		=> $this->language->get( 'entry_loop_desc' ),
							'default' 	=> 'false',
							'options'	=> array(
								array(
									'value'	=> 'true',
									'label'	=> 'Enabled'
								),
								array(
									'value'	=> 'false',
									'label'	=> 'Disabled'
								)
							)
						),
						array(
							'type'		=> 'select',
							'name'		=> 'auto_play',
							'label'		=> $this->language->get( 'entry_auto_play' ),
							'default' 	=> 'false',
							'options'	=> array(
								array(
									'value'	=> 'true',
									'label'	=> 'Enabled'
								),
								array(
									'value'	=> 'false',
									'label'	=> 'Disabled'
								)
							)
						),
						array(
							'type'	  => 'number',
							'name'	  => 'auto_play_time',
							'label'	  => $this->language->get( 'entry_auto_play_time' ),
							'default' => 5000
						),
						array(
                            'type'  => 'image',
                            'name'  => 'banner_image',
                            'label' => $this->language->get( 'entry_banner_image' )
                        ),
                        array(
                            'type'  => 'text',
                            'name'  => 'banner_size',
                            'label' => $this->language->get( 'entry_banner_image_size' ),
                            'desc'  => $this->language->get( 'entry_banner_image_desc' ),
                            'default'       => 'full',
                            'placeholder'   => '200x400'
                        ),
                        array(
							'type'		=> 'select',
							'name'		=> 'countdown',
							'label'		=> $this->language->get( 'entry_countdown_text' ),
							'default' 	=> 'false',
							'options'	=> array(
								array(
									'value'	=> 'true',
									'label'	=> 'Enabled'
								),
								array(
									'value'	=> 'false',
									'label'	=> 'Disabled'
								)
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
						array(
							'type'	=> 'colorpicker',
							'name'	=> 'color',
							'label'	=> $this->language->get( 'entry_color_text' )
						)
					)
				)
			)
		);
	}

	public function render( $settings = array(), $content = '' ) {
		$this->load->model( 'tool/image' );
		$this->load->model('catalog/product');
		$this->load->model('catalog/category');
		$this->load->model( 'extension/module/pavdealscategory' );
		$this->load->model( 'extension/module/pavobuilder' );
		$this->load->language( 'extension/module/pavobuilder' );
		$this->document->addScript('catalog/view/javascript/jquery.countdown.min.js' );
	    
	    $this->document->addScript('catalog/view/javascript/jquery/swiper/js/swiper.min.js');
	    
		$settings['products'] = array ();
		$filter_data = array (
			'start'  => 0,
			'limit'  => $settings['limit']
		);

		if( defined("IMAGE_URL")){
            $server =  IMAGE_URL;
        } else  {
            $server = ($this->request->server['HTTPS'] ? HTTPS_SERVER : HTTP_SERVER).'image/';
        }

		$settings['server'] = $server;

		$settings['server'] = $server;
		if (empty($settings['category'])) {
			$settings['category'] = "all";
		}
		
		if (!empty($settings['category'])) {
			 if ($settings['category'] == "all") { 
				$specials = $this->model_extension_module_pavdealscategory->getAllProductSpecials($filter_data);
			}
			 else { 
				$specials = $this->model_extension_module_pavdealscategory->getProductSpecials($filter_data,$settings['category']);
			}
			if ($settings['category'] == "all") {
				$settings['cat_name'] = "All";
			} else {
				$cat_name = $this->model_catalog_category->getCategory($settings['category']);
				$settings['cat_name'] = isset($cat_name['name']) ? $cat_name['name'] : '';
				$settings['category_link']	= $this->url->link('product/category','path=' .$settings['category']); 
			}

			foreach ($specials as $get_special) {
				if ( ! empty( $get_special['image'] ) ) {
		                $settings['image_size'] = strtolower( $settings['image_size'] );
		                $src = empty( $settings['image_size'] ) || $settings['image_size'] == 'full' ? $server . $get_special['image'] : false;
		                if ( strpos( $settings['image_size'], 'x' ) ) {
		                    $src = $this->getImageLink($get_special['image'], $settings['image_size']);
		                }

		                $get_special['image'] = $src ? $src : $get_special['image'];
		            }

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($get_special['price'], $get_special['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$get_special['special']) {
					$special = $this->currency->format($this->tax->calculate($get_special['special'], $get_special['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$discount = floor((($get_special['price']-$get_special['special'])/$get_special['price'])*100);
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$get_special['special'] ? $get_special['special'] : $get_special['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
		            $rating = $get_special['rating'];
		        } else {
		            $rating = false;
		        }
		        $images = $this->model_catalog_product->getProductImages( $get_special['product_id'] );
		        $output = array();
		        if( $images ){
		            foreach( $images as $timage ){
		                if ($timage['image']) {
		                	$tmp = $this->getImageLink( $timage['image'], $settings['image_size'] );                  
		                } else {
		                	$tmp = $this->getImageLink( 'placeholder.png', $settings['image_size'] );                        
		                }   
		                $output[] = $tmp;
		            }
		        }

				$sold = $this->model_extension_module_pavobuilder->getTotalBought( $get_special['product_id'] );
				
				$settings['products'][] = array (
					'images'      => $output,
					'product_id'  => (int)$get_special['product_id'],
					'thumb'       => $get_special['image'],
					'name'        => $get_special['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($get_special['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'percentage'  => 100-floor( (float)$get_special['special']*100/(float)$get_special['price']),
					'discount'   => isset($discount)?'-'.$discount.'%':'',
					'minimum'     => $get_special['minimum'] > 0 ? $get_special['minimum'] : 1,
					'rating'      => $get_special['rating'],
					'sold'		  => isset($sold) ? (int)$sold : 0,
					'href'        => $this->url->link('product/product', 'product_id=' . $get_special['product_id'] ),
					'date_start'  => $get_special['date_start'],
					'date_end'    => $get_special['date_end'],
					'date_added'  => $get_special['date_added'],
					'reviews'     => sprintf($this->language->get('text_reviews'), (int)$get_special['reviews']),
					'countdown'	  => isset ($settings['countdown']) ? $settings['countdown'] : "false",
				);
			}
		}

		if ( ! empty( $settings['banner_image'] ) ) {
			$settings['banner_size'] = strtolower( $settings['banner_size'] );
				$srcs = empty( $settings['banner_size'] ) || $settings['banner_size'] == 'full' ? $server . $settings['banner_image'] : false;
				if ( strpos( $settings['banner_size'], 'x' ) ) {
					$srcs = $this->getImageLink($settings['banner_image'], $settings['banner_size']);
			}
				$settings['banner_image'] = $srcs ? $srcs : $settings['banner_image'];
		}

		$settings['product_grid_layout'] = ''; 
		$file = DIR_APPLICATION . 'view/theme/'. $this->config->get('config_theme').'/template/product/layout/'. $this->config->get( 'pavothemer_product_grid_layout' ) .'.twig'; 
	  	if( file_exists( $file ) ){  
			$settings['product_grid_layout'] = $this->config->get('config_theme').'/template/product/layout/'. $this->config->get( 'pavothemer_product_grid_layout' ) .'.twig'; 
	  	}

	  	if (!empty($settings['layout'])) {
			$args = $this->renderLayout($settings['layout']);
		} else {
			$args = 'extension/module/pavobuilder/pa_deals_category/pa_deals_category';
		}
	  	
		return $this->load->view( $args, array( 'settings' => $settings, 'content' => $content ) );
	}
}
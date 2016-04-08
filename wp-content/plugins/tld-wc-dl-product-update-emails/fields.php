<?php
include_once('advanced-custom-fields/acf.php');

if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_product-options',
		'title' => 'Product Options',
		'fields' => array (
			array (
				'key' => 'field_56fe9d17eaca5',
				'label' => 'Send Email On Update',
				'name' => 'send_email_on_update',
				'type' => 'radio',
				'instructions' => 'Email customers when this download file is updated.',
				'choices' => array (
					'yes' => 'Yes',
					'no' => 'No',
				),
				'other_choice' => 0,
				'save_other_choice' => 0,
				'default_value' => 'no',
				'layout' => 'horizontal',
			),
		/*	array (
				'key' => 'field_56fe9cd6eaca4',
				'label' => 'Product ID',
				'name' => 'product_id',
				'type' => 'number',
				'instructions' => 'Please enter the ID of this product. You can find it in the address bar. <a href="http://google.com">Click Here</a>',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_56fe9d17eaca5',
							'operator' => '==',
							'value' => 'yes',
						),
					),
					'allorany' => 'all',
				),
				'default_value' => 'no',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'min' => '',
				'max' => '',
				'step' => '',
			),*/
			array (
				'key' => 'field_56fe9d96eaca6',
				'label' => 'I Updated The Download File',
				'name' => 'updated_the_download_file',
				'type' => 'radio',
				'instructions' => 'Select whether you updated the download file',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_56fe9d17eaca5',
							'operator' => '==',
							'value' => 'yes',
						),
					),
					'allorany' => 'all',
				),
				'choices' => array (
					'yes' => 'Yes',
					'no' => 'No',
				),
				'other_choice' => 0,
				'save_other_choice' => 0,
				'default_value' => 'no',
				'layout' => 'horizontal',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'product',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'side',
			'layout' => 'default',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
}

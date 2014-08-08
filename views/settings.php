<?php

/**
 * Subscribr settings page
 */
add_filter('subscribr_register_settings', 'subscribr_settings');

function subscribr_settings($settings) {

	$settings[] = array(
		'section_id'          => 'email-options',
		'section_title'       => __( 'Email Options', 'subscribr'),
		'section_description' => '',
		'section_order'       => 5,
		'fields'              => array(
			array(
				'id'          => 'text',
				'title'       => 'Text',
				'desc'        => 'This is a description.',
				'placeholder' => 'This is a placeholder.',
				'type'        => 'text',
				'std'         => 'This is std'
			),
			array(
				'id'          => 'password',
				'title'       => 'Password',
				'desc'        => 'This is a description.',
				'placeholder' => 'This is a placeholder.',
				'type'        => 'password',
				'std'         => 'Password'
			),
			array(
				'id'          => 'textarea',
				'title'       => 'Textarea',
				'desc'        => 'This is a description.',
				'placeholder' => 'This is a placeholder.',
				'type'        => 'textarea',
				'std'         => 'This is std'
			),
			array(
				'id'      => 'select',
				'title'   => 'Select',
				'desc'    => 'This is a description.',
				'type'    => 'select',
				'std'     => 'green',
				'choices' => array(
					'red'   => 'Red',
					'green' => 'Green',
					'blue'  => 'Blue'
				)
			),
			array(
				'id'      => 'radio',
				'title'   => 'Radio',
				'desc'    => 'This is a description.',
				'type'    => 'radio',
				'std'     => 'green',
				'choices' => array(
					'red'   => 'Red',
					'green' => 'Green',
					'blue'  => 'Blue'
				)
			),
			array(
				'id'    => 'checkbox',
				'title' => 'Checkbox',
				'desc'  => 'This is a description.',
				'type'  => 'checkbox',
				'std'   => 1
			),
			array(
				'id'      => 'checkboxes',
				'title'   => 'Checkboxes',
				'desc'    => 'This is a description.',
				'type'    => 'checkboxes',
				'std'     => array(
					'red',
					'blue'
				),
				'choices' => array(
					'red'   => 'Red',
					'green' => 'Green',
					'blue'  => 'Blue'
				)
			),
			array(
				'id'    => 'color',
				'title' => 'Color',
				'desc'  => 'This is a description.',
				'type'  => 'color',
				'std'   => '#ffffff'
			),
			array(
				'id'    => 'file',
				'title' => 'File',
				'desc'  => 'This is a description.',
				'type'  => 'file',
				'std'   => ''
			),
			array(
				'id'    => 'editor',
				'title' => 'Editor',
				'desc'  => 'This is a description.',
				'type'  => 'editor',
				'std'   => ''
			)
		)
	);



	$settings[] = array(
		'section_id'    => 'email',
		'section_title' =>  __( 'Email Options', 'subscribr'),
		'section_order' => 5,
		'fields'        => array(
			array(
				'id'    => 'more-text',
				'title' =>  __( 'Email Options', 'subscribr'),
				'desc'  =>  __( 'Email Options', 'subscribr'),
				'type'  => 'text',
				'std'   =>  __( 'Email Options', 'subscribr')
			),
		)
	);

	$settings[] = array(
		'section_id'    => 'tax',
		'section_title' =>  __( 'Types &amp; Taxonomies', 'subscribr'),
		'section_order' => 10,
		'fields'        => array(
			array(
				'id'    => 'more-text',
				'title' =>  __( 'Email Options', 'subscribr'),
				'desc'  =>  __( 'Email Options', 'subscribr'),
				'type'  => 'text',
				'std'   =>  __( 'Email Options', 'subscribr')
			),
		)
	);
	$settings[] = array(
		'section_id'    => 'general',
		'section_title' =>  __( 'General Options', 'subscribr'),
		'section_order' => 10,
		'fields'        => array(
			array(
				'id'    => 'more-text',
				'title' =>  __( 'Email Options', 'subscribr'),
				'desc'  =>  __( 'Email Options', 'subscribr'),
				'type'  => 'text',
				'std'   =>  __( 'Email Options', 'subscribr')
			),
		)
	);

	return $settings;
}

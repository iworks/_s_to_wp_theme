<?php
defined( 'ABSPATH' ) || exit;

return array(
				'general' => array(
					'default' => array (
						'section' => 'default',
						'fields' => array(
							'version' => array(
								'type' => 'string', // Valid values are 'string', 'boolean', 'integer', 'number', 'array', and 'object'.
								'default' => '0.0.0.0',
								'label' => esc_html__( 'Version', 'tadanero-gastro' ),
							),
							'phone' => array(
								'type' => 'phone',
								'label' => esc_html__( 'Phone', 'tadanero-gastro' ),
							),
						),
					),
				),
				'writing' => array(
					'default' => array(
						'fields'=>array(),
					),
					'post_via_email' => array(
						'fields'=>array(),
					),
				),
				'reading' => array(
					'default' => array(
						'fields'=>array(),
					),
				),
				'discussion' => array(
					'default' => array(
						'fields'=>array(),
					),
					'avatars' => array(
						'fields'=>array(),
					),
				),
				'media' => array(
					'default' => array(
						'fields'=>array(),
					),
					'embeds' => array(
						'fields'=>array(),
					),
					'uploads' => array(
						'fields'=>array(),
					),
				),
				'permalink' => array(
					'optional' => array(
						'fields'=>array(),
					),
				),
			);

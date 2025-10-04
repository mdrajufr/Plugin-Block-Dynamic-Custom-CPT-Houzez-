<?php
// This file is generated. Do not modify it manually.
return array(
	'houzez' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'create-block/houzez',
		'version' => '0.1.0',
		'title' => 'Houzez Properties',
		'category' => 'widgets',
		'icon' => 'admin-home',
		'description' => 'Display property listings with advanced real estate features.',
		'example' => array(
			
		),
		'supports' => array(
			'html' => false,
			'align' => array(
				'wide',
				'full'
			),
			'spacing' => array(
				'margin' => true,
				'padding' => true
			),
			'color' => array(
				'text' => true,
				'background' => true
			),
			'typography' => array(
				'fontSize' => true,
				'lineHeight' => true
			)
		),
		'attributes' => array(
			'postsToShow' => array(
				'type' => 'number',
				'default' => 6
			),
			'order' => array(
				'type' => 'string',
				'enum' => array(
					'ASC',
					'DESC'
				),
				'default' => 'DESC'
			),
			'orderBy' => array(
				'type' => 'string',
				'enum' => array(
					'date',
					'modified',
					'title',
					'price',
					'size'
				),
				'default' => 'date'
			),
			'layout' => array(
				'type' => 'string',
				'enum' => array(
					'grid',
					'list',
					'masonry',
					'carousel'
				),
				'default' => 'grid'
			),
			'columns' => array(
				'type' => 'number',
				'default' => 3
			),
			'showFeatured' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showPrice' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showLocation' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showSize' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showBedrooms' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showBathrooms' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showGarage' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showYearBuilt' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showAgent' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showStatus' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showTaxonomies' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showExcerpt' => array(
				'type' => 'boolean',
				'default' => true
			),
			'excerptLength' => array(
				'type' => 'number',
				'default' => 20
			),
			'showMeta' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showMap' => array(
				'type' => 'boolean',
				'default' => false
			),
			'imageSize' => array(
				'type' => 'string',
				'enum' => array(
					'thumbnail',
					'medium',
					'medium_large',
					'large',
					'full'
				),
				'default' => 'medium_large'
			),
			'pricePrefix' => array(
				'type' => 'string',
				'default' => '$'
			),
			'sizeSuffix' => array(
				'type' => 'string',
				'default' => 'sq ft'
			),
			'categoryFilter' => array(
				'type' => 'string',
				'default' => ''
			),
			'statusFilter' => array(
				'type' => 'string',
				'default' => ''
			),
			'featuredOnly' => array(
				'type' => 'boolean',
				'default' => false
			)
		),
		'textdomain' => 'houzez',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'viewScript' => 'file:./view.js',
		'render' => 'file:./render.php'
	)
);

<?php defined('SYSPATH') OR die('No direct script access.');

return array(

	// 分页配置
	'cms' => array(
		'current_page'      => array('source' => 'query_string', 'key' => 'page'), // source: "query_string" or "route"
		'total_items'       => 0,
		'items_per_page'    => 10,
		'view'              => 'Pagination.Digg',
		'auto_hide'         => TRUE,
		'first_page_in_url' => FALSE,
	),
);

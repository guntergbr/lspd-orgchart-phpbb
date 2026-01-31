<?php
/**
 *
 * Organizational Chart. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026, Gabriel
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace gunter\orgchart\acp;

/**
 * Organizational Chart ACP module info.
 */
class main_info
{
	public function module()
	{
		return [
			'filename'	=> '\gunter\orgchart\acp\main_module',
			'title'		=> 'ACP_ORGCHART_TITLE',
			'modes'		=> [
				'settings'	=> [
					'title'	=> 'ACP_ORGCHART',
					'auth'	=> 'ext_gunter/orgchart && acl_a_new_gunter_orgchart',
					'cat'	=> ['ACP_ORGCHART_TITLE'],
				],
			],
		];
	}
}

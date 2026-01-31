<?php
/**
 *
 * Organizational Chart. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026, Gabriel
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace gunter\orgchart\migrations;

class install_acp_module extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['gunter_orgchart_goodbye']);
	}

	public static function depends_on()
	{
		return ['\phpbb\db\migration\data\v320\v320'];
	}

	public function update_data()
	{
		return [
			['config.add', ['gunter_orgchart_groups', 'a:0:{}']],

			['module.add', [
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_ORGCHART_TITLE'
			]],
			['module.add', [
				'acp',
				'ACP_ORGCHART_TITLE',
				[
					'module_basename'	=> '\gunter\orgchart\acp\main_module',
					'modes'				=> ['settings'],
				],
			]],
		];
	}
}

<?php
/**
 *
 * Organizational Chart. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026, Gabriel
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace gunter\orgchart\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Organizational Chart Event listener.
 */
class main_listener implements EventSubscriberInterface
{
	/**
	 * Map phpBB core events to the listener methods that should handle those events
	 *
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		return [
			'core.user_setup'							=> 'load_language_on_setup',
			'core.page_header'							=> 'add_page_header_link',
			'core.viewonline_overwrite_location'		=> 'viewonline_page',
			'core.permissions'							=> 'add_permissions',
		];
	}

	/* @var \phpbb\language\language */
	protected $language;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\template\template */
	protected $template;

	/** @var string phpEx */
	protected $php_ext;

	/**
	 * Constructor
	 *
	 * @param \phpbb\language\language	$language	Language object
	 * @param \phpbb\controller\helper	$helper		Controller helper object
	 * @param \phpbb\template\template	$template	Template object
	 * @param string                    $php_ext    phpEx
	 */
	public function __construct(\phpbb\language\language $language, \phpbb\controller\helper $helper, \phpbb\template\template $template, $php_ext)
	{
		$this->language = $language;
		$this->helper   = $helper;
		$this->template = $template;
		$this->php_ext  = $php_ext;
	}

	/**
	 * Load common language files during user setup
	 *
	 * @param \phpbb\event\data	$event	Event object
	 */
	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = [
			'ext_name' => 'gunter/orgchart',
			'lang_set' => 'common',
		];
		$event['lang_set_ext'] = $lang_set_ext;
	}

	/**
	 * Add a link to the controller in the forum navbar
	 */
	public function add_page_header_link()
	{
		$this->template->assign_vars([
			'U_ORGCHART_PAGE'	=> $this->helper->route('gunter_orgchart_controller', ['name' => 'index']),
		]);
	}

	/**
	 * Show users viewing Organizational Chart page on the Who Is Online page
	 *
	 * @param \phpbb\event\data	$event	Event object
	 */
	public function viewonline_page($event)
	{
		if ($event['on_page'][1] === 'app' && strrpos($event['row']['session_page'], 'app.' . $this->php_ext . '/orgchart') === 0)
		{
			$event['location'] = $this->language->lang('VIEWING_GUNTER_ORGCHART');
			$event['location_url'] = $this->helper->route('gunter_orgchart_controller', ['name' => 'index']);
		}
	}

	/**
	 * Add permissions to the ACP -> Permissions settings page
	 * This is where permissions are assigned language keys and
	 * categories (where they will appear in the Permissions table):
	 * actions|content|forums|misc|permissions|pm|polls|post
	 * post_actions|posting|profile|settings|topic_actions|user_group
	 *
	 * Developers note: To control access to ACP, MCP and UCP modules, you
	 * must assign your permissions in your module_info.php file. For example,
	 * to allow only users with the a_new_gunter_orgchart permission
	 * access to your ACP module, you would set this in your acp/main_info.php:
	 *    'auth' => 'ext_gunter/orgchart && acl_a_new_gunter_orgchart'
	 *
	 * @param \phpbb\event\data	$event	Event object
	 */
	public function add_permissions($event)
	{
		$permissions = $event['permissions'];

		$permissions['a_new_gunter_orgchart'] = ['lang' => 'ACL_A_NEW_GUNTER_ORGCHART', 'cat' => 'misc'];
		$permissions['m_new_gunter_orgchart'] = ['lang' => 'ACL_M_NEW_GUNTER_ORGCHART', 'cat' => 'post_actions'];
		$permissions['u_new_gunter_orgchart'] = ['lang' => 'ACL_U_NEW_GUNTER_ORGCHART', 'cat' => 'post'];

		$event['permissions'] = $permissions;
	}
}

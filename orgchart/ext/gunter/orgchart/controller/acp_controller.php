<?php
/**
 *
 * Organizational Chart. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026, Gabriel
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace gunter\orgchart\controller;

/**
 * Organizational Chart ACP controller.
 */
class acp_controller
{
    /** @var \phpbb\config\config */
    protected $config;

    /** @var \phpbb\language\language */
    protected $language;

    /** @var \phpbb\log\log */
    protected $log;

    /** @var \phpbb\request\request */
    protected $request;

    /** @var \phpbb\template\template */
    protected $template;

    /** @var \phpbb\user */
    protected $user;

    /** @var string Custom form action */
    protected $u_action;

    /** @var \phpbb\db\driver\driver_interface */
    protected $db;

    /**
     * Constructor.
     *
     * @param \phpbb\config\config		$config		Config object
     * @param \phpbb\language\language	$language	Language object
     * @param \phpbb\log\log			$log		Log object
     * @param \phpbb\request\request	$request	Request object
     * @param \phpbb\template\template	$template	Template object
     * @param \phpbb\user				$user		User object
     */
    public function __construct(\phpbb\config\config $config, \phpbb\language\language $language, \phpbb\log\log $log, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, \phpbb\db\driver\driver_interface $db)
    {
        $this->config = $config;
        $this->language = $language;
        $this->log = $log;
        $this->request = $request;
        $this->template = $template;
        $this->user = $user;
        $this->db = $db;
    }

    /**
     * Display the options a user can configure for this extension.
     *
     * @return void
     */
    public function display_options()
    {
        // Load language file
        $this->language->add_lang('common', 'gunter/orgchart');

        // Create form key to prevent CSRF
        add_form_key('gunter_orgchart_acp');

        $errors = [];

        // Get groups from database
        $sql = 'SELECT group_id, group_name FROM ' . GROUPS_TABLE . ' ORDER BY group_name ASC';
        $result = $this->db->sql_query($sql);

        $groups = [];
        while ($row = $this->db->sql_fetchrow($result)) {
            $groups[] = [
                'GROUP_ID' => (int) $row['group_id'],
                'GROUP_NAME' => $this->language->lang($row['group_name']),
            ];
        }
        $this->db->sql_freeresult($result);

        // Get previously saved groups
        $saved_groups = isset($this->config['gunter_orgchart_groups']) ? @unserialize($this->config['gunter_orgchart_groups']) : [];
        if (!is_array($saved_groups)) {
            $saved_groups = [];
        }

        // Check if the form was submitted
        if ($this->request->is_set_post('submit')) {
            // Check form key
            if (!check_form_key('gunter_orgchart_acp')) {
                $errors[] = $this->language->lang('FORM_INVALID');
            }

            if (empty($errors)) {
                // Save the selected groups (force integer and serialize)
                $selected_groups = array_map(
                    'intval',
                    $this->request->variable('gunter_orgchart_groups', [0 => 0])
                );

                $this->config->set('gunter_orgchart_groups', serialize($selected_groups));

                // Log debug info
                $this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'DEBUG: Saved ACP groups: ' . implode(',', $selected_groups));

                // Log admin action
                $this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_ACP_ORGCHART_SETTINGS');

                // Confirm save and redirect
                trigger_error($this->language->lang('ACP_ORGCHART_SETTING_SAVED') . adm_back_link($this->u_action));
            }
        }

        // Send variables to template
        $this->template->assign_vars([
            'S_ERROR' => !empty($errors),
            'ERROR_MSG' => !empty($errors) ? implode('<br>', $errors) : '',
            'U_ACTION' => $this->u_action,
            'GROUPS' => $groups,
            'SELECTED_GROUPS' => $saved_groups,
        ]);
    }




    /**
     * Set custom form action.
     *
     * @param string	$u_action	Custom form action
     * @return void
     */
    public function set_page_url($u_action)
    {
        $this->u_action = $u_action;
    }
}

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
        // Încarcă fișierele de limbă
        $this->language->add_lang('common', 'gunter/orgchart');

        // Crează form key pentru prevenirea CSRF
        add_form_key('gunter_orgchart_acp');

        $errors = [];

        // Preluăm grupurile din baza de date
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

        // Preluăm grupurile salvate anterior
        $saved_groups = isset($this->config['gunter_orgchart_groups']) ? @unserialize($this->config['gunter_orgchart_groups']) : [];
        if (!is_array($saved_groups)) {
            $saved_groups = [];
        }

        // Dacă s-a trimis formularul
        if ($this->request->is_set_post('submit')) {
            // Verificăm form key
            if (!check_form_key('gunter_orgchart_acp')) {
                $errors[] = $this->language->lang('FORM_INVALID');
            }

            if (empty($errors)) {

                // Salvăm grupurile selectate (forțăm integer și serializare)
                $selected_groups = array_map(
                    'intval',
                    $this->request->variable('gunter_orgchart_groups', [0 => 0])
                );

                $this->config->set('gunter_orgchart_groups', serialize($selected_groups));

                // Log debug sigur
                $this->log->add('admin', 0, '', 'DEBUG: Grupuri salvate ACP: ' . implode(',', $selected_groups));

                // Logăm acțiunea admin
                $this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_ACP_ORGCHART_SETTINGS');

                // Confirmare salvare și redirect
                trigger_error($this->language->lang('ACP_ORGCHART_SETTING_SAVED') . adm_back_link($this->u_action));
            }
        }

        // Trimitem variabile către template
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

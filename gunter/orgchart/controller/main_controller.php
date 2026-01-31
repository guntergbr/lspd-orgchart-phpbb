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
use Symfony\Component\HttpFoundation\Response;

class main_controller
{
    protected $config;
    protected $helper;
    protected $template;
    protected $language;
    protected $user;
    protected $auth;
    protected $db;
    protected $orgchart_table;
    protected $log;

    public function __construct(\phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\language\language $language, \phpbb\user $user, \phpbb\db\driver\driver_interface $db, \phpbb\auth\auth $auth, $orgchart_table, \phpbb\log\log $log)
    {
        $this->config = $config;
        $this->helper = $helper;
        $this->template = $template;
        $this->language = $language;
        $this->user = $user;
        $this->db = $db;
        $this->auth = $auth;
        $this->orgchart_table = $orgchart_table;
        $this->log = $log;
    }

    public function handle($name)
    {
        $saved_groups = [];

        // Get saved groups from config
        if (!empty($this->config['gunter_orgchart_groups'])) {
            $tmp = @unserialize($this->config['gunter_orgchart_groups']);
            if (is_array($tmp)) {
                $saved_groups = $tmp;
            }
        }

        // Get org chart nodes
        $nodes = $this->getOrgChartNodes();

        // Check edit permission
        $editPermission = $this->hasEditPermission($saved_groups);

        // Add form key
        add_form_key('form');

        // Assign variables to template
        $this->template->assign_vars([
            'ORGCHART_NODES' => json_encode($nodes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'ORGCHART_EDIT_PERMISSION' => $editPermission,
            'ORGCHART_CHIEF_NAME' => $this->config['gunter_orgchart_chief_name'] ?? 'VACANT',
            'ORGCHART_DP_DATE' => $this->config['gunter_orgchart_dp_date'] ?? 'Unknown DP',
        ]);

        // Render template
        return $this->helper->render('@gunter_orgchart/orgchart_body.html', $name);
    }

    /**
     * Summary of get_user_groups
     * @return array
     */
    protected function get_user_groups()
    {
        // Get user ID
        $user_id = (int) $this->user->data['user_id'];

        // Query to get user groups
        $sql = 'SELECT g.group_id, g.group_name
        FROM ' . USER_GROUP_TABLE . ' ug
        JOIN ' . GROUPS_TABLE . ' g
            ON g.group_id = ug.group_id
        WHERE ug.user_id = ' . (int) $user_id . '
          AND ug.user_pending = 0';

        // Execute query
        $result = $this->db->sql_query($sql);

        // Fetch groups
        $groups = [];

        while ($row = $this->db->sql_fetchrow($result)) {
            $groups[] = $row; // id + name
        }

        // Free result
        $this->db->sql_freeresult($result);
        return $groups;
    }

    /**
     * Summary of user_in_group
     * @param mixed $group_id
     * @return bool
     */
    protected function user_in_group($group_id)
    {
        // Get user groups
        $groups = $this->get_user_groups();

        // Check if user is in the specified group
        foreach ($groups as $g) {
            if ((int) $g['group_id'] === (int) $group_id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Summary of hasEditPermission
     * @param array $saved_groups
     * @return bool
     */
    function hasEditPermission(array $saved_groups)
    {
        // If no groups are set, no one has edit permission
        if (empty($saved_groups)) {
            return false;
        }

        // Check if user is in any of the saved groups
        $editPermission = false;

        foreach ($saved_groups as $group_id) {
            if ($this->user_in_group($group_id)) {
                $editPermission = true;
                break;
            }
        }

        return $editPermission;
    }

    /**
     * Summary of getOrgChartNodes
     * @return array
     */
    function getOrgChartNodes()
    {
        // Fetch all nodes from the org chart table
        $nodes = [];

        $sql = 'SELECT
        id,
        parent_id,
        department,
        title,
        rank,
        name,
        rank_img,
        picture_img
        FROM ' . $this->orgchart_table . '
        ORDER BY id ASC';

        $result = $this->db->sql_query($sql);

        // Build nodes array
        while ($row = $this->db->sql_fetchrow($result)) {
            $nodes[] = [
                'id' => (int) $row['id'],
                'parentId' => ((int) $row['parent_id'] === 0) ? null : (int) $row['parent_id'],
                'department' => $row['department'],
                'title' => $row['title'],
                'rank' => $row['rank'],
                'name' => $row['name'],
                'rankImg' => $row['rank_img'],
                'pictureImg' => $row['picture_img'],
            ];
        }

        $this->db->sql_freeresult($result);

        return $nodes;
    }

    /**
     * Summary of add_node
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function add_node()
    {
        // Get data from request
        $data = json_decode(file_get_contents('php://input'), true);

        // Insert new node into database
        $sql = 'INSERT INTO ' . $this->orgchart_table . ' ' .
            $this->db->sql_build_array('INSERT', [
                'parent_id' => $data['parentId'] !== null ? (int) $data['parentId'] : null,
                'department' => $data['department'],
                'title' => $data['title'],
                'rank' => $data['rank'],
                'name' => $data['name'],
                'rank_img' => 'police_officer.png',
                'picture_img' => '',
            ]);

        $this->db->sql_query($sql);
        $id = (int) $this->db->sql_nextid();

        $this->log->add('mod', $this->user->data['user_id'], $this->user->ip, 'LOG_ACP_ORGCHART_NODE_ADDED', time());

        return new \Symfony\Component\HttpFoundation\JsonResponse([
            'id' => $id
        ]);
    }

    /**
     * Summary of edit_node
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function edit_node()
    {
        // Get data from request
        $data = json_decode(file_get_contents('php://input'), true);

        // Update node in database
        $sql = 'UPDATE ' . $this->orgchart_table . '
            SET ' . $this->db->sql_build_array('UPDATE', [
                        'department' => $data['department'],
                        'title' => $data['title'],
                        'rank' => $data['rank'],
                        'name' => $data['name'],
                        'rank_img' => $data['rankImg'],
                        'picture_img' => $data['pictureImg'],
                    ]) . '
            WHERE id = ' . (int) $data['id'];

        $this->db->sql_query($sql);

        $this->log->add('mod', $this->user->data['user_id'], $this->user->ip, 'LOG_ACP_ORGCHART_NODE_EDITED', time(), [
            (int) $data['id'],
            $data['name'],
            $data['department'],
            $data['title'],
            $data['rank'],
        ]);

        return new \Symfony\Component\HttpFoundation\JsonResponse(['ok' => true]);
    }

    /**
     * Summary of delete_node
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function delete_node()
    {
        // Get data from request
        $data = json_decode(file_get_contents('php://input'), true);
        $id = (int) $data['id'];

        $to_delete = [$id];

        // Find all child nodes recursively
        for ($i = 0; $i < count($to_delete); $i++) {
            $pid = $to_delete[$i];

            $sql = 'SELECT id FROM ' . $this->orgchart_table . '
                WHERE parent_id = ' . $pid;

            $result = $this->db->sql_query($sql);
            while ($row = $this->db->sql_fetchrow($result)) {
                $to_delete[] = (int) $row['id'];
            }
            $this->db->sql_freeresult($result);
        }

        // Delete all nodes in $to_delete
        $sql = 'DELETE FROM ' . $this->orgchart_table . '
            WHERE ' . $this->db->sql_in_set('id', $to_delete);

        $this->db->sql_query($sql);

        $this->log->add('mod', $this->user->data['user_id'], $this->user->ip, 'LOG_ACP_ORGCHART_NODE_DELETED', time(), [(int) $id]);

        return new \Symfony\Component\HttpFoundation\JsonResponse(['ok' => true]);
    }
}

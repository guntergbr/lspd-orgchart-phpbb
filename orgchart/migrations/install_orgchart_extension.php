<?php
namespace gunter\orgchart\migrations;

class install_orgchart_extension extends \phpbb\db\migration\migration
{
    public function effectively_installed()
    {
        return isset($this->db_tools) &&
            $this->db_tools->sql_table_exists($this->table_prefix . 'orgchart_nodes');
    }

    static public function depends_on()
    {
        return ['\phpbb\db\migration\data\v330\v330'];
    }

    public function update_schema()
    {
        return [
            'add_tables' => [
                $this->table_prefix . 'orgchart_nodes' => [
                    'COLUMNS' => [
                        'id' => ['UINT', null, 'auto_increment'],
                        'parent_id' => ['UINT', null, 'null'],
                        'department' => ['VCHAR:255', ''],
                        'title' => ['VCHAR:255', ''],
                        'rank' => ['VCHAR:255', ''],
                        'name' => ['VCHAR:255', ''],
                        'rank_img' => ['VCHAR:255', ''],
                        'picture_img' => ['VCHAR:255', ''],
                    ],
                    'PRIMARY_KEY' => 'id',
                ],
            ],
        ];
    }

    public function revert_schema()
    {
        return [
            'drop_tables' => [
                $this->table_prefix . 'orgchart_nodes',
            ],
        ];
    }
}

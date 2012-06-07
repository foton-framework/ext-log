<?php



class EXT_Log extends SYS_Model_Database
{
	//--------------------------------------------------------------------------

	public $table = 'log';
	public $name  = 'Data log';
	public $add_action = FALSE;

	//--------------------------------------------------------------------------

	public function init()
	{
		$this->fields[$this->table] = array(
			'id'     => NULL,
			'uid'    => NULL,
			'status' => array(
				'label'   => 'Статус',
				'default' => 1,
				'field'   => 'radiogroup',
				'options' => 'status_list',
				'user_group' => array(1)
			),
			'postdate' => array(
				'label'   => 'Дата публикации',
				'default' => time(),
			),
			'editdate' => array(
				'label'   => 'Дата последнего изменения',
				'default' => time(),
			),
			'type' => array(
				'label'  => 'Type',
				'field'  => 'input',
				'rules'  => 'trim|required|strip_tags'
			),
			'pid' => array(
				'label'  => 'PID',
				'field'  => 'input',
				'rules'  => 'trim|required|strip_tags|numeric'
			),
		);
	}
	
	//--------------------------------------------------------------------------

	/**
	 * Логгирует действия администратора. Создание, удаление и изменение статуса
	 * @param  string  $call_str Строка вызова модели (model.news или ext.sub)
	 * @param  integer $pid      ID объекта
	 * @param  integer $status   Изменение статуса объекта. 0: откл; 1: вкл; -1: удален
	 * @param  integer $uid      ID пользователя (админа)
	 * @return void
	 */
	public function log($call_str, $pid, $status = 1, $uid = 0)
	{
		$data = array(
			'status'   => $status,
			'editdate' => time(),
			'uid'      => $uid ? $uid : $this->user->id
		);
		$this->db->query('LOCK TABLES log WRITE');
		if ($this->db->where('type=? AND pid=?', $call_str, $pid)->count_all('log'))
		{
			$this->db->where('type=? AND pid=?', $call_str, $pid);
			$this->update(NULL, $data);
		}
		else
		{
			$data['type'] = $call_str;
			$data['pid']  = $pid;
			$this->insert(NULL, $data);
		}
		$this->db->query('UNLOCK TABLES');
	}

	//--------------------------------------------------------------------------

	public function &prepare_row_result(&$row)
	{
		$row = parent::prepare_row_result($row);
		
//		$row->full_link = '/comname/' . $row->id . '/';
		
		return $row;
	}
	
	//--------------------------------------------------------------------------
	
	public function get($table = NULL, $where = NULL)
	{
		if (TRUE || $this->user->group_id != 1)
		{
			$this->db->where('status=1');
		}
		
		$this->db->order_by('postdate DESC');
		
		return parent::get($table, $where);
	}
	
	//--------------------------------------------------------------------------
	
	public function status_list($val = NULL)
	{
		static $list = array(
			0 => 'Отключен',
			1 => 'Включен'
		);
		
		if ($val !== NULL) return $list[$val];
		
		return $list;
	}

	//--------------------------------------------------------------------------

}
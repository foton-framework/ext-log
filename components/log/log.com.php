<?php


class EXT_COM_Log extends SYS_Component
{

	//--------------------------------------------------------------------------

	// public $valid_types   = array();
	public $exclude_types = array();

	//--------------------------------------------------------------------------

	function init()
	{
		$this->load->extension('log');
	}

	//--------------------------------------------------------------------------

	function box($limit = 10)
	{
		$data = array();
		
		$this->db->where('uid>0'); // Do not load deprecated rows
		$this->db->where('pid>0 AND status=1')->where_not_in('type', $this->exclude_types)->limit($limit);
		$log_result = $this->log->get_result();
		foreach ($log_result as $row)
		{
//			if (in_array($row->type, $this->exclude_types)) continue;
			$data[$row->type . $row->pid] = $row;
		}
		
		
		$parents_ids = array();
		foreach ($log_result as $row)
		{
			$parents_ids[$row->type][$row->pid] = $row->pid;
		}

		
		foreach ($parents_ids as $type => $ids)
		{
			$parent_model =& sys::call($type);

			$this->db->where_in($parent_model->table . '.id', $ids);
			$parent_data = $parent_model->get_result();
			
			foreach ($parent_data as $row)
			{
				$key = $type . $row->id;
				$data[$key]->model_name = $parent_model->name;
				$data[$key]->data = $row;
			}
		}
		
		
		$this->data['data'] = $data;
		$this->data['cols'] = 3;
	}

	//--------------------------------------------------------------------------

}
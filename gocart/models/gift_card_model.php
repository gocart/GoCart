<?php

class Gift_card_model extends CI_Model 
{
	function __construct()
	{
		parent::__construct();
	}
	
	// check the expiration date and/or balance 
	function is_valid($card)	
	{
		
		if($card->activated == 0) return false;
		
		// check for zero balance
		if($this->get_balance($card) == 0) return false;
		
		// check expiry date.. not required
		if($card->expiry_date!="0000-00-00")
		{
			$e_date = split("-", $card->expiry_date);
			$end = mktime(0,0,0, $e_date[1], (int) $e_date[2] +1 , $e_date[0]); // add a day to account for the end date as the last viable day
			$current = time();
		
			if($current > $end) return false; 
		}
		return true;
	}
	
	
	// update the card records
	function update_used_card_balances($gc_list)
	{
		foreach($gc_list as $code=>$card)
		{
			if(isset($card['amt_used'])) {
				$this->db->where('code', $code);
				$this->db->set('amount_used', $card['amt_used']);
				$this->db->update('gift_cards');
			}
		}
	}
	
	function activate($code)
	{
		$this->db->where('code', $code);
		$this->db->set('activated', '1');
		$this->db->update('gift_cards');
	}
	
	function delete($id)
	{
		$this->db->where('id', $id);
		$this->db->delete('gift_cards');
	}
		
	function get_all_new() 
	{
		$this->db->select('gift_cards.*, orders.status', false);
		$this->db->from('gift_cards');
		$this->db->join('orders', 'gift_cards.order_number = orders.order_number', 'left');
		$this->db->order_by('gift_cards.id', 'DESC');
		$res = $this->db->get();
		$cards = $res->result_array();		
		return $cards;
	}
	
	function save_card($data) 
	{
		$this->db->insert('gift_cards', $data);
	}
	
	function get_balance($card)
	{
		return (float) $card->beginning_amount - (float) $card->amount_used;
	}
	
	function get_gift_card($code)
	{
		$this->db->where('code', $code);
		$res = $this->db->get('gift_cards');
		return $res->row();
	}
	
	function send_notification($gc_data)
	{
		
		$this->load->helper('formatting_helper');
		$row = $this->db->where('id', '1')->get('canned_messages')->row_array();

		// set replacement values for subject & body
		$row['subject']	= str_replace('{from}', $gc_data['from'], $row['subject']);
		$row['subject']	= str_replace('{site_name}', $this->config->item('company_name'), $row['subject']);

		$row['content']	= str_replace('{code}', $gc_data['code'], $row['content']);
		$row['content']	= str_replace('{amount}', format_currency($gc_data['beginning_amount']), $row['content']);
		$row['content']	= str_replace('{from}', $gc_data['from'], $row['content']);
		$row['content']	= str_replace('{personal_message}', nl2br($gc_data['personal_message']), $row['content']);
		$row['content']	= str_replace('{url}', $this->config->item('base_url'), $row['content']);
		$row['content']	= str_replace('{site_name}', $this->config->item('company_name'), $row['content']);

		$this->load->library('email');

		$config['mailtype'] = 'html';
		$this->email->initialize($config);

		$this->email->from($this->config->item('email'));
		$this->email->to($gc_data['to_email']);

		$this->email->subject($row['subject']);
		$this->email->message($row['content']);

		$this->email->send();
	}
	
	function is_active($code)
	{
		$this->db->where('code', $code);
		$res = $this->db->get('gift_cards');
		$row = $res->row();
		if($row)
		{
			return (bool) $row->activated;
		}
		else
		{
			return false;
		}
		
	}
	

}
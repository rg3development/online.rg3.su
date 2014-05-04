<?php
/*
 * Model of online site models
 * @author rav <arudyuk@rg3.su>
 * @version 1.0
 *
 */

class Online_mapper extends CI_Model {
	protected $_table_clients;
	protected $_table_sites;
	protected $_site_list;
	protected $_client_list;
	protected $_email;
	protected $_sms;
	protected $_sms_id_api;

	public function  __construct($id = 0) {
		parent::__construct();
		$this->_table_clients		= 'clients';
		$this->_table_sites			= 'sites';
		$this->_table_logs			= 'logs';
		$this->_client_list			= array();
		$this->_site_list			= array();
		$this->_sms_id_api			= 'a325b70e-68a5-ef44-cd98-56daf9ed8d68';
		$this->_email				= array('dev@rg3.su', 'arudyuk1985@yandex.ru');
		$this->_sms					= '79037558675';
		
		require_once('client_item.php');
		require_once('site_item.php');
		require_once('smsru.php');
	}

	public function get_all() {
		$sql = "select s.id id, s.url url, unix_timestamp(s.date_created) created, unix_timestamp(s.date_updated) updated,
						s.client_id client_id, c.name name, c.phone phone, c.email email, unix_timestamp(c.date_updated) client_updated, unix_timestamp(c.date_created) client_created
				from {$this->_table_sites} s left join {$this->_table_clients} c on c.id = s.client_id order by c.name asc";
		$res = $this->db->query($sql)->result_array();
		if (sizeof($res) == 0) return false;
		$site_object_array		= array();
		$client_object_array	= array();
		foreach ($res as $item) {
			$time_now = time();
			$status = $this->_get_site_info($item['url']);
			$sql = "insert into {$this->_table_logs} set site_id = '{$item['id']}', status = '{$status}'";
			$this->db->query($sql);
			// создаем объект пользователя
			$tmp_client				= new Client_item();
			$tmp_client->id			= $item['client_id'];
			$tmp_client->name		= $item['name'];
			$tmp_client->phone		= $item['phone'];
			$tmp_client->email		= $item['email'];;
			$tmp_client->created	= $item['client_created'];
			$tmp_client->updated	= $item['client_updated'];
			// создаем объект сайта
			$tmp_site				= new Site_item();
			$tmp_site->id			= $item['id'];
			$tmp_site->url			= $item['url'];
			$tmp_site->client		= $tmp_client;
			$tmp_site->created		= $item['created'];
			$tmp_site->updated		= $item['updated'];
			// создаем список объектов
			$client_object_array[]	= $tmp_client;
			$site_object_array[]	= $tmp_site;
		}
		$this->_client_list		= $client_object_array;
		$this->_site_list		= $site_object_array;
		// если все ок, возвращаем true
		return true;
	}
	
	public function get_clients() {
		return $this->_client_list;
	}
	
	public function get_sites() {
		return $this->_site_list;
	}
	
	protected function _get_site_info($url = '') {
		if (empty($url)) return false;
		$status = @get_headers($url, 1);
		if ($status[0] == 'HTTP/1.1 200 OK') return 'online';
		$this->_send_alert($url);
		return 'offline';
	}
	
	protected function _send_sms($url = '') {
		$sms = new \Zelenin\smsru($this->_sms_id_api);
		$sms->sms_send($this->_sms, 'Авария! Сайт '.$url.' упал!');
	}

	protected function _send_alert($url = '') {
		foreach ($this->_email as $address) {
			$this->email->clear();
			$this->email->to($address);
			$this->email->from('support@rg3.su');
			$this->email->subject('Авария!!! Сайт '.$url.' не отвечает.');
			$this->email->message('Offline статус сайта '.$url.'. Срочно примите меры!');
			$this->email->send();
			// Отправляем СМС оповещение
			$this->_send_sms($url);
		}
	}

}

<?php

class ControllerModuleGetresponse extends Controller
{
	private $allow_fields = array('telephone', 'country', 'city', 'address', 'postcode');
	private $custom_fields;

	public function index() {
		$form = $this->config->get('getresponse_form');

		if (!isset($form['active']) || $form['active'] == 0 || strlen($form['url']) < 15) {
			return false;
		}

		$data = array();
		$data['form_url'] = $form['url'];

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/getresponse.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/module/getresponse.tpl', $data);
		}

		return $this->load->view('default/template/module/getresponse.tpl', $data);
	}

	public function on_customer_add($customer_id) {
		$this->load->model('account/customer');
		$customer = $this->model_account_customer->getCustomer($customer_id);
		$settings = $this->config->get('getresponse_reg');
		$apikey = $this->config->get('getresponse_apikey');


		if ($settings['active'] == 0 || $customer['newsletter'] == 0) {
			return true;
		}

		$get_response = new GetResponseApiV3($apikey);
		$customs = array();
		$customs[] = array('customFieldId' => $this->getCustomFieldId('ref'), 'value' => array('OpenCart'));

		foreach ($this->allow_fields as $af) {
			if (!empty($row[$af])) {
				$customs[] = array('customFieldId' => $this->getCustomFieldId($af), 'value' => array($customer[$af]));
			}
		}

		$params = array(
				'name' => $customer['firstname'] . ' ' . $customer['lastname'],
				'email' => $customer['email'],
				'campaign' => array('campaignId' => $settings['campaign']),
				'customFieldValues' => $customs,
				'ipAddress' => $customer['ip']
		);

		if (isset($settings['sequence_active']) && $settings['sequence_active'] == 1 && isset($settings['day'])) {
			$params['dayOfCycle'] = (int)$settings['day'];
		}

		$get_response->addContact($params);

		return true;
	}

	private function getCustomFieldId($name) {
		$apikey = $this->config->get('getresponse_apikey');
		$get_response = new GetResponseApiV3($apikey);

		if (empty($this->custom_fields)) {
			$this->custom_fields = $get_response->getCustomFields();
		}

		if (!empty($get_response)) {
			foreach ($this->custom_fields as $custom) {
				if ($custom->name === $name) {
					return $custom->customFieldId;
				}
			}
		}

		$newCustom = array('name' => $name, 'type' => 'text', 'hidden' => false, 'values' => array());
		$result = $get_response->setCustomField($newCustom);

		if (!empty($this->custom_fields)) {
			$this->custom_fields[] = $result;
		}

		return $result->customFieldId;
	}
}

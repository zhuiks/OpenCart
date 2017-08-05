<?php

class ControllerExtensionModuleGetresponse extends Controller
{
	private $allow_fields = array('telephone', 'country', 'city', 'address', 'postcode');
	private $custom_fields;

	public function index() {
		$form = $this->config->get('module_getresponse_form');

		if (!isset($form['active']) || $form['active'] == 0 || strlen($form['url']) < 15) {
			return false;
		}

		$data = array();
		$data['form_url'] = $form['url'];

		return $this->load->view('extension/module/getresponse', $data);
	}

	public function on_customer_add($route, $data, $customer_id) {
		$customer = $data[0];
		$settings = $this->config->get('module_getresponse_reg');
		$apikey = $this->config->get('module_getresponse_apikey');


		if ($settings['active'] == 0 || $customer['newsletter'] == 0) {
			return $customer_id;
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
				//'ipAddress' => $customer['ip']
		);

		if (isset($settings['sequence_active']) && $settings['sequence_active'] == 1 && isset($settings['day'])) {
			$params['dayOfCycle'] = (int)$settings['day'];
		}

		$get_response->addContact($params);

		return $customer_id;
	}

	private function getCustomFieldId($name) {
		$apikey = $this->config->get('module_getresponse_apikey');
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

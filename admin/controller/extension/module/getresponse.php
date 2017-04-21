<?php

class ControllerExtensionModuleGetresponse extends Controller
{

	private $error = array();
	private $gr_apikey;
	private $get_response;
	private $campaigns;
	private $campaign;
	private $custom_fields = array();
	private $allow_fields = array('telephone', 'country', 'city', 'address', 'postcode');

	public function __construct($registry)
	{
		parent::__construct($registry);

		$this->gr_apikey = $this->config->get('getresponse_apikey');

		if (!empty($this->gr_apikey)) {
			$this->get_response = new GetResponseApiV3($this->gr_apikey);
		}
	}

	public function index() {
		$this->load->language('extension/module/getresponse');
		$this->load->model('localisation/language');
		$this->load->model('design/layout');
		$this->document->setTitle($this->language->get('heading_title'));

		$data = array();
		$data = $this->saveSettings($data);
		$data = $this->assignLanguage($data);
		$data = $this->assignSettings($data);
		$data = $this->assignBreadcrumbs($data);

		if (!empty($this->gr_apikey)) {
			$data = $this->assignAutoresponders($data);
			$data = $this->assignForms($data);
			$data['campaigns'] = $this->getCampaigns();
		}

		$data['layouts'] = $this->model_design_layout->getLayouts();
		$data['action'] = $this->url->link('extension/module/getresponse', 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/getresponse', $data));
	}

	private function assignAutoresponders($data) {
		$autoresponders = $this->get_response->getAutoresponders();
		$data['campaign_days'] = array();

		if (isset($autoresponders->httpStatus) && $autoresponders->httpStatus != 200) {
			$this->session->data['error_warning'] = $autoresponders->codeDescription;

			return $data;
		}

		if (!empty($autoresponders) && is_object($autoresponders)) {
			foreach ($autoresponders as $autoresponder) {
				if ($autoresponder->triggerSettings->dayOfCycle == null) {
					continue;
				}

				$data['campaign_days'][$autoresponder->triggerSettings->subscribedCampaign->campaignId][$autoresponder->triggerSettings->dayOfCycle] = array(
						'day' => $autoresponder->triggerSettings->dayOfCycle,
						'name' => $autoresponder->subject,
						'status' => $autoresponder->status
				);
			}
		}

		return $data;
	}

	private function assignForms($data) {
		$forms = $this->get_response->getForms();
		$new_forms = array();
		$old_forms = array();

		if (!empty($forms)) {
			foreach ($forms as $form) {
				if (isset($form->formId) && !empty($form->formId) && $form->status == 'published') {
					$new_forms[] = array('id' => $form->formId, 'name' => $form->name, 'url' => $form->scriptUrl);
				}
			}
		}

		$webforms = $this->get_response->getWebForms();

		if (!empty($webforms)) {
			foreach ($webforms as $form) {
				if (isset($form->webformId) && !empty($form->webformId) && $form->status == 'enabled') {
					$old_forms[] = array('id' => $form->webformId, 'name' => $form->name, 'url' => $form->scriptUrl);
				}
			}
		}

		$data['new_forms'] = $new_forms;
		$data['old_forms'] = $old_forms;

		return $data;
	}

	/**
	 * @param array $data
	 * @return array
	 */
	private function assignBreadcrumbs($data) {
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => false
		);
		$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_module'),
				'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => ' :: '
		);
		$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/getresponse', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => ' :: '
		);

		return $data;
	}

	/**
	 * @param array $data
	 * @return array
	 */
	private function assignLanguage($data) {
		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_module'] = $this->language->get('text_module');
		$data['text_success'] = $this->language->get('text_success');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_export'] = $this->language->get('entry_export');
		$data['entry_apikey'] = $this->language->get('entry_apikey');
		$data['entry_apikey_hint'] = $this->language->get('entry_apikey_hint');
		$data['entry_campaign'] = $this->language->get('entry_campaign');
		$data['entry_campaign_hint'] = $this->language->get('entry_campaign_hint');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_export'] = $this->language->get('button_export');
		$data['apikey_info'] = $this->language->get('apikey_info');
		$data['export_info'] = $this->language->get('export_info');
		$data['register_info'] = $this->language->get('register_info');
		$data['webform_info'] = $this->language->get('webform_info');
		$data['apikey_title'] = $this->language->get('apikey_title');
		$data['export_title'] = $this->language->get('export_title');
		$data['register_title'] = $this->language->get('register_title');
		$data['webform_title'] = $this->language->get('webform_title');
		$data['label_active'] = $this->language->get('label_active');
		$data['label_form'] = $this->language->get('label_form');
		$data['label_campaign'] = $this->language->get('label_campaign');
		$data['label_day_of_cycle'] = $this->language->get('label_day_of_cycle');
		$data['label_auto_queue'] = $this->language->get('label_auto_queue');
		$data['label_yes'] = $this->language->get('label_yes');
		$data['label_no'] = $this->language->get('label_no');
		$data['label_none'] = $this->language->get('label_none');
		$data['label_new_forms'] = $this->language->get('label_new_forms');
		$data['label_old_forms'] = $this->language->get('label_old_forms');
		$data['info_loading'] = $this->language->get('info_loading');
		$data['info_ajax_error'] = $this->language->get('info_ajax_error');

		return $data;
	}

	/**
	 * @param array $data
	 * @return array
	 */
	private function assignSettings($data) {
		$this->enable_module = $this->config->get('getresponse_enable_module');
		$this->campaign = $this->config->get('getresponse_campaign');

		$data['modules'] = $this->config->get('getresponse_module');

		if ($this->config->get('getresponse_form')) {
			$data['getresponse_form'] = $this->config->get('getresponse_form');
		} else {
			$data['getresponse_form'] = array('id' => 0, 'url' => '' , 'active' => 0);
		}

		if ($this->config->get('getresponse_reg')) {
			$data['getresponse_reg'] = $this->config->get('getresponse_reg');
		} else {
			$data['getresponse_reg'] = array('campaign' => '', 'day' => '', 'sequence_active' => 0);
		}

		if (isset($this->session->data['success'])) {
			$data['save_success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}

		if (isset($this->session->data['error_warning'])) {
			$data['error_warning'] = $this->session->data['error_warning'];
			unset($this->session->data['error_warning']);
		}

		$data['token'] = $this->session->data['token'];

		if (isset($data['getresponse_apikey']) && strlen($data['getresponse_apikey']) > 0 && isset($this->session->data['active_tab'])) {
            $data['active_tab'] = $this->session->data['active_tab'];
        } else {
            $data['active_tab'] = 'home';
        }

		$data['getresponse_apikey'] = $this->gr_apikey;
		$data['getresponse_campaign'] = $this->campaign;

		return $data;
	}

	/**
	 * Module settings to read and/or write config
	 */
	private function saveSettings($data) {
		$this->load->model('setting/setting');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!$this->checkApiKey($this->request->post['getresponse_apikey'])) {
				$this->session->data['error_warning'] = $this->language->get('error_incorrect_apikey');
			} else {
				$this->model_setting_setting->editSetting('getresponse', $this->request->post);
				$this->session->data['success'] = $this->language->get('text_success');
			}

			$this->session->data['active_tab'] = $this->request->post['getresponse_form']['current_tab'];

			$this->response->redirect(
					$this->url->link(
							'extension/module/getresponse', 'token=' . $this->session->data['token'],
							'SSL'
					)
			);
		}

		return $data;
	}

	/**
	 * @param $apikey
	 * @return bool
	 */
	private function checkApiKey($apikey) {
		if (empty($apikey)) {
			return false;
		} elseif ($this->config->get('getresponse_apikey') == $apikey) {
			return true;
		}

		$get_response = new GetResponseApiV3($apikey);
		$campaigns = $get_response->getCampaigns();

		return !(isset($campaigns->httpStatus) && $campaigns->httpStatus != 200);
	}

	/**
	 * Validate permission
	 *
	 * @return bool
	 */
	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/getresponse')) {
            $this->error['warning'] = $this->language->get('error_permission');
		}

		return (!$this->error);
	}

	/**
	 * Export contacts to campaign
	 */
	public function export() {

		$this->load->model('extension/getresponse');
		$contacts = $this->model_extension_getresponse->getContacts();
		$this->campaign = $this->request->post['campaign'];
		$gr_campaign = array();
		$campaigns = $this->getCampaigns();

		if (!empty($campaigns)) {
			foreach ($campaigns as $campaign) {
				if ($campaign->campaignId === $this->campaign) {
					$gr_campaign = $campaign;
				}
			}
		}

		if (empty($gr_campaign)) {
			$results = array('status' => 2, 'response' => '  No campaign with the specified name.');
		} else {
			$duplicated = 0;
			$queued = 0;
			$contact = 0;
			$not_added = 0;

			foreach ($contacts as $row) {
				$customs = array();
				$customs[] = array('customFieldId' => $this->getCustomFieldId('origin'), 'value' => array('OpenCart'));

				foreach ($this->allow_fields as $af) {
					$custom_field_id = $this->getCustomFieldId($af);
					if (!empty($row[$af]) && $custom_field_id !== false) {
						$customs[] = array('customFieldId' => $custom_field_id, 'value' => array($row[$af]));
					}
				}

				$grContact = $this->get_response->getContacts(
						array('query' => array('campaignId' => $gr_campaign->campaignId, 'email' => $row['email']))
				);

				$cycle_day = (!empty($grContact) && !empty($grContact->dayOfCycle)) ? $grContact->dayOfCycle : 0;

				$params = array(
						'name' => $row['firstname'] . ' ' . $row['lastname'],
						'email' => $row['email'],
						'dayOfCycle' => $cycle_day,
						'campaign' => array('campaignId' => $gr_campaign->campaignId),
						'customFieldValues' => $customs,
						'ipAddress' => empty($row['ip']) ? '127.0.0.1' : $row['ip']
				);

				try {
					$r = $this->get_response->addContact($params);

					if (is_object($r) && !isset($r->code)) {
                        $queued++;
                    } elseif (is_object($r) && isset($r->code) && $r->code == 1008) {
						$duplicated++;
					} else {
						$not_added++;
					}

					$contact++;
				} catch (Exception $e) {
					$not_added++;
				}
			}

			$results = array(
					'status' => 1,
					'response' => '  Export completed. Contacts: ' . $contact . '. Queued: ' . $queued . '. Updated: ' .
							$duplicated . '. Not added (Contact already queued): ' . $not_added . '.'
			);
		}

		$this->response->setOutput(json_encode($results));
	}

	private function getCustomFieldId($name) {
		if (empty($this->custom_fields)) {
			$custom_fields = $this->get_response->getCustomFields();

			if (!empty($custom_fields)) {
				foreach ($custom_fields as $field) {
					if (isset($field->customFieldId)) {
						$this->custom_fields[strtolower($field->name)] = $field->customFieldId;
					}
				}
			}
		}

		if (isset($this->custom_fields[$name])) {
			return $this->custom_fields[$name];
		}

		$newCustom = array('name' => $name, 'type' => 'text', 'hidden' => false, 'values' => array());

		$result = $this->get_response->setCustomField($newCustom);

		if (!empty($this->custom_fields) && isset($result->customFieldId)) {
			$this->custom_fields[$result->name] = $result->customFieldId;
			return $result->customFieldId;
		}
		return false;
	}

	/**
	 * @return array
	 */
	private function getCampaigns()
	{
		if (empty($this->gr_apikey)) {
			return array();
		}

		if (!empty($this->campaigns)) {
			return $this->campaigns;
		}

		$this->campaigns = $this->get_response->getCampaigns();

		if (isset($this->campaigns->httpStatus) && $this->campaigns->httpStatus != 200) {
			$this->session->data['error_warning'] = $this->campaigns->codeDescription;
			$this->campaigns = array();
		}

		return $this->campaigns;
	}

	public function install() {
		$this->load->model('extension/event');
		$this->model_extension_event->addEvent('getresponse', 'catalog/model/account/customer/addCustomer/after', 'extension/module/getresponse/on_customer_add');

    }

	public function uninstall() {
		$this->load->model('extension/event');
		$this->model_extension_event->deleteEvent('Getresponse');
	}
}
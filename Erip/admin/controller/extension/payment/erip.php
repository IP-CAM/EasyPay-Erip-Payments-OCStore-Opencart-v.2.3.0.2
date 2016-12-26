<?php

class ControllerExtensionPaymentErip extends Controller
{
    private $error = array();

    public function index()
    {
        $this->load->language('extension/payment/erip');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');


        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
            $this->model_setting_setting->editSetting('erip', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true));
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_edit'] = $this->language->get('text_edit');

        $data['entry_merchant'] = $this->language->get('entry_merchant');
        $data['entry_webkey'] = $this->language->get('entry_webkey');
        $data['entry_expires'] = $this->language->get('entry_expires');
        $data['entry_debug'] = $this->language->get('entry_debug');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_order_status_id'] = $this->language->get('entry_order_status_id');
        $data['entry_processing_status_id'] = $this->language->get('entry_processing_status_id');
        $data['entry_canceled_status_id'] = $this->language->get('entry_canceled_status_id');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['error_warning'] = $this->language->get('error_warning');

        $data['merchant_comment'] = $this->language->get('merchant_comment');
        $data['webkey_comment'] = $this->language->get('webkey_comment');
        $data['expires_comment'] = $this->language->get('expires_comment');
        $data['debug_comment'] = $this->language->get('debug_comment');


        $data['sort_order_label'] = $this->language->get('sort_order_label');


        $data['action'] = $this->url->link('extension/payment/erip', 'token=' . $this->session->data['token'], 'SSL');
        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', 'SSL');

        if (isset($this->error['merchant'])) {
            $data['error_merchant'] = $this->error['merchant'];
        } else {
            $data['error_merchant'] = '';
        }

        if (isset($this->error['webkey'])) {
            $data['error_webkey'] = $this->error['webkey'];
        } else {
            $data['error_webkey'] = '';
        }

        if (isset($this->error['expires'])) {
            $data['error_expires'] = $this->error['expires'];
        } else {
            $data['error_expires'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/erip', 'token=' . $this->session->data['token'], true)
        );


        $data['action'] = $this->url->link('extension/payment/erip', 'token=' . $this->session->data['token'], 'SSL');

        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', 'SSL');

        if (isset($this->request->post['erip_merchant'])) {
            $data['erip_merchant'] = $this->request->post['erip_merchant'];
        } else {
            $data['erip_merchant'] = $this->config->get('erip_merchant');
        }

        if (isset($this->request->post['erip_webkey'])) {
            $data['erip_webkey'] = $this->request->post['erip_webkey'];
        } else {
            $data['erip_webkey'] = $this->config->get('erip_webkey');
        }

        if (isset($this->request->post['erip_expires'])) {
            $data['erip_expires'] = $this->request->post['erip_expires'];
        } elseif ($this->config->has('erip_expires')) {
            $data['erip_expires'] = $this->config->get('erip_expires');
        } else {
            $data['erip_expires'] = '2';
        }

        if (isset($this->request->post['erip_debug'])) {
            $data['erip_debug'] = $this->request->post['erip_debug'];
        } else {
            $data['erip_debug'] = $this->config->get('erip_debug');
        }

        if (isset($this->request->post['erip_sort_order'])) {
            $data['erip_sort_order'] = $this->request->post['erip_sort_order'];
        } else {
            $data['erip_sort_order'] = $this->config->get('erip_sort_order');
        }

        if (isset($this->request->post['erip_status'])) {
            $data['erip_status'] = $this->request->post['erip_status'];
        } else {
            $data['erip_status'] = $this->config->get('erip_status');
        }

        if (isset($this->request->post['erip_order_status_id'])) {
            $data['erip_order_status_id'] = $this->request->post['erip_order_status_id'];
        } elseif ($this->config->has('erip_order_status_id')) {
            $data['erip_order_status_id'] = $this->config->get('erip_order_status_id');
        } else {
            $data['erip_order_status_id'] = '1';
        }

        $this->load->model('localisation/order_status');
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['erip_processing_status_id'])) {
            $data['erip_processing_status_id'] = $this->request->post['erip_processing_status_id'];
        } elseif ($this->config->has('erip_processing_status_id')) {
            $data['erip_processing_status_id'] = $this->config->get('erip_processing_status_id');
        } else {
            $data['erip_processing_status_id'] = '5';
        }

        if (isset($this->request->post['erip_canceled_status_id'])) {
            $data['erip_canceled_status_id'] = $this->request->post['erip_canceled_status_id'];
        } elseif ($this->config->has('erip_canceled_status_id')) {
            $data['erip_canceled_status_id'] = $this->config->get('erip_canceled_status_id');
        } else {
            $data['erip_canceled_status_id'] = '10';
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/erip', $data));
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/payment/erip')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['erip_webkey']) {
            $this->error['webkey'] = $this->language->get('error_webkey');
        }

        if (!$this->request->post['erip_merchant']) {
            $this->error['merchant'] = $this->language->get('error_merchant');
        }

        if (!$this->request->post['erip_expires']) {
            $this->error['expires'] = $this->language->get('error_expires');
        }

        return !$this->error;
    }
}
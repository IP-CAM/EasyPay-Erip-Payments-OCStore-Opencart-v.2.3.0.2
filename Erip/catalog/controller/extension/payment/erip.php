<?php

class ControllerExtensionPaymentErip extends Controller
{
    public function index()
    {
        $this->load->language('extension/payment/erip');

        $data['text_loading'] = $this->language->get('text_loading');

        $data['continue'] = $this->url->link('extension/payment/erip/ep_redirect');
        $data['text_loading'] = $this->language->get('text_loading');
        $data['button_confirm'] = $this->language->get('button_confirm');

        return $this->load->view('extension/payment/erip', $data);
    }

    //Ошибка оплаты
    public function fail()
    {
        $this->cart->clear();//Очищение корзины
        $order_id = $this->request->get['EP_OrderNo'];//Получение номера заказа
        $data['order_id'] = $order_id;


        $this->template = 'extension/payment/erip_failure';

        $this->load->model('checkout/order');
        $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('erip_canceled_status_id')); //Меняем статус заказа

        //Подключение шаблонов
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('extension/payment/erip_failure', $data));
    }

    //Перенаправление клиента после оплаты
    public function success()
    {
        $order_id = $this->request->get['EP_OrderNo'];//Получение номера заказа
        $data['order_id'] = $order_id;
        $this->cart->clear();//Очищение корзины

        $this->template = 'extension/payment/erip_success';

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('extension/payment/erip_success', $data));

    }

    public function ep_redirect()
    {
        if ($this->session->data['payment_method']['code'] == 'erip') {
            $this->load->language('extension/payment/erip');
            $data['redirect'] = $this->language->get('redirect');
            $data['redirect'] = iconv("windows-1251", "UTF-8", $data['redirect']);

            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

            $data['action'] = 'https://sslt.easypay.by/weborder/?EP_Module=opencart_2_3';
            $data['merchant'] = $this->config->get('erip_merchant');
            $data['order_id'] = $this->session->data['order_id'];
            $data['description'] = html_entity_decode($this->config->get('config_store'), ENT_QUOTES, 'UTF-8');
            $data['debug'] = $this->config->get('erip_debug');
            $data['expires'] = $this->config->get('erip_expires');
            $data['hash'] = md5($this->config->get('erip_merchant') . $this->config->get('erip_webkey') . $this->session->data['order_id'] . $order_info['total']);
            $data['amount'] = $order_info['total'];
            $data['return'] = $this->url->link('extension/payment/erip/success');
            $data['fail'] = $this->url->link('extension/payment/erip/fail');
            $data['EP_OrderInfo'] = "Заказ №" . $data['order_id'];
            $data['EP_Comment'] = "Заказ от магазина " . $order_info['store_name'];
            $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('erip_order_status_id'));
            $this->cart->clear();
            $this->response->setOutput($this->load->view('extension/payment/erip_redirect', $data));
        }

    }

    public function notify()
    {

        $values = array(
            'order_mer_code' =>
                isset($this->request->post['order_mer_code']) ? $this->request->post['order_mer_code'] : '',
            'sum' =>
                isset($this->request->post['sum']) ? $this->request->post['sum'] : '',
            'mer_no' =>
                isset($this->request->post['mer_no']) ? $this->request->post['mer_no'] : '',
            'card' =>
                isset($this->request->post['card']) ? $this->request->post['card'] : '',
            'purch_date' =>
                isset($this->request->post['purch_date']) ? $this->request->post['purch_date'] : '',
            'notify_sig' =>
                isset($this->request->post['notify_signature']) ? $this->request->post['notify_signature'] : '',
            'web_key' => $this->config->get('erip_webkey')
        );

        $signature_checked = validateRequest($values);
        if ($signature_checked == 1) {
            $this->load->model('checkout/order');
            $this->model_checkout_order->addOrderHistory($values['order_mer_code'], $this->config->get('erip_processing_status_id')); //Меняем статус заказа
            header("HTTP/1.0 200 OK");
            print $status = 'OK | the notice is processed'; //Все успешно
        } elseif ($signature_checked == 0) {
            header("HTTP/1.0 400 Bad Request");
            print $status = 'FAILED | incorrect digital signature'; //Ошибка вычисления электронной подписи
        } else {
            header("HTTP/1.0 400 Bad Request");
            print $status = 'FAILED | the notice is not processed'; //Ошибка в параметрах
        }
    }

}

//функция проверки электронной подписи
function validateRequest($request)
{
    $signature_checked = -1;
    if (($request['order_mer_code'] != '') &&
        ($request['sum'] != '') &&
        ($request['mer_no'] != '') &&
        ($request['card'] != '') &&
        ($request['purch_date'] != '')
    ) {
        $signature_checked = 0;
        $hash = md5($request['order_mer_code'] .
            $request['sum'] .
            $request['mer_no'] .
            $request['card'] .
            $request['purch_date'] .
            $request['web_key']);
        if ($hash == $request['notify_sig']) {
            $signature_checked = 1;
        }
    }
    return $signature_checked;
}
<?php
class ModelExtensionPaymentEasypay extends Model {
	public function getMethod() {
		$this->load->language('extension/payment/easypay');
		
        $status = true;

        $method_data = array();
		
		if ($status) {
			$method_data = array(
				'code'       => 'easypay',
				'title'      => $this->language->get('text_title'),
				'sort_order' => $this->config->get('easypay_sort_order'),
				'terms'      => ''

			);
		}
		return $method_data;
	}
}
?>
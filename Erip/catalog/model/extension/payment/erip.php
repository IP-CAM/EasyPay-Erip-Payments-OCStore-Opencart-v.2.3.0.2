<?php
class ModelExtensionPaymentErip extends Model {
	public function getMethod() {
		$this->load->language('extension/payment/erip');
		
        $status = true;

        $method_data = array();
		
		if ($status) {
			$method_data = array(
				'code'       => 'erip',
				'title'      => $this->language->get('text_title'),
				'sort_order' => $this->config->get('erip_sort_order'),
				'terms'      => ''

			);
		}
		return $method_data;
	}
}
?>
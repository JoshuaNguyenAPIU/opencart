<?php
namespace Opencart\Catalog\Controller\Extension\Opencart\Payment;
/**
 *
 */
class BankTransfer extends \Opencart\System\Engine\Controller {
	/**
	 * @return string
	 */
	public function index(): string {
		$this->load->language('extension/opencart/payment/bank_transfer');

		$data['bank'] = nl2br($this->config->get('payment_bank_transfer_bank_' . $this->config->get('config_language_id')));

		$data['language'] = $this->config->get('config_language');

		return $this->load->view('extension/opencart/payment/bank_transfer', $data);
	}

	/**
	 * @return void
	 */
	public function confirm(): void {
		$this->load->language('extension/opencart/payment/bank_transfer');

		$json = [];

		if (!isset($this->session->data['order_id'])) {
			$json['error'] = $this->language->get('error_order');
		}

		if (!isset($this->session->data['payment_method']) || $this->session->data['payment_method']['code'] != 'bank_transfer.bank_transfer') {
			$json['error'] = $this->language->get('error_payment_method');
		}

		if (!$json) {
			$comment  = $this->language->get('text_instruction') . "\n\n";
			$comment .= $this->config->get('payment_bank_transfer_bank_' . $this->config->get('config_language_id')) . "\n\n";
			$comment .= $this->language->get('text_payment');

			$this->load->model('checkout/order');

			$this->model_checkout_order->addHistory($this->session->data['order_id'], $this->config->get('payment_bank_transfer_order_status_id'), $comment, true);

			$json['redirect'] = $this->url->link('checkout/success', 'language=' . $this->config->get('config_language'), true);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}

<?php

namespace App\Services\Gateway\paytm;

use App\Models\Voucher;
use Facades\App\Services\BasicService;

class Payment
{
	public static function prepareData($deposit, $gateway)
	{
		$val['MID'] = trim($gateway->parameters->MID);
		$val['WEBSITE'] = trim($gateway->parameters->WEBSITE);
		$val['CHANNEL_ID'] = trim($gateway->parameters->CHANNEL_ID);
		$val['INDUSTRY_TYPE_ID'] = trim($gateway->parameters->INDUSTRY_TYPE_ID);
		$val['ORDER_ID'] = $deposit->utr;
		$val['TXN_AMOUNT'] = round($deposit->payable_amount, 2);
		$val['CUST_ID'] = $deposit->user_id;
		$val['CALLBACK_URL'] = route('ipn', [$gateway->code, $deposit->utr]);
		$val['CHECKSUMHASH'] = (new PayTM())->getChecksumFromArray($val, trim($gateway->parameters->merchant_key));
		$send['val'] = $val;
		$send['view'] = 'user.payment.redirect';
		$send['method'] = 'post';

        if($gateway->environment == 'live'){
            $url = "https://securegw.paytm.in/order/process?orderid={$deposit->utr}";
        }else{
            $url = "https://securegw-stage.paytm.in/order/process?orderid={$deposit->utr}";
        }
        $send['url'] = $url;
		return json_encode($send);
	}

	public static function ipn($request, $gateway, $deposit = null, $trx = null, $type = null)
	{
		$ptm = new PayTM();
		if ($ptm->verifychecksum_e($request, trim($gateway->parameters->merchant_key), $request->CHECKSUMHASH) === "TRUE") {
			if ($request->RESPCODE == "01") {
				$requestParamList = array("MID" => trim($gateway->parameters->MID), "ORDERID" => $request->ORDERID);
				$StatusCheckSum = $ptm->getChecksumFromArray($requestParamList, trim($gateway->parameters->merchant_key));
				$requestParamList['CHECKSUMHASH'] = $StatusCheckSum;
                if($gateway->environment == 'live'){
                    $url = "https://securegw.paytm.in/order/status";
                }else{
                    $url = "https://securegw-stage.paytm.in/order/status";
                }
				$responseParamList = $ptm->callNewAPI($url, $requestParamList);
				if ($responseParamList['STATUS'] == 'TXN_SUCCESS' && $responseParamList['TXNAMOUNT'] == $request->TXNAMOUNT) {
					BasicService::prepareOrderUpgradation($deposit);

					$data['status'] = 'success';
					$data['msg'] = 'Transaction was successful.';
					$data['redirect'] = route('success');
				} else {
					$data['status'] = 'error';
					$data['msg'] = 'it seems some issue in server to server communication. Kindly connect with administrator';
					$data['redirect'] = route('failed');
				}
			} else {
				$data['status'] = 'error';
				$data['msg'] = $request->RESPMSG;
				$data['redirect'] = route('failed');
			}
		} else {
			$data['status'] = 'error';
			$data['msg'] = 'security error!';
			$data['redirect'] = route('failed');
		}
		return $data;
	}
}

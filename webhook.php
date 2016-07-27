<?php
function verify_signature($raw_data, $signature, $pub_key_path) {
	$pub_key_contents = file_get_contents($pub_key_path);
	return openssl_verify($raw_data, base64_decode($signature), $pub_key_contents, 'sha256');
}

$raw_data = file_get_contents('php://input');

require_once('./pingxx/init.php');

$headers = \Pingpp\Util\Util::getRequestHeaders();
// 签名在头部信息的 x-pingplusplus-signature 字段
$signature = isset($headers['X-Pingplusplus-Signature']) ? $headers['X-Pingplusplus-Signature'] : NULL;

// 请从 https://dashboard.pingxx.com 获取「Ping++ 公钥」
$pub_key_path = "Public/pingpp_rsa_public_key.pem";

$result = verify_signature($raw_data, $signature, $pub_key_path);
if ($result === 1) {
    // 验证通过
} elseif ($result === 0) {
    http_response_code(400);
    echo 'verification failed';
    exit;
} else {
    http_response_code(400);
    echo 'verification error';
    exit;
}

$event = json_decode($raw_data, true);
if ($event['type'] == 'charge.succeeded') {
    $charge = $event['data']['object'];

    if ($charge['extra']['context'] == 'registration') {
        # code...
    }
    elseif ($charge['extra']['context'] == 'promotion') {
        $remote_server = 'http://successfuldesign.org/index.php/Home/Pay/promotion_pay_success_api/submission_id/'.$charge['extra']['submission_id'].'/promotion_code/'.$charge['order_no'];
        $context = array(
            'http' => array(
                'method' => 'GET',
                )
        );
        $stream_context = stream_context_create( $context );
        $data = file_get_contents( $remote_server, FALSE, $stream_context );
    }

    http_response_code(200); // PHP 5.4 or greater
} elseif ($event['type'] == 'refund.succeeded') {
    $refund = $event['data']['object'];
    // ...
    http_response_code(200); // PHP 5.4 or greater
} else {
    /**
     * 其它类型 ...
     * - summary.daily.available
     * - summary.weekly.available
     * - summary.monthly.available
     * - transfer.succeeded
     * - red_envelope.sent
     * - red_envelope.received
     * ...
     */
    http_response_code(200);

    // 异常时返回非 2xx 的返回码
    // http_response_code(400);
}

?>
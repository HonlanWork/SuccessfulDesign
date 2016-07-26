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

$mysql_server_name='localhost';
$mysql_username = 'root';
$mysql_password = 'root';
$mysql_database = 'dbname';

$conn = mysql_connect($mysql_server_name, $mysql_username, $mysql_password) or die("error connecting") ;
mysql_select_db($mysql_database, $conn);

$event = json_decode($raw_data, true);
if ($event['type'] == 'charge.succeeded') {
    $charge = $event['data']['object'];

    if ($charge['extra']['context'] == 'registration') {
        # code...
        // $sql = ''
        // $result = mysql_query($sql, $conn);
    }
    elseif ($charge['extra']['context'] == 'promotion') {
        $sql = "update promotion set ispaied=1, promotion_code='' where submission_id=".$charge['extra']['submission_id']." and promotion_code=".$charge['order_no']; 
        $result = mysql_query($sql, $conn);
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

mysql_close($conn);
?>
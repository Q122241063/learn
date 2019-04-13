<?php

/**
 * 发送请求方法
 *
 * @param string $url      请求地址
 * @param array $data      请求数据
 * @param array $headers   请求头
 * @return string|array
 */
function sendRequest($url, $data = [], $headers = [])
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if (!empty($data)) {
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    $response = curl_exec($ch) ? curl_multi_getcontent($ch) : '';
    curl_close($ch);
    return $response;
}

// 如果用户同意登陆， github 就会返回到 callback.php 并携带一个code参数
// 此时只需要使用这个 code 去获取 access_token, 然后在使用 access_token 获取用户信息
$url        = "https://github.com/login/oauth/access_token";
$app_id     = "db28f4552ddb067c01cd";
$app_secret = "aac1b24347fb0b395f2993c6289583aac2194036";

// 组合请求参数
$code   = $_GET['code'];
$params = [
    'client_id'     => $app_id,
    'client_secret' => $app_secret,
    'code'          => $code,
];

// 发送请求并获取响应信息
$response = sendRequest($url, $params, ['Accept: application/json']);
$response = json_decode($response, true);

// 如果有响应信息， 说明请求成功
if (!empty($response['access_token'])) {
    // 请求成功，使用 access_token 获取用户信息
    $access_token = $response['access_token'];
    $url = "https://api.github.com/user";

    // 发送请求，调取github API 获取用户信息
    $userInfo =  sendRequest($url,[],[
        "User-Agent:liaohui8",
        "Authorization:token {$access_token}"
    ]);

    echo "<p>登陆成功</p></pre>";
    var_dump($userInfo);
    exit("</pre>");
}

// 如果登陆失败就打印错误信息
echo "<p>登陆失败</p></pre>";
var_dump($response);
exit("</pre>");

<?php

namespace yzq96;

class Common
{
    function get_url($url, $data = null, $headers = null)
    {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)'); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);    // 自动设置Referer

        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);   // Post提交的数据包x
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);         // 设置超时限制 防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0);           // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);   // 获取的信息以文件流的形式返回
        if (!empty($headers) && is_array($headers)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            echo 'Errno' . curl_error($curl);//捕抓异常
        }
        curl_close($curl); // 关闭CURL会话
        return $tmpInfo; // 返回数据
    }

    /**
     *  模拟提交数据函数
     */
    function post_url($url, $data = null, $header = null)
    {
        try {
            $curl = curl_init(); // 启动一个CURL会话
            curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
            curl_setopt($curl, CURLOPT_USERAGENT,
                'User-Agent :Mozilla/5.0 (Windows NT 6.1; rv:50.0) Gecko/20100101 Firefox/50.0'); // 模拟用户使用的浏览器
            if (!empty($header)) {
                curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
                curl_setopt($curl, CURLOPT_HEADER, 0);//返回response头部信息
            }
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
            curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
            if (!empty($data)) {
                curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
            }
            curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
            curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
            $tmpInfo = curl_exec($curl); // 执行操作
            if (curl_errno($curl)) {
                return curl_errno($curl) . ' Errno ' . curl_error($curl);
            }
            curl_close($curl); // 关键CURL会话
        } catch (\Exception $e) {
            return json('身份验证失败1')->code(401);
        }
        return $tmpInfo; // 返回数据
    }

    /**
     * 生成uuid
     * @return string
     */
    function uuid()
    {
        if (function_exists('com_create_guid')) {
            return com_create_guid();
        } else {
            mt_srand(( double )microtime() * 10000); //optional for php 4.2.0 and up.随便数播种，4.2.0以后不需要了。
            $charid = strtoupper(md5(uniqid(rand(), true))); //根据当前时间（微秒计）生成唯一id.
            $hyphen = chr(45); // "-"
//        $uuid = '' . //chr(123)// "{"
//            substr ( $charid, 0, 8 ) . $hyphen . substr ( $charid, 8, 4 ) . $hyphen . substr ( $charid, 12, 4 ) . $hyphen . substr ( $charid, 16, 4 ) . $hyphen . substr ( $charid, 20, 12 );
//        //.chr(125);// "}"
            $uuid = $charid;
            return $uuid;
        }
    }

    /**
     * 读取http原始输入
     *
     * @param bool $jsonDecode
     * @return bool|mixed|string
     */
    function input_raw($jsonDecode = true)
    {
        $post = file_get_contents('php://input');
        if ($jsonDecode) {
            $post = @json_decode($post, true);
        }

        return $post;
    }

    function mb_url($url = '', $vars = '', $domain = false)
    {
        return mb\helper\Core::url($url, $vars, $domain);
    }

    function attach($path)
    {
        if (stripos(strtolower($path), 'http://') === 0 || stripos(strtolower($path), 'https://') === 0) {
            return $path;
        } else {
            $baseUrl = url();

            return $baseUrl . $path;
        }
    }

    /**
     * 构造错误数组
     *
     * @param int $errno 错误码，0为无任何错误。
     * @param string $errormsg 错误信息，通知上层应用具体错误信息。
     * @return array
     */
    function error($errno, $message = '')
    {
        return array(
            'errno' => $errno,
            'message' => $message,
        );
    }

    /**
     * 检测返回值是否产生错误
     *
     * 产生错误则返回true，否则返回false
     *
     * @param mixed $data 待检测的数据
     * @return boolean
     */
    function is_error($data)
    {
        if (empty($data) || !is_array($data) || !array_key_exists('errno', $data) || (array_key_exists('errno',
                    $data) && $data['errno'] == 0)) {
            return false;
        } else {
            return true;
        }
    }
}
<?php
namespace Hexd;
/**
 * GitHub授权登录
 * author: hexiaodong
 * Date: 2019/04/02
 */
class GitHub{
    private static $clientId;
    private static $clientSecret;
    private static $callback;
    private static $getUserInfoURL = 'https://api.github.com/user';
    private static $getCodeURL = 'https://github.com/login/oauth/authorize';
    private static $getAccessTokenURL = 'https://github.com/login/oauth/access_token';
    
    /**
     * 构造函数
     * @param array $options
     * @return void;
     * */
    public function __construct($options) {
        self::$clientId = $options['client_id'];
        self::$callback = $options['callback'];
        self::$clientSecret = $options['client_secret'];
    }
    //获取code的url
    public function getCodeUrl(){
        return self::$getCodeURL.'?'.self::$clientId;
    }
    
    /**
     * 获取accessToken
     *
     * @param  string  $code
     * @return bool|string
     *
     */
    public function getAccessToken($code){
        $data = array(
            'code'          => $code,
            'client_id'     => self::$clientId,
            'client_secret' => self::$clientSecret
        );
        $result= $this->getHttpResponsePOST(self::$getAccessTokenURL,$data);
        parse_str($result,$accessToken);
        
        return $accessToken;
    }
    
    /**
     * 获取用户信息
     *
     * @param  string $accessToken
     *
     * @return bool|string
     *
     */
    public function getUserInfo($accessToken){
        $data = array(
            'access_token' => $accessToken,
        );
        $headers = array(
            'User-Agent:coolswater',
        );
        $userInfo = $this->getHttpResponseGET(self::$getUserInfoURL,$data,$headers);
        
        return $userInfo;
    }
    
    /**
     * 远程获取数据，POST模式
     *
     * @param   string    $url      指定URL完整路径地址
     * @param   array     $param    请求参数
     * @param   array     $headers  请求头部
     *
     * @return  string    $data   远程输出的数据
     */
    public function getHttpResponsePOST($url, $param = array(), $headers=array()) {
        if (empty($url) || empty($param)) {
            return false;
        }
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$url);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, $headers);//设置header
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        #设置为 1 是检查服务器SSL证书中是否存在一个公用名(common name)。
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, '0');
        #禁止 cURL 验证对等证书（peer's certificate）。要验证的交换证书可以在 CURLOPT_CAINFO 选项中设置
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, '0');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        $data = curl_exec($ch);//运行curl
        
        if (!$data){
            echo 'Curl error: ' . curl_error($ch);
        }
        curl_close($ch);
        
        return $data;
    }
    
    /**
     * 远程获取数据，GET模式
     *
     * @param string    $url        指定URL完整路径地址
     * @param array     $param      请求参数
     * @param array     $headers     请求头部
     *
     * @return array    $output     远程输出的数据
     */
    public function getHttpResponseGET($url, $param = array(), $headers= array()) {
        $url = $url.'?'.http_build_query($param);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if(!empty($headers)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        #设置为 1 是检查服务器SSL证书中是否存在一个公用名(common name)。
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, '0');
        #禁止 cURL 验证对等证书（peer's certificate）。要验证的交换证书可以在 CURLOPT_CAINFO 选项中设置
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, '0');
        $data = curl_exec($ch);
        echo curl_getinfo($ch, CURLINFO_HEADER_OUT);
        if (!$data){
            echo 'Curl error: ' . curl_error($ch);
        }
        curl_close($ch);
        
        return $data;
    }
}
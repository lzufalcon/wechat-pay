<?php
use zhangv\wechat\pay\WechatPay;
use zhangv\wechat\pay\service\Jsapi;
use zhangv\wechat\pay\util\HttpClient;
use zhangv\wechat\pay\util\OAuth;
use PHPUnit\Framework\TestCase;

class JsapiTest extends TestCase {
	/** @var Jsapi */
	private $wechatPay;
	/** @var HttpClient */
	private $httpClient;
	/** @var OAuth */
	private $wechatOauth;

	public function setUp(){
		$config = [
			'mch_id' => 'XXXX', //商户号
			'app_id' => 'XXXXXXXXXXXXXXXXXXX', //APPID
			'app_secret' => 'XXXXXXXXXXXXXXXXXXXXXXXXX', //App Secret
			'api_key' =>'XXXXXXXXXXXXXXXXXXXXXXX', //支付密钥
			'ssl_cert_path' => '/PATHTO/apiclient_cert.pem',
			'ssl_key_path' => '/PATHTO/apiclient_key.pem',
			'sign_type' => 'MD5',
			'notify_url' => 'http://YOURSITE/paidnotify.php',
			'refund_notify_url' => 'http://YOURSITE/refundednotify.php',
			'h5_scene_info' => [//required in H5
				'h5_info' => ['type' => 'Wap', 'wap_url' => 'http://wapurl', 'wap_name' => 'wapname']
			],
			'rsa_pubkey_path' => __DIR__ .'/pubkey.pem',
			'jsapi_ticket' => __DIR__ .'/jsticket.json'
		];
		$this->wechatPay = WechatPay::Jsapi($config);
		$this->httpClient = $this->createMock(HttpClient::class);
		$this->wechatOauth = $this->createMock(OAuth::class);
		$this->wechatPay->setCacheProvider(new \zhangv\wechat\pay\cache\JsonFileCacheProvider());
	}

	/** @test */
	public function getPrepayId(){
		$this->httpClient->method('post')->willReturn(
			"<xml><return_code>SUCCESS</return_code><result_code>SUCCESS</result_code><prepay_id>fakeprepay_id</prepay_id></xml>");
		$this->wechatPay->setHttpClient($this->httpClient);

		$result = $this->wechatPay->getPrepayId("", "", 1, 'openid', 'ext');
		$this->assertEquals($result,'fakeprepay_id');
	}

	/** @test */
	public function getPackage(){
		$r = $this->wechatPay->getPackage('1');
		$this->assertEquals("prepay_id=1",$r['package']);
	}

}

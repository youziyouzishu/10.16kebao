<?php

use Yansongda\Pay\Pay;

return [
    'alipay' => [
        'default' => [
            // 必填-支付宝分配的 app_id
            'app_id' => '2021004175652465',
            // 必填-应用私钥 字符串或路径
            // 在 https://open.alipay.com/develop/manage 《应用详情->开发设置->接口加签方式》中设置
            'app_secret_cert' => 'MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCGWu8fpJ6IhF0YFZxFs0Eg/CP4D1c/8SVLjzzUhEY9eHG+ZeTTKfLMxiTxHUKXFihi42vSJ3VC04KGGKnEEaeXhosastjjjw2j9s2KyyXnwJqnkCKZSjpwunLl8nXgm/oww8wLI39SR77QQEkoZCV0XCVLpwQeEBrv9IkFb01wmcFS0DcCRau0/h2iopLIC6Lu7knjK3g+rqmGcYWbhcZS3ZBBq1DDu3Jl1hSp//80Bes+znt1W0DPIqOdi+86s1VJo1TV9x/ip9yY3hSw4NoHxa+/BHH+GsjGb0F1hcXXJJ4H7Bs54V9qerJ3sXm7IaT9BIRKeFJCtF94fFV2GvI9AgMBAAECggEASoL//vKTIW8XGhZSFq21Pw/W+um3H7UjU+ZafBnnwoubuVZVM28eQsbZEgeCOLyHJWCvGVvcwnT+/FONQznvUi/B0crCUmGx1O85SvIjUYYVvzxGk/EAvCgLNM/k4+5dMNJFxR+oqv8zKdedOxNfWksIPA6iT/Hqno1luGfj8L/nDQ5Zgphrok8e7k6QFAoLnSa0Wj4SsIWs0qfa45BSyZEjZia+c/U5L6pyhXmtuTdN+HZ7zjuGzLWyxdKtm7kiEJymu19QWpEyAmIMQyMA7qFDCQhX0htdObueu51YPx047wElrkXr2/yYw3VlfOe51sOVO4JiiZeKkmAezV4UgQKBgQC/6v99lLgfYlDKUAI1seDbCLTqGg40LihuAMSvAEFH1lFBK9NshfmmQU916fOosYHriugCzpTRaDc4idf7G/8Q8IEMBki8mTktl3uFXcqUM1XLpbIdljB7PykASuu/TF8i5OHeezMGSVePpR+Kd0ontIoHXr4aS77BCEqJIa8J8QKBgQCzN4QLs9UmXxUNW4TOm65lhcTzMpfxCFZbIXTnF5p94a2PpnjJf95p7+ZoxR44QlQqamf0n+fMsoxdLTfTDik3pMZxYZQE64TEiHaWOADRazid3CH95UBkZc/vm+pkVDeDeYS7RuAWfDvL+hGDLLPfZPruhf64+Ni2KTv5goWBDQKBgGglMjgHuFLvmz/uYwSYXpj+BI71TLfsRGxNZm5BCSvelYF0Mus1WOBrmJ84Mc1dZk9XtcewKvnoP+8ifl36N1QN7zmDP314+JpRFeqtlv0NToWQiTOdCPoYsDtEbOIGo6nf4uJEoM/MhQOia5dMXKVnR2/wbeD/Mai2wxvfd4lBAoGAVt1Apj9qv6d0V74VF+NDWzfEJzBNjulAmfkUZXH+UqdQ7YB9qQTOM8Cwh/WK2S/lBY3/hwT+YCvmdr8VALorZin6eTgXe28AMhYGjHbmhpqWnYT2AM7eMAtdBsEmkax0H8iFehQ3Rw6+GPbDCDZhlJSoP3Y46UTMHTbNN/l4Zf0CgYBfRstgYQ6o9W8gmObAuySbuV1M3WMc7ayv8HZWWlP8rUC7qhm5bGhaNKxRuBh0sDBmxnYtX3pOfPGr5p+H2Hp3P1S/XOn8eGdQIqQxawn/Ekjzeap64WP/JEOm04d4Wl5UY2d78k4E62Lqmpa76Xd5QYAAHa/ggBS4/378TibAqw==',
            // 必填-应用公钥证书 路径
            // 设置应用私钥后，即可下载得到以下3个证书
            'app_public_cert_path' => config_path().'/payment/appCertPublicKey_2021004175652465.crt',
            // 必填-支付宝公钥证书 路径
            'alipay_public_cert_path' => config_path().'/payment/alipayCertPublicKey_RSA2.crt',
            // 必填-支付宝根证书 路径
            'alipay_root_cert_path' => config_path().'/payment/alipayRootCert.crt',
            'return_url' => 'https://yansongda.cn/alipay/return',
            'notify_url' => 'https://0907shangcheng.62.hzgqapp.com/api/notify/alipay',
            // 选填-第三方应用授权token
            'app_auth_token' => '',
            // 选填-服务商模式下的服务商 id，当 mode 为 Pay::MODE_SERVICE 时使用该参数
            'service_provider_id' => '',
            // 选填-默认为正常模式。可选为： MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
            'mode' => Pay::MODE_NORMAL,
        ]
    ],
    'wechat' => [
        'default' => [
            // 必填-商户号，服务商模式下为服务商商户号
            // 可在 https://pay.weixin.qq.com/ 账户中心->商户信息 查看
            'mch_id' => '1701195795',
            // 选填-v2商户私钥
            'mch_secret_key_v2' => '',
            // 必填-v3 商户秘钥
            // 即 API v3 密钥(32字节，形如md5值)，可在 账户中心->API安全 中设置
            'mch_secret_key' => 'vNZeQicIUeOfcUgZNJKia2HP8ddFVqYB',
            // 必填-商户私钥 字符串或路径
            // 即 API证书 PRIVATE KEY，可在 账户中心->API安全->申请API证书 里获得
            // 文件名形如：apiclient_key.pem
            'mch_secret_cert' => config_path().'/payment/apiclient_key.pem',
            // 必填-商户公钥证书路径
            // 即 API证书 CERTIFICATE，可在 账户中心->API安全->申请API证书 里获得
            // 文件名形如：apiclient_cert.pem
            'mch_public_cert_path' =>  config_path().'/payment/apiclient_cert.pem',
            // 必填-微信回调url
            // 不能有参数，如?号，空格等，否则会无法正确回调
            'notify_url' => 'https://1016kebao.62.hzgqapp.com/api/notify/wechat',
            // 选填-公众号 的 app_id
            // 可在 mp.weixin.qq.com 设置与开发->基本配置->开发者ID(AppID) 查看
            'mp_app_id' => '',
            // 选填-小程序 的 app_id
            'mini_app_id' => 'wxf39f9d4de3190229',
            // 选填-app 的 app_id
            'app_id' => '',
            // 选填-服务商模式下，子公众号 的 app_id
            'sub_mp_app_id' => '',
            // 选填-服务商模式下，子 app 的 app_id
            'sub_app_id' => '',
            // 选填-服务商模式下，子小程序 的 app_id
            'sub_mini_app_id' => '',
            // 选填-服务商模式下，子商户id
            'sub_mch_id' => '',
            // 选填-微信平台公钥证书路径, optional，强烈建议 php-fpm 模式下配置此参数
            'wechat_public_cert_path' => [
                '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
            ],
            // 选填-默认为正常模式。可选为： MODE_NORMAL, MODE_SERVICE
            'mode' => Pay::MODE_NORMAL,
        ]
    ],
    'unipay' => [
        'default' => [
            // 必填-商户号
            'mch_id' => '777290058167151',
            // 选填-商户密钥：为银联条码支付综合前置平台配置：https://up.95516.com/open/openapi?code=unionpay
            'mch_secret_key' => '979da4cfccbae7923641daa5dd7047c2',
            // 必填-商户公私钥
            'mch_cert_path' => __DIR__.'/Cert/unipayAppCert.pfx',
            // 必填-商户公私钥密码
            'mch_cert_password' => '000000',
            // 必填-银联公钥证书路径
            'unipay_public_cert_path' => __DIR__.'/Cert/unipayCertPublicKey.cer',
            // 必填
            'return_url' => 'https://yansongda.cn/unipay/return',
            // 必填
            'notify_url' => 'https://yansongda.cn/unipay/notify',
            'mode' => Pay::MODE_NORMAL,
        ],
    ],
    'douyin' => [
        'default' => [
            // 选填-商户号
            // 抖音开放平台 --> 应用详情 --> 支付信息 --> 产品管理 --> 商户号
            'mch_id' => '73744242495132490630',
            // 必填-支付 Token，用于支付回调签名
            // 抖音开放平台 --> 应用详情 --> 支付信息 --> 支付设置 --> Token(令牌)
            'mch_secret_token' => 'douyin_mini_token',
            // 必填-支付 SALT，用于支付签名
            // 抖音开放平台 --> 应用详情 --> 支付信息 --> 支付设置 --> SALT
            'mch_secret_salt' => 'oDxWDBr4U7FAAQ8hnGDm29i4A6pbTMDKme4WLLvA',
            // 必填-小程序 app_id
            // 抖音开放平台 --> 应用详情 --> 支付信息 --> 支付设置 --> 小程序appid
            'mini_app_id' => 'tt226e54d3bd581bf801',
            // 选填-抖音开放平台服务商id
            'thirdparty_id' => '',
            // 选填-抖音支付回调地址
            'notify_url' => 'https://yansongda.cn/douyin/notify',
        ],
    ],
    'jsb' => [
        'default' => [
            // 服务代码
            'svr_code' => '',
            // 必填-合作商ID
            'partner_id' => '',
            // 必填-公私钥对编号
            'public_key_code' => '00',
            // 必填-商户私钥(加密签名)
            'mch_secret_cert_path' => '',
            // 必填-商户公钥证书路径(提供江苏银行进行验证签名用)
            'mch_public_cert_path' => '',
            // 必填-江苏银行的公钥(用于解密江苏银行返回的数据)
            'jsb_public_cert_path' => '',
            //支付通知地址
            'notify_url' => '',
            // 选填-默认为正常模式。可选为： MODE_NORMAL:正式环境, MODE_SANDBOX:测试环境
            'mode' => Pay::MODE_NORMAL,
        ],
    ],
    'logger' => [
        'enable' => true,
        'file' => './runtime/logs/pay.log',
        'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
        'type' => 'single', // optional, 可选 daily.
        'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
    ],
    'http' => [ // optional
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
        // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
    ],
];

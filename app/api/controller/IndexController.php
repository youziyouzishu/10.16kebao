<?php

namespace app\api\controller;

use app\admin\model\ShopGoods;
use app\admin\model\ShopGoodsOrders;
use app\api\basic\Base;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use plugin\admin\app\model\Dict;
use plugin\admin\app\model\User;
use support\Cache;
use support\Request;
use Webman\RedisQueue\Client;
use Webman\Util;

class IndexController extends Base
{
    protected $noNeedLogin = ['*'];
    public function index(Request $request)
    {
    }

}

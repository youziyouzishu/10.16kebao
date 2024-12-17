<?php

namespace app\api\controller;

use app\admin\model\Area;
use app\api\basic\Base;
use support\Request;


class AreaController extends Base
{

    protected $noNeedLogin = ['*'];


    function getProvince(Request $request)
    {
        $row = Area::where('level', 1)->get();
        return $this->success('请求成功', $row);
    }
    function getCity(Request $request)
    {
        $province_id = $request->post('province_id');
        $row = Area::where('level', 2)->where('pid', $province_id)->get();
        return $this->success('请求成功', $row);
    }

    function getDistrict(Request $request)
    {
        $city_id = $request->post('city_id');
        $row = Area::where('level', 3)->where('pid', $city_id)->get();
        return $this->success('请求成功', $row);
    }

    //获取当前位置的城市
    function getCityFromLngLat(Request $request)
    {
        $lng = $request->post('lng');
        $lat = $request->post('lat');
        $row = Area::getCityFromLngLat($lng, $lat);
        return $this->success('请求成功', $row);
    }

    function getDistrictFromLngLat(Request $request)
    {
        $lng = $request->post('lng');
        $lat = $request->post('lat');
        $row = Area::getDistrictFromLngLat($lng, $lat);
        return $this->success('请求成功', $row);
    }




}

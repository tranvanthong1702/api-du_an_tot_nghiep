<?php

namespace App\Http\Controllers;

use App\Models\Config_ghn;
use App\Models\District;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TransportController extends Controller
{
    // lấy danh sách các tỉnh
    public function getProvince()
    {
        $provinces = Province::all();
        return response()->json([
            'success' => true,
            'data' => $provinces
        ]);
    }
    // lấy thông tin config_ghn
    public function editTransport()
    {
        $config_ghn=Config_ghn::find(1);
        return response()->json([
            'success' => true,
            'data' => $config_ghn
        ]);
    }
    // reset giá cước
    public function resetTransport()
    {
        DB::table('config_ghns')->truncate();
        DB::table('districts')->truncate();
        DB::table('provinces')->truncate();
        $token = '9855937d-4f26-11ec-bde8-6690e1946f41';
        $shopId = 2280224;
        $weight = 1000;
        $length = 10;
        $width = 10;
        $height = 10;
        // cập nhật các tỉnh vào db
        $province = Http::withHeaders(['token' => $token])
            ->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/province')['data'];
        if ($province) {
            foreach ($province as $p) {
                $province = new Province();
                $province->provinceID = $p['ProvinceID'];
                $province->provinceName = $p['ProvinceName'];
                $province->save();
            }

            // cập nhật các quận trong Hà Nội
            $province=Province::where('ProvinceID',201)->first();
            $districts = Http::withHeaders(['token' => $token])
                ->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/district', ['province_id' => 201])['data'];
            foreach ($districts as $d) {
                $district = new District();
                $district->province_id = $province->id;
                $district->districtID = $d['DistrictID'];
                $district->districtName = $d['DistrictName'];
                $district->save();
            }

            // lấy địa chỉ gửi hàng
            $from_districts_id = $districts[2]['DistrictID'];
            // // lấy địa chỉ giao hàng
            $to_districts_id = $districts[5]['DistrictID'];
            // lấy mã dịch vụ khả dụng
            $serviceId = Http::withHeaders(['token' => $token])
                ->post('https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/available-services', ['shop_id' => $shopId, 'from_district' => $from_districts_id, 'to_district' => $to_districts_id])['data'];
            $serviceId = $serviceId[0]['service_id'];
            // lấy giá cước vận chuyển chuẩn
            $transportation_costs = Http::withHeaders(['Token' => $token, 'ShopId' => $shopId])
                ->post(
                    'https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee',
                    [
                        "from_district_id" => $from_districts_id,
                        "service_id" => $serviceId,
                        "service_type_id" => null,
                        "to_district_id" => $to_districts_id,
                        "height" => $height,
                        "length" => $length,
                        "weight" => $weight,
                        "width" => $width,
                        "insurance_value" => 100000
                    ]
                )['data'];
            // cập nhật thông tin config_ghn vào db
            $model = new Config_ghn();
            $model->provinceID = 201;
            $model->token = $token;
            $model->shopId = $shopId;
            $model->length = $length;
            $model->width = $width;
            $model->height = $height;
            $model->weight = $weight;
            $model->serviceId = $serviceId;
            $model->from_districts_id = $from_districts_id;
            $model->to_districts_id = $to_districts_id;
            $model->expected_price = $transportation_costs['total'];
            $model->save();

            return response()->json([
                'success' => true,
                'data' => $model
            ]);
        }
    }

    // update transport
    public function updateTransport(Request $request)
    {
        // dd(1);
        DB::table('districts')->truncate();
        $config_ghn = Config_ghn::find(1);
        // cập nhật các quận 
        $provinceID = $request->provinceID;
        $province_id=Province::where('provinceID',$provinceID)->first()->id;
        if ($provinceID) {
            $districts = Http::withHeaders(['token' => $config_ghn->token])
                ->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/district', ['province_id' => $provinceID])['data'];
            foreach ($districts as $d) {
                $district = new District();
                $district->province_id = $province_id;
                $district->districtID = $d['DistrictID'];
                $district->districtName = $d['DistrictName'];
                $district->save();
            }

            // cập nhật thông tin cho bảng config_ghns

            // lấy địa chỉ gửi hàng
            $from_districts_id = $districts[2]['DistrictID'];
            // // lấy địa chỉ giao hàng
            $to_districts_id = $districts[5]['DistrictID'];
            // // lấy mã dịch vụ khả dụng
            $serviceId = Http::withHeaders(['token' => $config_ghn->token])
                ->post('https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/available-services', ['shop_id' => $config_ghn->shopId, 'from_district' => $from_districts_id, 'to_district' => $to_districts_id])['data'];
            $serviceId = $serviceId[0]['service_id'];

            // lấy giá cước vận chuyển
            $transportation_costs = Http::withHeaders(['Token' => $config_ghn->token, 'ShopId' => $config_ghn->shopId])
                ->post(
                    'https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee',
                    [
                        "from_district_id" => $from_districts_id,
                        "service_id" => $serviceId,
                        "service_type_id" => null,
                        "to_district_id" => $to_districts_id,
                        "height" => $request->height,
                        "length" => $request->length,
                        "weight" => $config_ghn->weight,
                        "width" => $request->width,
                        "insurance_value" => 100000
                    ]
                )['data'];
            // cập nhật config_ghn

            $config_ghn->provinceID = $request->provinceID;
            $config_ghn->length = $request->length;
            $config_ghn->width = $request->width;
            $config_ghn->height = $request->height;
            $config_ghn->serviceId = $serviceId;
            $config_ghn->from_districts_id = $from_districts_id;
            $config_ghn->to_districts_id = $to_districts_id;
            $config_ghn->expected_price = $transportation_costs['total'];
            $config_ghn->save();

            return response()->json([
                'success' => true,
                'data' => $config_ghn
            ]);
        }
    }
    // lấy giá cước ứng với weight người dùng gửi lên
    public function getPriceTransport($total)
    {
        $config_ghn = Config_ghn::find(1);
        $weight = (int)$total;
        $transportation_costs = Http::withHeaders(['Token' => $config_ghn->token, 'ShopId' => $config_ghn->shopId,])
            ->post(
                'https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee',
                [
                    "from_district_id" =>(int)$config_ghn->from_districts_id,
                    "service_id" => (int)$config_ghn->serviceId,
                    "service_type_id" => (int)$config_ghn->service_type_id,
                    "to_district_id" => (int)$config_ghn->to_districts_id,
                    "height" => (int)$config_ghn->height,
                    "length" => (int)$config_ghn->length,
                    "weight" => (int)$weight,
                    "width" => (int)$config_ghn->width,
                    "insurance_value" => 100000
                ]
            )['data'];
        return response()->json([
            'success' => true,
            'data' => $transportation_costs['total']
        ]);
    }
    public function getProvinceDisstrict()
    {
       $provinceID=Config_ghn::find(1)->provinceID;
       $province=Province::where('provinceID',$provinceID)->first();
       $province->load('districts');
       return response()->json([
        'success' => true,
        'data' => $province

    ]);
    }
}

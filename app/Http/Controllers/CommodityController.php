<?php

namespace App\Http\Controllers;

use App\User;
use App\Active;
use App\Region;
use App\Commodity;
use Illuminate\Http\Request;

class CommodityController extends Controller
{
    public function detail(Request $request)
    {
    	$commodity_id = $request->input('id');

    	$commodity = Commodity::find($commodity_id);
        if (!empty($commodity))
        {
            $commodity->region;
            // 获取 Region 中的用户信息
            $region_user = Region::get_connection($commodity->region->connection)
                                ->where('username', session('user')->name)
                                ->first();
            if ($region_user) {
                $commodity->region->user = $region_user;
            }
            unset($commodity->region->connection);
        }

        return $commodity;
    }

    public function buy(Request $request)
    {
        $commodity_id = $request->input('id');
        $commodity = Commodity::find($commodity_id);

        $user = User::find(session('user')->id);

        // 扣款 未保存
        $before_balance = $user->balance;
        $user->balance = $user->balance - $commodity->price;
        
        // 检查余额
        if ($user->balance < 0)
        {
            $active = new Active;
            $active->username = $user->name;
            $active->commodity_id = $commodity_id;
            $active->content = '购买失败，余额不足';
            $active->price = $commodity->price;
            $active->save();

            return [
                'success' => false,
                'message' => "购买{$commodity->name}失败，余额 ￥{$user->balance} 不足"
            ];
        }

        Region::create_user($commodity->region->connection, $user->name, $commodity, '余额购买');

        // 开通结束 扣款
        $user->save();


        return [
            'success' => true,
            'message' => "购买{$commodity->name}成功"
        ];
    }

    /**
     * 返回套餐列表
     * @return JSON
     */
    public function search(Request $request)
    {
        $id = $request->input('id');
        $query = $request->input('query');
        $commodity = Commodity::orderBy('id', 'desc');
        
        if ($id)
        {
            return $commodity->where('region_id', $id)->get();
        }
        else if ($query)
        {
            $query = '%'.$query.'%';
            $commodity->where('name', 'like', $query)
                ->orWhere('introduction', 'like', $query);
        }

        $commodity = $commodity->paginate(20);

        foreach ($commodity as $key => $value) {
            $value->region;
        }

        return $commodity;
    }

    /**
     * 删除套餐
     * @return JSON
     */
    public function delete(Request $request)
    {
        $id = $request->input('id');

        $result = Commodity::destroy($id);
        if ($result)
        {
            return [
                'success' => true
            ];
        }

        return [
            'success' => false,
            'message' => '删除失败'
        ];
    }

    /**
     * 创建服务器
     * @return JSON
     */
    public function create(Request $request)
    {
        $data = $request->input('commodity');

        if (!isset($data['region_id']) || !Region::find($data['region_id']))
        {
            return [
                'success' => false,
                'message' => '区域不存在'
            ];
        }

        if (empty($data['name']))
        {
            return [
                'success' => false,
                'message' => '缺少必要字段'
            ];
        }

        $commodity = new Commodity;
        $commodity->region_id = $data['region_id'];
        $commodity->name = $data['name'];
        $commodity->price = isset($data['price']) ? $data['price'] : 0;
        $commodity->flow = isset($data['flow']) ? $data['flow'] : 0;
        $commodity->expiration = isset($data['expiration']) ? $data['expiration'] : 0;
        $commodity->introduction = isset($data['introduction']) ? $data['introduction'] : '';

        if ($commodity->save())
        {
            return [
                'success' => true,
                'message' => '创建套餐成功',
                'commodity' => $commodity
            ];
        }

        return [
            'success' => false,
            'message' => '保存失败'
        ];
    }

    /**
     * 服务器详细信息
     * @return JSON
     */
    public function admin_detail(Request $request)
    {
        $id = $request->input('id');

        $commodity = Commodity::find($id);
        if ($commodity)
        {
            return [
                'success' => true,
                'commodity' => $commodity
            ];
        }

        return [
            'success' => false,
            'message' => '找不到套餐'
        ];
    }

    /**
     * 更新服务器信息
     * @return JSON
     */
    public function update(Request $request)
    {
        $id = $request->input('id');
        $data = $request->input('commodity');

        if (!isset($data['region_id']) || !Region::find($data['region_id']))
        {
            return [
                'success' => false,
                'message' => '套餐指向的区域不存在'
            ];
        }

        if (empty($data['name']))
        {
            return [
                'success' => false,
                'message' => '缺少名称字段'
            ];
        }

        $commodity = Commodity::find($id);
        $commodity->region_id = $data['region_id'];
        $commodity->name = $data['name'];
        $commodity->price = $data['price'];
        $commodity->flow = $data['flow'];
        $commodity->expiration = $data['expiration'];
        $commodity->introduction = $data['introduction'];

        if ($commodity->save())
        {
            return [
                'success' => true,
                'commodity' => $commodity
            ];
        }

        return [
            'success' => false,
            'message' => '保存失败'
        ];
    }
}

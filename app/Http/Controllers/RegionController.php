<?php

namespace App\Http\Controllers;

use App\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    /**
     * 返回区域列表
     * @return JSON
     */
    public function all(Request $request)
    {
        if ($request->input('page'))
        {
            return Region::paginate(20);
        }
        return Region::all();
    }

    /**
     * 查看区域详细信息
     * @return JSON
     */
    public function detail(Request $request)
    {
        $id = $request->input('id');
        $user = session('user');
        $region = Region::find($id);
        if (empty($region))
        {
            return [
                'success' => false,
                'message' => '区域不存在'
            ];
        }
        $region->commodity;
        $region->server;
        // 获取 Region 中的用户信息
        $region_user = $region->get_connection($region->connection)
                            ->where('username', $user->name)
                            ->first();
        if ($region_user) {
            $region->user = $region_user;
        } else {
            $server = $region->server;
            foreach ($server as $key => $value) {
                $server[$key]->protocol = '购买后查看';
                $server[$key]->address = '购买后查看';
            }
            $region->server = $server;
        }
        unset($region->connection);
        return $region;
    }

    /**
     * 查看区域详细信息
     * @return JSON
     */
    public function admin_detail(Request $request)
    {
        $id = $request->input('id');
        $user = session('user');
        $region = Region::find($id);
        if (!$region)
        {
            return [
                'success' => false,
                'message' => '区域不存在'
            ];
        }
        return [
            'success' => true,
            'region' => $region
        ];
    }

    /**
     * 保存区域信息
     * @return JSON
     */
    public function update(Request $request)
    {
        $id = $request->input('id');
        $region_data = $request->input('region');

        $region = Region::find($id);
        $region->name = $region_data['name'];
        $region->introduction = $region_data['introduction'];
        $region->connection = $region_data['connection'];
        $region->save();

        return [
            'success' => true,
            'message' => '保存成功'
        ];
    }

    /**
     * 创建区域
     * @return JSON
     */
    public function create(Request $request)
    {
        $region_data = $request->input('region');

        $region = new Region;
        $region->name = $region_data['name'];
        $region->introduction = $region_data['introduction'];
        $region->connection = $region_data['connection'];
        $region->save();

        return [
            'success' => true,
            'message' => '创建成功'
        ];
    }

    /**
     * 删除区域
     * @return JSON
     */
    public function delete(Request $request)
    {
        $id = $request->input('id');

        $result = Region::where('id', $id)->delete();

        return [
            'success' => $result ? true : false,
            'message' => $result ? '删除成功' : '删除失败'
        ];
    }
}

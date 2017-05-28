<?php

namespace App\Http\Controllers;

use App\Region;
use App\Server;
use Illuminate\Http\Request;

class ServerController extends Controller
{
    /**
     * 返回服务器列表
     * @return JSON
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        $servers = Server::orderBy('servers.id', 'desc');
        
        if ($query)
        {
            $query = '%'.$query.'%';
            $servers->where('name', 'like', $query)
                ->orWhere('protocol', 'like', $query)
                ->orWhere('introduction', 'like', $query)
                ->orWhere('address', 'like', $query);
        }

        $servers = $servers->paginate(20);

        foreach ($servers as $key => $value) {
            $value->region;
        }
        return $servers;
    }

    /**
     * 创建服务器
     * @return JSON
     */
    public function create(Request $request)
    {
        $server_data = $request->input('server');

        if (!isset($server_data['region_id']) || !Region::find($server_data['region_id']))
        {
            return [
                'success' => false,
                'message' => '区域不存在'
            ];
        }

        if (empty($server_data['name']) || empty($server_data['protocol']) || empty($server_data['address']))
        {
            return [
                'success' => false,
                'message' => '缺少必要字段'
            ];
        }

        $server = new Server;
        $server->region_id = $server_data['region_id'];
        $server->name = $server_data['name'];
        $server->introduction = $server_data['introduction'];
        $server->protocol = $server_data['protocol'];
        $server->address = $server_data['address'];

        if ($server->save())
        {
            return [
                'success' => true,
                'message' => '创建服务器成功',
                'server' => $server
            ];
        }

        return [
            'success' => false,
            'message' => '保存失败'
        ];
    }

    /**
     * 删除服务器
     * @return JSON
     */
    public function delete(Request $request)
    {
        $id = $request->input('id');

        $result = Server::destroy($id);
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
     * 服务器详细信息
     * @return JSON
     */
    public function detail(Request $request)
    {
        $id = $request->input('id');

        $server = Server::find($id);
        if ($server)
        {
            return [
                'success' => true,
                'server' => $server
            ];
        }

        return [
            'success' => false,
            'message' => '找不到服务器'
        ];
    }

    /**
     * 更新服务器信息
     * @return JSON
     */
    public function update(Request $request)
    {
        $id = $request->input('id');
        $server_data = $request->input('server');

        if (!isset($server_data['region_id']) || !Region::find($server_data['region_id']))
        {
            return [
                'success' => false,
                'message' => '区域不存在'
            ];
        }

        if (empty($server_data['name']) || empty($server_data['protocol']) || empty($server_data['address']))
        {
            return [
                'success' => false,
                'message' => '缺少必要字段'
            ];
        }

        $server = Server::find($id);
        $server->region_id = $server_data['region_id'];
        $server->name = $server_data['name'];
        $server->introduction = $server_data['introduction'];
        $server->protocol = $server_data['protocol'];
        $server->address = $server_data['address'];

        if ($server->save())
        {
            return [
                'success' => true,
                'server' => $server
            ];
        }

        return [
            'success' => false,
            'message' => '保存失败'
        ];
    }
}

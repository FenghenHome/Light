<?php

namespace App\Http\Controllers;

use App\User;
use App\Active;
use App\Region;
use App\Commodity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * 登录
     * @param $username string
     * @param $password string
     * @return JSON
     */
    public function login(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        $user = User::where('name', $username)->first();

        if (!empty($user) && Hash::check($password, $user->password))
        {
            unset($user['password']);

            session(['user' => $user]);
            return [
                'success' => true,
                'user' => $user
            ];
        }

        return [
            'success' => false,
            'message' => '登录失败'
        ];
    }

    /**
     * 注册
     * @param $username string
     * @param $password string
     * @return JSON
     */
    public function register(Request $request)
    {
        $user = new User;
        $user->name = $request->input('username');
        $user->password = $request->input('password');

        if (empty($user->name))
        {
            return [
                'success' => false,
                'message' => '名称不能为空'
            ];
        }
        if (empty($user->password))
        {
            return [
                'success' => false,
                'message' => '密码不能为空'
            ];
        } else {
            $user->password = Hash::make($user->password);
        }
        if (User::where('name', $user->name)->first())
        {
            return [
                'success' => false,
                'message' => '名称已经被使用'
            ];
        }

        $user->save();
        session(['user' => $user]);

        return [
            'success' => true,
            'user' => $user
        ];
    }

    /**
     * 退出
     * @return JSON
     */
    public function logout(Request $request)
    {
        session()->forget('user');
    }

    /**
     * 获得当前登录用户的账户信息
     * @return JSON
     */
    public function profile(Request $request)
    {
        $user = session('user');
        if ($user)
        {
            $user = User::find($user->id);

            unset($user['password']);
            session(['user' => $user]);

            return [
                'success' => true,
                'user' => $user
            ];
        }
        else
        {
            if ($request->input('redirect'))
            {
                // no redirect
                return [
                    'success' => false,
                    'message' => '你还没有登录'
                ];
            }

            return [
                'success' => false,
                'redirect' => 'login',
                'message' => '你还没有登录'
            ];
            
        }
    }

    /**
     * 保存账户信息
     * @return JSON
     */
    public function save_profile(Request $request)
    {
        $user_editor = $request->input('user');

        $user = User::find(session('user')->id);
        $user->email = $user_editor['email'];
        $user->qq = $user_editor['qq'];
        $user->wechat = $user_editor['wechat'];
        if (isset($user_editor['password']) && $user_editor['password'])
        {
            $user->password = Hash::make($user_editor['password']);
        }
        $user->save();

        session(['user' => $user]);

        return [
            'success' => true,
            'user' => $user
        ];
    }

    /**
     * 获得消费记录
     * @return JSON
     */
    public function active(Request $request)
    {
        return Active::select('actives.*', 'commodities.name as commodity_name')
            ->where('username', session('user')->name)
            ->leftJoin('commodities', 'actives.commodity_id', '=', 'commodities.id')
            ->orderBy('actives.created_at', 'desc')
            ->get();
    }

    /**
     * 获得用户列表
     * @param $query JSON
     * @return JSON
     */
    public function lists(Request $request)
    {
        $search_query = $request->input('query');
        $query = User::orderBy('id', 'desc');
        if ($search_query)
        {
            $search_query = '%'.$search_query.'%';
            $query->where('name', 'like', $search_query)
                ->orWhere('email', 'like', $search_query)
                ->orWhere('qq', 'like', $search_query)
                ->orWhere('wechat', 'like', $search_query);
        }
        return $query->paginate(20);
    }

    /**
     * 获得用户的详细信息，在所有区域中的信息
     * @return JSON
     */
    public function detail(Request $request)
    {
        $id = $request->input('id');
        $user = User::find($id);

        if (!$user){
            return [
                'success' => false,
                'message' => '用户不存在'
            ];
        }

        $region = [];

        foreach (Region::all() as $line) {
            $region_user = Region::get_connection($line->connection)
                ->where('username', $user->name)
                ->first();

            if ($region_user && $line->server)
            {
                $servers = $line->server;
                
                unset($line->server);

                $region[] = [
                    'detail' => $line,
                    'user' => $region_user,
                    'servers' => $servers
                ];
            }
        }


        return [
            'success' => true,
            'user' => $user,
            'region' => $region
        ];
    }

    /**
     * 管理员保存用户账户信息
     * @param $id integer
     * @param $user JSON
     * @return JSON
     */
    public function admin_profile_edit(Request $request)
    {
        $id = $request->input('id');
        $user_editor = $request->input('user');

        $user = User::find($id);
        $user->email = $user_editor['email'];
        $user->qq = $user_editor['qq'];
        $user->wechat = $user_editor['wechat'];
        $user->balance = $user_editor['balance'];
        $user->save();

        return [
            'success' => true,
            'user' => $user,
            'message' => '保存成功'
        ];
    }

    /**
     * 启用或禁用某个区域中的指定用户
     * @param $id string
     * @param $username string
     * @param $enable integer (0 or 1)
     * @return JSON
     */
    public function enable(Request $request)
    {
        $region_id = $request->input('id');
        // 用户名仅管理员可用
        $username = $request->input('username');
        $enable = $request->input('enable');

        $region = Region::find($region_id);
        if ($username && session('user')->level > 0)
        {
            // admin
            Region::get_connection($region->connection)->where('username', $username)->update([
                'enable' => $enable
            ]);
        }
        else
        {
            Region::get_connection($region->connection)->where('username', session('user')->name)->update([
                'enable' => $enable
            ]);
        }

        return [
            'success' => true
        ];
    }

    /**
     * 获得指定区域中指定用户的账户信息
     * @param $user_id integer
     * @param $region_id integer
     * @return JSON
     */
    public function region(Request $request)
    {
        $user_id = $request->input('user_id');
        $region_id = $request->input('region_id');

        $user = User::find($user_id);
        $region = Region::find($region_id);

        if (!$user)
        {
            return [
                'success' => false,
                'message' => '用户不存在'
            ];
        }

        if (!$region)
        {
            return [
                'success' => false,
                'message' => '区域不存在'
            ];
        }

        $region_user = Region::get_connection($region->connection)->where('username', $user->name)->first();

        if (!$region_user)
        {
            return [
                'success' => false,
                'message' => '账户信息不存在'
            ];
        }

        return [
            'success' => true,
            'detail' => $region,
            'user' => $user,
            'region_user' => $region_user
        ];
    }

    /**
     * 编辑用户在指定区域中的账户
     * @param $user_id integer
     * @param $region_id integer
     * @param $user array
     * @return JSON
     */
    public function admin_save_region_user(Request $request)
    {
        $user_id = $request->input('user_id');
        $region_id = $request->input('region_id');
        $region_user = $request->input('user');

        $user = User::find($user_id);
        $region = Region::find($region_id);

        if (!$user)
        {
            return [
                'success' => false,
                'message' => '用户不存在'
            ];
        }

        if (!$region)
        {
            return [
                'success' => false,
                'message' => '区域不存在'
            ];
        }

        Region::get_connection($region->connection)->where('username', $user->name)->update($region_user);

        $region_user = Region::get_connection($region->connection)->where('username', $user->name)->first();

        return [
            'success' => true
        ];
    }

    /**
     * 搜索记录
     * @return JSON
     */
    public function record_search(Request $request)
    {
        $query = $request->input('query');
        $code = $request->input('code');

        $record = Active::select('actives.*', 'commodities.name as commodity_name', 'users.id as user_id', 'discounts.remark as discount_remark')
            ->leftJoin('commodities', 'actives.commodity_id', '=', 'commodities.id')
            ->leftJoin('users', 'actives.username', '=', 'users.name')
            ->leftJoin('discounts', 'discounts.code', '=', 'actives.code')
            ->orderBy('actives.created_at', 'desc');
        
        if ($query)
        {
            $query = '%'.$query.'%';
            $record->where('actives.username', 'like', $query)
                ->orWhere('commodities.name', 'like', $query)
                ->orWhere('actives.content', 'like', $query);
        }
        if ($code) {
            $code = '%'.$code.'%';
            $record->orWhere('actives.code', 'like', $code)
                ->orWhere('discounts.remark', 'like', $code);
        }

        return $record->paginate(20);
    }

    /**
     * 在后台创建用户
     * @return JSON
     */
    public function create(Request $request)
    {
        $user_data = $request->input('user');
        $region_id = $request->input('region_id');
        $commodity_id = $request->input('commodity_id');

        $region = Region::find($region_id);
        $commodity = Commodity::find($commodity_id);

        if (empty($user_data) || empty($user_data['name']) || empty($user_data['password']))
        {
            return [
                'success' => false,
                'message' => '请输入用户名与密码'
            ];
        }
        else if (empty($region))
        {
            return [
                'success' => false,
                'message' => '请选择区域'
            ];
        }
        else if (empty($commodity))
        {
            return [
                'success' => false,
                'message' => '请选择套餐'
            ];
        }

        $user = new User;
        $user->name = $user_data['name'];
        $user->password = Hash::make($user_data['password']);
        $user->email = $user_data['email'];
        $user->qq = $user_data['qq'];
        $user->wechat = $user_data['wechat'];

        if (User::where('name', $user->name)->first())
        {
            return [
                'success' => false,
                'message' => '名称已经被使用'
            ];
        }

        $user->save();

        return array_merge(Region::create_user($region->connection, $user->name, $commodity, '后台开通'), ['user' => $user]);
    }

    /**
     * 在后台创建用户
     * @return JSON
     */
    public function region_reset_password(Request $request)
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

        Region::get_connection($region->connection)->where('username', $user->name)->update([
            'passwd' => str_random(6)
        ]);

        return [
            'success' => true,
            'message' => '重置密码成功'
        ];
    }

    /**
     * 后台删除用户在某个区域中的账户
     * @return JSON
     */
    public function admin_delete_region_user(Request $request)
    {
        $region_id = $request->input('region_id');
        $region_user_id = $request->input('region_user_id');
        $region = Region::find($region_id);

        if (empty($region))
        {
            return [
                'success' => false,
                'message' => '区域不存在'
            ];
        }

        Region::get_connection($region->connection)->where('id', $region_user_id)->delete();

        return [
            'success' => true,
            'message' => '删除成功'
        ];
    }
}

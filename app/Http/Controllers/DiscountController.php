<?php

namespace App\Http\Controllers;

use App\Active;
use App\Region;
use App\Commodity;
use App\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    /**
     * 查找激活码
     * @return JSON
     */
    public function find(Request $request)
    {
        $code = $request->input('code');

        $discount = Discount::whereNull('username')->where('code', $code)->first();

        if (!$discount)
        {
            return [
                'success' => false,
                'message' => '激活码不存在'
            ];
        }

        $commodity = $discount->commodity;
        $user = session('user');

        // 为用户开通帐号
        // 类型检查
        $region_user = Region::get_connection($commodity->region->connection)->where('username', $user->name)->first();
        if ($region_user)
        {
            // 续费
            // 检查套餐类型和用户类型是否一致
            if ($commodity->flow > 0)
            {
                // 流量计费套餐
                if ($region_user->expired_time > time())
                {
                    // 账户目前有效
                    return [
                        'success' => false,
                        'message' => '你的账户是时间计费账户，无法开通流量计费套餐。'
                    ];
                }
                // 如果账户内有流量，则加上
                Region::get_connection($commodity->region->connection)->where('id', $region_user->id)->update([
                    'transfer_enable' => max(($region_user->transfer_enable - $region_user->u - $region_user->d), 0) + $commodity->flow
                ]);
            }
            else
            {
                // 时间计费套餐
                // 账户内流量全部作废
                Region::get_connection($commodity->region->connection)->where('id', $region_user->id)->update([
                    'transfer_enable' => 1024 * 1024 * 1024,
                    'expired_time' => max(time(), $region_user->expired_time) + $commodity->expiration
                ]);
            }
        }
        else
        {
            $last_port = Region::get_connection($commodity->region->connection)->orderBy('id', 'desc')->value('port');
            // 开通对应类型的账户
            $id = Region::get_connection($commodity->region->connection)->insertGetId([
                'passwd' => str_random(6),
                'transfer_enable' => $commodity->expiration > 0 ? 10737418240 : $commodity->flow,
                'port' => max(5001, (int)$last_port + 1),
                'expired_time' => time() + $commodity->expiration,
                'username' => $user->name
            ]);
        }

        $region_user = Region::get_connection($commodity->region->connection)->where('username', $user->name)->first();

        $active = new Active;
        $active->username = $user->name;
        $active->commodity_id = $commodity->id;
        $active->content = "激活码购买";
        $active->code = $discount->code;
        $active->price = $commodity->price;
        $active->flow = $region_user->expired_time < time() ? $region_user->transfer_enable - $region_user->u - $region_user->d : 0;
        $active->expired_time = $region_user->expired_time;
        $active->save();

        $discount->username = $user->name;
        $discount->save();

        return [
            'success' => true,
            'message' => "激活套餐{$commodity->name}成功",
            'code' => $code
        ];
    }

    /**
     * 返回激活码列表
     * @return JSON
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        $discount = Discount::withTrashed()
            ->join('commodities', 'commodities.id', '=', 'discounts.commodity_id')
            ->orderBy('discounts.id', 'desc');
        
        if ($query)
        {
            $query = '%'.$query.'%';
            $discount->where('code', 'like', $query)
                ->orWhere('name', 'like', $query)
                ->orWhere('remark', 'like', $query);
        }

        $discount = $discount->paginate(20);

        return $discount;
    }

    /**
     * 创建服务器
     * @return JSON
     */
    public function create(Request $request)
    {
        $commodity_id = $request->input('commodity_id');
        $data = $request->input('discount');

        if (!isset($commodity_id) || !Commodity::find($commodity_id))
        {
            return [
                'success' => false,
                'message' => '套餐不存在'
            ];
        }

        if ((int)$data['number'] < 1)
        {
            return [
                'success' => false,
                'message' => '数量错误'
            ];
        }

        $discounts = [];
        foreach (range(1, $data['number']) as $i) {
            $discount = new Discount;
            $discount->code = str_random(32);
            $discount->commodity_id = $commodity_id;
            $discount->remark = $data['remark'];
            $discount->save();
            $discounts[] = $discount;
        }

        return [
            'success' => true,
            'message' => '生成成功',
            'discounts' => $discounts
        ];
    }

    /**
     * 删除激活码
     * @return JSON
     */
    public function delete(Request $request)
    {
        $id = $request->input('id');

        $result = Discount::destroy($id);
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
}

<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Region extends Model
{
    use SoftDeletes;

    // 根据连接字符串获取操作对象
    // 注意 返回的对象不可复用
    public static function get_connection($detail)
    {
        $name = 'region_' . str_random(4);
        config(["database.connections.{$name}" => json_decode($detail, true)]);

        return DB::connection($name)->table('user');
    }

    public function commodity()
    {
        return $this->hasMany('App\Commodity');
    }

    public function server()
    {
        return $this->hasMany('App\Server');
    }

    public static function create_user($connection, $username, $commodity, $remark)
    {
        // 为用户开通帐号
        // 类型检查
        $region_user = self::get_connection($connection)->where('username', $username)->first();

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
                        'message' => '时间计费账户无法开通流量计费套餐。'
                    ];
                }
                // 如果账户内有流量，则加上
                self::get_connection($connection)->where('id', $region_user->id)->update([
                    'transfer_enable' => max(($region_user->transfer_enable - $region_user->u - $region_user->d), 0) + $commodity->flow
                ]);
            }
            else
            {
                // 时间计费套餐
                // 账户内流量全部作废
                self::get_connection($connection)->where('id', $region_user->id)->update([
                    'transfer_enable' => 1024 * 1024 * 1024,
                    'expired_time' => max(time(), $region_user->expired_time) + $commodity->expiration
                ]);
            }
        }
        else
        {
            $last_port = self::get_connection($connection)->orderBy('id', 'desc')->value('port');
            // 开通对应类型的账户
            $id = self::get_connection($connection)->insertGetId([
                'passwd' => str_random(6),
                'transfer_enable' => $commodity->expiration > 0 ? 10737418240 : $commodity->flow,
                'port' => max(5001, (int)$last_port + 1),
                'expired_time' => time() + $commodity->expiration,
                'enable' => 1,
                'username' => $username
            ]);
        }

        $region_user = self::get_connection($connection)->where('username', $username)->first();

        $active = new Active;
        $active->username = $username;
        $active->commodity_id = $commodity->id;
        $active->content = "余额支付";
        $active->price = $commodity->price;
        $active->flow = $region_user->expired_time < time() ? $region_user->transfer_enable - $region_user->u - $region_user->d : 0;
        $active->expired_time = $region_user->expired_time;
        $active->save();

        return [
            'success' => true,
            'region_user' => $region_user
        ];
    }
}

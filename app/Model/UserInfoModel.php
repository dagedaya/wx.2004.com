<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserInfoModel extends Model
{
    //设置表名
    protected $table="userInfo";
    //设置主键
    protected $primaryKey="id";
    //设置时间戳
    public $timestamps=false;
}

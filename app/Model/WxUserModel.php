<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WxUserModel extends Model
{
    //设置表名
    protected $table="WxUserModel";
    //设置主键
    protected $primaryKey="id";
    //设置时间戳
    public $timestamps=false;
}

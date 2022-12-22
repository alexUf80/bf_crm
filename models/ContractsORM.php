<?php

class ContractsORM extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 's_contracts';
    protected $guarded = [];
    public $timestamps = false;

    public function user()
    {
        return $this->hasOne(UsersORM::class, 'id','user_id');
    }

    public function order()
    {
        return $this->hasOne(OrdersORM::class, 'id','order_id');
    }
}
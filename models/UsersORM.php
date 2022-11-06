<?php

class UsersORM extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 's_users';
    protected $guarded = [];
    public $timestamps = false;

    public function orders()
    {
        return $this->hasMany(OrdersORM::class, 'user_id','id');
    }
}
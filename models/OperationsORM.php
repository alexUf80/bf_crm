<?php

class OperationsORM extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 's_operations';
    protected $guarded = [];
    public $timestamps = false;

    public function contract() {
        return $this->hasOne(ContractsORM::class, 'id', 'contract_id');
    }
}
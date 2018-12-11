<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserLoan extends Model
{
    //
    protected $table = 'user_loans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'amount', 'duration', 'frequency', 'interest_rate', 'arrangement_fee', 'remaining_principal', 'created_by', 'updated_by',
    ];

    public function frequency() {
        switch($this->frequency) {
            case 'monthly':
                return 1;
            case 'quarterly':
                return 3;
            case 'semester':
                return 6;
            case 'yearly':
                return 12;             
        }
    }
}

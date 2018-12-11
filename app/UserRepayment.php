<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRepayment extends Model
{
    //
    protected $table = 'user_repayments';

    protected $fillable = [
        'loan_id', 'user_id', 'installment_no', 'amount', 'due_date', 'payment_amount', 'payment_date', 'penalty', 'created_by', 'updated_by',
    ];

    public function loan() {
        return $this->belongsTo('App\UserLoan');
    }
}

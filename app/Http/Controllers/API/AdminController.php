<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\UserLoan;
use App\UserRepayment;
use Illuminate\Support\Facades\DB;


class AdminController extends Controller
{
    //
    function approveLoan(Request $request) {
        if(!$request->user()->hasPermissionTo('approve-loan')) {
            return response(json_encode(['isSuccess'=>0, 'message'=>'Permission Denied']), 200)->header('Content-Type', 'application/json');
        }
        $rule = [
            'id' => 'required|exists:mysql.user_loans',
        ];
        $this->validate($request, $rule);
        DB::transaction(function () {
            global $request;
            $userLoan = UserLoan::find($request->id);
            /* If $request->user()->can('APPROVE_LOAN') */
            if($userLoan->status == 1) {
                $userLoan->status = 2;
                $userLoan->save();
                /* Generate User's repayments schedule */
                $this->generateRepayments($userLoan, $request);
            } else {
                return response(json_encode(['isSuccess'=>0, 'message'=>'Approve fail, check loan status!']), 200)->header('Content-Type', 'application/json');
            }
        }, 2);
        /* Send Loan approved email to user */

        return response(json_encode(['isSuccess'=>1, 'message'=>'Loan approved']), 200)->header('Content-Type', 'application/json');
    }

    function rejectLoan(Request $request) {
        if(!$request->user()->hasPermissionTo('reject-loan')) {
            return response(json_encode(['isSuccess'=>0, 'message'=>'Permission Denied']), 200)->header('Content-Type', 'application/json');
        }
        $rule = [
            'id' => 'required|exists:mysql.user_loans',
        ];
        $this->validate($request, $rule);
        
        $userLoan = UserLoan::find($request->id);
        /* If $request->user()->can('APPROVE_LOAN') */
        if($userLoan->status == 1) {
            $userLoan->status = 3;
        }
            
        $userLoan->save();
        /* Send Loan rejected email to user */
        return response(json_encode(['isSuccess'=>1, 'message'=>'Loan rejected']), 200)->header('Content-Type', 'application/json');
    }

    private function generateRepayments($userLoan, $request) {
        $total_interest = $userLoan->interest_rate/12 * $userLoan->duration;
        $principal = $userLoan->amount + $userLoan->amount*$total_interest/100;
        $installment_amount = $principal/$userLoan->duration;
        $monthsFrequency = $userLoan->frequency();
        $data = [];
        $data[0] = [
            'loan_id' => $userLoan->id,
            'user_id' => $userLoan->user_id,
            'installment_no' => 0,
            'amount' => $userLoan->arrangement_fee,
            'due_date' => date('Y-m-d', strtotime(date('Y-m-d') . '+ 1 month')),
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ];
        for($i=1; $i<=$userLoan->duration; $i++) {
            if($i==1) $due_date = date('Y-m-d', strtotime(date('Y-m-d') . '+ 1 month'));
            else $due_date = date('Y-m-d', strtotime($data[$i-1]['due_date'] . '+ 1 month'));
            $data[$i] = [
                'loan_id' => $userLoan->id,
                'user_id' => $userLoan->user_id,
                'installment_no' => $i,
                'amount' => $installment_amount,
                'due_date' => $due_date,
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
            ];  
        }
        UserRepayment::insert($data); 
    }
    
    function verifyPayment(Request $request) {
        if(!$request->user()->hasPermissionTo('verify-payment')) {
            return response(json_encode(['isSuccess'=>0, 'message'=>'Permission Denied']), 200)->header('Content-Type', 'application/json');
        }
        $rule = [
            'id' => 'required|exists:mysql.user_repayments',
        ];
        $this->validate($request, $rule);
        DB::transaction(function ($userPayment) {
            global $request;
            $userPayment = UserRepayment::with('loan')->get()->find($request->id);
            
            if($userPayment->loan->status == 4) {
                return response(json_encode(['isSuccess'=>0, 'message'=>'Loan has status Repaid']), 200)->header('Content-Type', 'application/json');
            }

            /* If $request->user()->can('APPROVE_LOAN') */
            if($userPayment->status == 'unverify') {
                    $userPayment->status = 'verified';
                    $userPayment->save();
                    $userLoan = $userPayment->loan;
                    $userLoan->remaining_principal = $userLoan->remaining_principal - $userPayment->payment_amount;
                    if($userLoan->remaining_principal <= 0) $userLoan->status = '4';
                    $userLoan->save();

            } else {
                return response(json_encode(['isSuccess'=>0, 'message'=>'This payment has already been verified']), 200)->header('Content-Type', 'application/json');
            }                
        }, 2);        

        return response(json_encode(['isSuccess'=>1, 'message'=>'Payment verified']), 200)->header('Content-Type', 'application/json');
    }
}

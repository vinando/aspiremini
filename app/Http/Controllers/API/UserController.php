<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\UserLoan;
use App\UserRepayment;

class UserController extends Controller
{
    //
    function createLoan(Request $request) {
        $rule = [
            'amount' => 'required',
            'duration' => 'required',
            'frequency' => 'required',
            'interest_rate' => 'required',
            'arrangement_fee' => 'required',
        ];
        $this->validate($request, $rule);

        $total_interest = $userLoan->interest_rate/12 * $userLoan->duration;
        $principal = $userLoan->amount + $userLoan->amount*$total_interest/100;

        $userLoan = new UserLoan;
        $userLoan->user_id = $request->user()->id;
        $userLoan->amount = $request->amount;
        $userLoan->duration = $request->duration;
        $userLoan->frequency = $request->frequency;
        $userLoan->interest_rate = $request->interest_rate;
        $userLoan->arrangement_fee = $request->arrangement_fee;
        $userLoan->description = $request->description;
        $userLoan->status = 1;
        $userLoan->remaining_principal = $principal;
        $userLoan->created_by = $request->user()->id;
        $userLoan->updated_by = $request->user()->id;
        $userLoan->save();

        return response(json_encode(['isSuccess'=>1, 'message'=>'Loan created']), 200)->header('Content-Type', 'application/json');
    }

    function listPayment(Request $request) {
        $listPayments = UserRepayment::with('loan')->get();
        return response(json_encode($listPayments), 200)->header('Content-Type', 'application/json');
    }

    function confirmPayment(Request $request) {
        $rule = [
            'id' => 'required',
            'payment_amount' => 'required',
            'payment_date' => 'required',
            'payment_method' => 'required',
        ];
        $this->validate($request, $rule);

        $userPayment = UserRepayment::findOrFail($request->id);
        $userPayment->payment_amount = $request->payment_amount;
        $userPayment->payment_date = $request->payment_date;
        $userPayment->payment_method = $request->payment_method;
        //$userPayment->proof_of_payment = ''; // Image location
        $userPayment->status = 'unverify';
        $userPayment->created_by = $request->user()->id;
        $userPayment->updated_by = $request->user()->id;
        $userPayment->save();

        return response(json_encode(['isSuccess'=>1, 'message'=>'Payment Confirmation submitted']), 200)->header('Content-Type', 'application/json');
    }
}

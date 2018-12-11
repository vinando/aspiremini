<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserRepaymentsTableAddColumnsPaymentMethodProofOfPayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('user_repayments', function (Blueprint $table) {
            $table->enum('payment_method', ['Bank transfer', 'Autodebet', 'Credit Card'])->after('penalty')->nullable();
            $table->enum('status', ['unverify', 'verified'])->after('payment_method')->nullable();
            $table->string('proof_of_payment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

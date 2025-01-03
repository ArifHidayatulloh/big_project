<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique()->nullable();
            $table->string('supplier_name');
            $table->decimal('payment_amount', 65,2);
            $table->timestamp('purchase_date');
            $table->timestamp('due_date');
            $table->enum('status', ['Unpaid', 'Paid'])->default('Unpaid');
            $table->timestamp('paid_date')->nullable();
            $table->string('attachment')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_schedules');
    }
};

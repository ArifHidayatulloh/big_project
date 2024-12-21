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
        Schema::create('actuals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monthly_budget_id')->constrained('monthly_budgets')->onDelete('cascade');
            $table->date('date');
            $table->string('source');
            $table->string('no_source');
            $table->text('description');
            $table->decimal('actual_spent', 15, 2)->nullable();
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('actuals');
    }
};

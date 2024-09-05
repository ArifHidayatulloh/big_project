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
        Schema::create('monthly_budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('description_id')->constrained('budget_descriptions')->onDelete('cascade'); // Menghubungkan dengan budget_descriptions
            $table->decimal('budget_amount', 15, 2); // Jumlah budget untuk bulan tersebut
            $table->year('year'); // Tahun budget
            $table->unsignedTinyInteger('month');
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
        Schema::dropIfExists('monthly_budgets');
    }
};

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
        Schema::create('sipil_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sipil_id')->constrained()->onDelete('cascade');
            $table->date('report_date');
            $table->text('progress');
            $table->text('obstacle')->nullable();
            $table->date('solution_date')->nullable();
            $table->text('solution')->nullable();
            $table->date('due_date')->nullable();
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
        Schema::dropIfExists('sipil_progress');
    }
};

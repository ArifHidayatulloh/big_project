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
        Schema::create('sipil_progress_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sipil_progress_id')->constraine()->onDelete('cascade');
            $table->boolean('dephead')->default(false);
            $table->boolean('pengawas')->default(false);
            $table->boolean('user')->default(false);
            $table->boolean('kki')->default(false);
            $table->boolean('itp')->default(false);
            $table->boolean('payment')->default(false);
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
        Schema::dropIfExists('sipil_progress_approvals');
    }
};

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
        Schema::create('sipils', function (Blueprint $table) {
            $table->id();
            $table->string('sr_no',50);
            $table->string('jo_no',50);
            $table->string('project_name');
            $table->string('pic');
            $table->date('start_date');
            $table->integer('progress_percentage')->default(0);
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
        Schema::dropIfExists('sipils');
    }
};

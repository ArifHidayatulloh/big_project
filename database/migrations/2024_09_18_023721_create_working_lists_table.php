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
        Schema::create('working_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained('units');
            $table->string('name');
            $table->foreignId('pic')->constrained('users');
            $table->json('relatedpic')->nullable();
            $table->timestamp('deadline');
            $table->enum('status', ['On Progress', 'Done', 'Outstanding', 'Requested', 'Rejected']);
            $table->timestamp('complete_date')->nullable();
            $table->text('status_comment')->nullable();
            $table->enum('request_status',['Requested','Rejected','Approved'])->nullable();
            $table->text('reject_reason')->nullable();
            $table->integer('is_priority'); // Menandakan apakah ini masuk ke skala prioritas
            $table->integer('score')->nullable(); // Bobot penilaian
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->foreignId('rejected_by')->nullable()->constrained('users');
            $table->timestamp('request_at')->nullable();
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
        Schema::dropIfExists('working_lists');
    }
};

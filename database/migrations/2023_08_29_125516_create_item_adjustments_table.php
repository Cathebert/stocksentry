<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('item_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id');
             $table->foreignId('user_id');
             $table->foreignId('laboratory_id');
            $table->foreignId('laboratory_section_id')->nullable();
            $table->date('adjusted_date');
            $table->string('Ref');
            $table->text('notes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_adjustments');
    }
};

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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_name');
            $table->foreignId('laboratory_id');
              $table->foreignId('laboratory_section_id')->nullable();
            $table->string('contact_person');
            $table->string('address');
            $table->string('email');
            $table->string('phone_number')->nullable();
            $table->date('contract_expiry');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};

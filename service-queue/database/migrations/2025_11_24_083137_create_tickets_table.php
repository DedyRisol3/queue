<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('queue_number'); // Contoh: A001, B001
            $table->char('session', 1);     // A atau B
            $table->enum('status', ['waiting', 'called', 'finished', 'skipped'])->default('waiting');
            $table->string('user_phone')->nullable();
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() : void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->string('prefix')->nullable();
            $table->string('first_name');
            $table->string('middle_name')
                  ->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->date('date_of_birth')->default('1900-01-01');
            $table->string('email');
            $table->string('password');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down() : void
    {
        Schema::dropIfExists('patients');
    }
};

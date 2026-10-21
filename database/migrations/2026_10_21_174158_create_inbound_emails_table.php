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
        Schema::create('inbound_emails', function (Blueprint $table) {
            $table->id();
            $table->string('sender');
            $table->string('subject');
            $table->text('body');
            $table->string('status')->default('pending')->index(); // pending | classified | failed
            $table->string('category')->nullable()->index();
            $table->string('priority')->nullable()->index();
            $table->string('sentiment')->nullable();
            $table->string('language', 10)->nullable();
            $table->text('summary')->nullable();
            $table->text('suggested_reply')->nullable();
            $table->boolean('needs_human')->default(false);
            $table->float('confidence')->nullable();
            $table->string('model')->nullable();
            $table->unsignedInteger('input_tokens')->nullable();
            $table->unsignedInteger('output_tokens')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inbound_emails');
    }
};

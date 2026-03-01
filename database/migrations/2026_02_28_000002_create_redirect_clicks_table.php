<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('redirect_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('redirect_link_id')->constrained()->cascadeOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('referer')->nullable();
            $table->string('browser')->nullable();
            $table->string('browser_version')->nullable();
            $table->string('platform')->nullable();
            $table->string('device_type')->nullable();
            $table->timestamp('clicked_at');

            $table->index('redirect_link_id');
            $table->index('clicked_at');
            $table->index(['redirect_link_id', 'clicked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redirect_clicks');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cctvs', function (Blueprint $table) {
            $table->string('hls_path')->nullable()->after('ip_rtsp');
            $table->integer('ffmpeg_pid')->nullable()->after('hls_path');
        });
    }

    public function down(): void
    {
        Schema::table('cctvs', function (Blueprint $table) {
            $table->dropColumn(['hls_path','ffmpeg_pid']);
        });
    }
};


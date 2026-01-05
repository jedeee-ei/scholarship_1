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
        Schema::table('announcements', function (Blueprint $table) {
            $table->string('type')->default('general')->after('content');
            $table->string('priority')->default('medium')->after('type');
            $table->boolean('is_published')->default(false)->after('priority');
            $table->timestamp('publish_date')->nullable()->after('is_published');
            $table->timestamp('expiry_date')->nullable()->after('publish_date');
            $table->string('updated_by')->nullable()->after('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['type', 'priority', 'is_published', 'publish_date', 'expiry_date', 'updated_by']);
        });
    }
};

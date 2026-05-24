<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->timestamp('reminder_7days_sent_at')->nullable()->after('expires_at');
            $table->timestamp('ended_notified_at')->nullable()->after('reminder_7days_sent_at');
        });
    }

    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['reminder_7days_sent_at', 'ended_notified_at']);
        });
    }
};

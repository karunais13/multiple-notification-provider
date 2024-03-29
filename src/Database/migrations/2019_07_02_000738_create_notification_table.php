<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( !Schema::hasTable(config('notification.tables.notification_store')) ){
            Schema::create(config('notification.tables.notification_store'), function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('notiuser_id');
                $table->string('notiuser_type');
                $table->tinyInteger('type')->comment('1-email, 2-web_push, 3-native_push, 4-sms');
                $table->string('subject');
                $table->text('content')->nullable();
                $table->text('target')->nullable();
                $table->tinyInteger('is_read')->default(0);
                $table->tinyInteger('status')->default(1)->comment('1-active, 2-failed');
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->timestamp('updated_at')->nullable()->default(DB::raw('NULL on update CURRENT_TIMESTAMP'));
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('notification.tables.notification_store'));
    }
}

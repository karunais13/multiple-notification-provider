<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationToken extends Migration
{
    public $_table = 'notification_token';
    public $_foreign = [];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( !Schema::hasTable($this->_table) ){
            Schema::create($this->_table, function( Blueprint $table ){
                $table->increments('id');
                $table->integer('notitokenable_id');
                $table->string('notitokenable_type');
                $table->unsignedInteger('type')->default(1)->comment('1 => Web Push Notification');
                $table->string('token');
                $table->unsignedInteger('is_login')->default(0);
                $table->tinyInteger('status')->default(1);
                $table->timestamp('last_updated_at')->nullable();
                $table->softDeletes();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

                if( count($this->_foreign) > 0 ){
                    foreach( $this->_foreign as $key => $value ){
                        $table->foreign($key)->references($value['ref'])->on($value['table']);
                    }
                }
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
        if( Schema::hasTable($this->_table) ){
            if( count($this->_foreign) > 0 ){
                Schema::table($this->_table, function( Blueprint $table ){
                    foreach( $this->_foreign as $key => $value ){
                        $table->dropForeign($this->_table . '_' . $key . '_foreign');
                    }
                });
            }
            Schema::dropIfExists($this->_table);

        }
    }
}

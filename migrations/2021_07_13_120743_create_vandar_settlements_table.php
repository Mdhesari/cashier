<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVandarSettlementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vandar_settlements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('settlement_id')->nullable();
            $table->decimal('amount', 20, 0)->comment('Cuurency : RIAL');
            $table->decimal('amount_toman', 20, 0)->comment('Cuurency : TOMAN')->nullable();
            $table->decimal('wage_toman', 20, 0)->comment('Cuurency : TOMAN')->nullable();
            $table->string('iban');
            $table->string('iban_id')->nullable();
            $table->uuid('track_id');
            $table->string('payment_number')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('gateway_transaction_id')->nullable();
            $table->enum('status', ['INIT', 'PENDING', 'DONE', 'FAILED', 'CANCELED']);
            $table->decimal('wallet', 20, 0)->nullable()->comment('Cuurency : RIAL');
            $table->boolean('is_instant')->default(false);
            $table->date('settlement_date')->nullable();
            $table->time('settlement_time')->nullable();
            $table->string('settlement_date_jalali')->nullable();
            $table->json('prediction')->nullable();
            $table->string('notify_url')->nullable();
            $table->string('errors')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vandar_settlements');
    }
}

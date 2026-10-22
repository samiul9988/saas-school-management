<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->foreignId('account_id')->nullable()->after('category')->constrained('chart_of_accounts')->nullOnDelete();
            $table->foreignId('payment_account_id')->nullable()->after('account_id')->constrained('chart_of_accounts')->nullOnDelete();
            $table->string('method', 20)->nullable()->after('payment_account_id');
            $table->string('reference', 100)->nullable()->after('method');
            $table->foreignId('journal_entry_id')->nullable()->after('reference')->constrained('journal_entries')->nullOnDelete();
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('account_id')->nullable()->after('category')->constrained('chart_of_accounts')->nullOnDelete();
            $table->foreignId('payment_account_id')->nullable()->after('account_id')->constrained('chart_of_accounts')->nullOnDelete();
            $table->string('method', 20)->nullable()->after('payment_account_id');
            $table->string('reference', 100)->nullable()->after('method');
            $table->foreignId('journal_entry_id')->nullable()->after('reference')->constrained('journal_entries')->nullOnDelete();
        });

        Schema::table('fee_payments', function (Blueprint $table) {
            $table->foreignId('payment_account_id')->nullable()->after('method')->constrained('chart_of_accounts')->nullOnDelete();
            $table->foreignId('journal_entry_id')->nullable()->after('payment_account_id')->constrained('journal_entries')->nullOnDelete();
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->string('method', 20)->nullable()->after('status');
            $table->foreignId('payment_account_id')->nullable()->after('method')->constrained('chart_of_accounts')->nullOnDelete();
            $table->foreignId('journal_entry_id')->nullable()->after('payment_account_id')->constrained('journal_entries')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('account_id');
            $table->dropConstrainedForeignId('payment_account_id');
            $table->dropConstrainedForeignId('journal_entry_id');
            $table->dropColumn(['method', 'reference']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('account_id');
            $table->dropConstrainedForeignId('payment_account_id');
            $table->dropConstrainedForeignId('journal_entry_id');
            $table->dropColumn(['method', 'reference']);
        });

        Schema::table('fee_payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_account_id');
            $table->dropConstrainedForeignId('journal_entry_id');
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_account_id');
            $table->dropConstrainedForeignId('journal_entry_id');
            $table->dropColumn('method');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Users
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->after('id')->nullable();
        });
        DB::table('users')->orderBy('id')->chunk(200, function ($rows) {
            foreach ($rows as $row) {
                DB::table('users')->where('id', $row->id)->update(['uuid' => (string) Str::uuid()]);
            }
        });
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->unique()->change();
        });

        // Entities
        Schema::table('entities', function (Blueprint $table) {
            $table->uuid('uuid')->after('id')->nullable();
        });
        DB::table('entities')->orderBy('id')->chunk(200, function ($rows) {
            foreach ($rows as $row) {
                DB::table('entities')->where('id', $row->id)->update(['uuid' => (string) Str::uuid()]);
            }
        });
        Schema::table('entities', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->unique()->change();
        });

        // Accounts
        Schema::table('accounts', function (Blueprint $table) {
            $table->uuid('uuid')->after('id')->nullable();
        });
        DB::table('accounts')->orderBy('id')->chunk(200, function ($rows) {
            foreach ($rows as $row) {
                DB::table('accounts')->where('id', $row->id)->update(['uuid' => (string) Str::uuid()]);
            }
        });
        Schema::table('accounts', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->unique()->change();
        });

        // Transactions
        Schema::table('transactions', function (Blueprint $table) {
            $table->uuid('uuid')->after('id')->nullable();
        });
        DB::table('transactions')->orderBy('id')->chunk(500, function ($rows) {
            foreach ($rows as $row) {
                DB::table('transactions')->where('id', $row->id)->update(['uuid' => (string) Str::uuid()]);
            }
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->unique()->change();
        });

        // Networks (slug)
        Schema::table('networks', function (Blueprint $table) {
            $table->string('slug')->after('name')->nullable();
        });
        DB::table('networks')->orderBy('id')->each(function ($row) {
            $slug = Str::slug($row->name);
            // Ensure uniqueness if duplicates exist?
            // Simple approach for now.
            DB::table('networks')->where('id', $row->id)->update(['slug' => $slug]);
        });
        Schema::table('networks', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
        Schema::table('entities', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
        Schema::table('networks', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};

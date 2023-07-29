<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RefactorEmailConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('email_configs');

        Schema::create('email_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email_type', 191)->comment('Which kind of email will be send or not');
            $table->tinyInteger('is_enabled')->size(1)->default(1)->comment('1 true 0 false');
            $table->string('subject', 191);
            $table->text('body');
            $table->decimal('resend_interval',10,2)->nullable();
            $table->timestamps();
        });

        \DB::table('email_configs')->insert([
            'email_type' => 'email_verification',
            'subject' => 'Welcome to Earnie! Please verify your email address.',
            'body' => '<p>Nearly there…</p><p>Confirm your email address to finish setting up your account.</p><p>Just click the button below.</p>',            
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        \DB::table('email_configs')->insert([
            'email_type' => 'invitation',
            'subject' => "You’ve been invited to Earnie!",
            'body' => "<p>You’ve been invited to Earnie!</p><p>Your way to get rewarded for the things you do on a daily basis.</p><p>How did I get access to this?</p><p>You received this email because your company have given you access to the Earnie platform. The next step is to activate your account!</p><p>What is included?</p><p>You'll get access to view your score and enter competitions to win prizes and cash.</p><p>How do I log in?</p><p>Click the button below to set up your account.</p>",
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        \DB::table('email_configs')->insert([
            'email_type' => 'invitation_reminder',
            'subject' => 'Invitation Reminder',
            'body' => '<p>You’re missing out on the chance to get rewarded for things you do every day!</p><p>Click the button below to set up your account.</p>',
            'resend_interval' => 7.00,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        \DB::table('email_configs')->insert([
            'email_type' => 'account_changes',
            'subject' => 'Someone made changes to your account.',
            'body' => '<p>Changes have been made to your account.</p>',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        \DB::table('email_configs')->insert([
            'email_type' => 'password_reset',
            'subject' => 'Password Reset',
            'body' => '<p>You have requested to reset your password.</p>',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        \DB::table('email_configs')->insert([
            'email_type' => 'competition_win',
            'subject' => 'You’re a winner!',
            'body' => '<p>Congratulations, you won!</p>',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        \DB::table('email_configs')->insert([
            'email_type' => 'competition_loss',
            'subject' => 'The results are in…',
            'body' => '<p>Better luck next time.</p>',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        \DB::table('email_configs')->insert([
            'email_type' => 'league_invite',
            'subject' => 'League Invitation',
            'body' => '<p>You’ve been invited to a league. Join now to compete!</p>',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_configs');

        Schema::create('email_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('email_verification')->default(0)->comment('1 true 0 false');
            $table->boolean('invitation')->default(0)->comment('1 true 0 false');
            $table->boolean('invitation_reminder')->default(0)->comment('1 true 0 false');
            $table->boolean('account_changes')->default(0)->comment('1 true 0 false');
            $table->boolean('password_reset')->default(0)->comment('1 true 0 false');
            $table->boolean('competition_result')->default(0)->comment('1 true 0 false');
            $table->boolean('league_invites')->default(0)->comment('1 true 0 false');
            $table->decimal('invitation_reminder_time_delay',10,2)->nullable();
            $table->timestamps();
        });

        \DB::table('email_configs')->insert([
            'email_verification' => 1, 
            'invitation' => 1, 
            'invitation_reminder' => 1, 
            'account_changes' => 1, 
            'password_reset' => 1, 
            'competition_result' => 1, 
            'league_invites' => 1, 
            'invitation_reminder_time_delay' => 7,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}

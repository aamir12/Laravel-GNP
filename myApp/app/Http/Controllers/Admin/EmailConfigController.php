<?php

namespace App\Http\Controllers\Admin;

use App\Classes\EmailConfigManager;
use App\Models\Role;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmailConfig\UpdateEmailConfigRequest;
use App\Models\EmailConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

/**
 * @group Admin API - Emails
 *
 * Admin APIs for managing emails.
 *
 * When certain events occur, the system will send out automated emails to its
 * users. For example, when a competition ends, all the entrants in the
 * competition will be notified. Other types of events that trigger emails
 * include user registration, password reset requests, league invites etc.
 *
 * Each email can be switched on or off independently and the content of each
 * email can also be altered.
 */
class EmailConfigController extends Controller
{
    /**
     * Update Email Config
     *
     * Updates the specified email config with the values provided.
     *
     * @bodyParam id int required The ID of the email config being updated. Example: 1
     * @bodyParam email_type string required Max: 191. The type of email being updated. Can be used as an alternative means to identify an email if the `id` is unknown. Example: invitation_reminder
     * @bodyParam is_enabled bool Whether or not the email is enabled. Example: 0
     * @bodyParam subject string Max: 191. The email's subject. Example: Test Subject
     * @bodyParam body string The email body. Example: <p>Test Body</p>
     * @bodyParam resend_interval int The interval after which the email will be resent. Example: 7.00
     *
     * @responseFile 200 resources/responses/Admin/EmailConfig/update.json
     * @responseFile 422 resources/responses/Admin/EmailConfig/update-422.json
     */
    public function update(UpdateEmailConfigRequest $req)
    {
        $emailConfig = EmailConfigManager::updateEmailConfig($req->validated());
        if ($emailConfig->wasChanged()) {
            return response()->success(__('email_config')['update_success'], $emailConfig);
        }
        return response()->success(__('nothing_updated'), $emailConfig);
    }

    /**
     * Get Email Config
     *
     * Retrieves the email config specified by `id`.
     *
     * @bodyParam id int required The ID of the email config. Example: 1
     * @bodyParam email_type string required Max: 191. The type of email. Can be used as an alternative means to identify an email if the `id` is unknown. Example: invitation_reminder
     *
     * @responseFile 200 resources/responses/Admin/EmailConfig/get.json
     * @responseFile 422 resources/responses/Admin/EmailConfig/get-422.json
     */
    public function get(Request $req)
    {
        $req->validate([
            'id' => 'required_without:email_type|
                     not_present_with:email_type|
                     exists:email_configs',
            'email_type' => 'required_without:id|
                             not_present_with:id|
                             exists:email_configs,email_type',
        ]);

        $result = EmailConfigManager::getEmailConfig($req);
        return response()->success(__('email_config')['found_success'], $result);
    }

    /**
     * List Email Configs
     *
     * Lists all email config details.
     *
     * @responseFile 200 resources/responses/Admin/EmailConfig/list.json
     */
    public function list()
    {
        return response()->success(__('email_config')['list_success'], EmailConfig::all());
    }

    /**
     * Send Email
     *
     * Triggers the specified email type to be sent to all registered users.
     *
     * @bodyParam id int required The ID of the email being sent. Example: 1
     * @bodyParam email_type string required required Max: 191. The type of email being sent. Can be used as an alternative means to identify an email if the `id` is unknown. Example: invitation_reminder
     * @bodyParam users array IDs of the users to send the email to. If omitted then the email will be sent to all non-admin users in the system. Example: [1, 2, 3]
     *
     * @responseFile 200 resources/responses/Admin/EmailConfig/send.json
     * @responseFile 422 resources/responses/Admin/EmailConfig/send-422.json
     */
    public function sendEmail(Request $req)
    {
        $req->validate([
            'id' => 'required_without:email_type|
                     not_present_with:email_type|
                     exists:email_configs,id',
            'email_type' => 'required_without:id|
                             not_present_with:id|
                             exists:email_configs,email_type',
            'users' => 'array',
            'users.*' => 'distinct|exists:users,id'
        ]);

        $emailConfig = isset($req->id)
            ? EmailConfig::find($req->id)
            : EmailConfig::findByType($req->email_type);

        $users = isset($req->users)
            ? User::findMany($req->users)
            : Role::firstWhere('name', 'user')->users;

        if ($users->isEmpty()) {
            return response()->error(__('manual_email')['failure']);
        }

        foreach ($users as $user) {
            Mail::to($user)->send($emailConfig->makeMailable($user));
        }

        return response()->success(__('manual_email')['success']);
    }
}

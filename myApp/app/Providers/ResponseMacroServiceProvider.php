<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('success', function ($message = null, $data = null, $paginated = false) {
            if (is_null($message)) {
                $message = __('handler')['success'];
            }

            $json = [
                'status' => 'success',
                'message' => $message
            ];

            if ($paginated) {
                $json = collect($json)->merge($data);
            } else if (!is_null($data)) {
                $json['data'] = $data;
            }

            return response()->json($json);
        });

        Response::macro('error', function ($message = null, $errors = null, $status = 422) {
            if (is_null($message)) {
                $message = __('handler')['validation_exception'];
            }

            $json = [
                'status' => 'error',
                'message' => $message
            ];

            if (!is_null($errors)) {
                $json['errors'] = $errors;
            }
            return response()->json($json, $status);
        });
    }
}

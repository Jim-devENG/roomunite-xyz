<?php

namespace App\Http\Controllers\Web\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\EmailController;
use App\Http\Helpers\Common;
use App\Models\User;
use App\Models\PasswordResets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DateTime;

class ForgotPasswordController extends Controller
{
    protected $helper;

    public function __construct()
    {
        $this->helper = new Common();
    }

    /**
     * Send password reset link
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResetLink(Request $request, EmailController $email_controller)
    {
        $rules = array(
            'email' => 'required|email|exists:users,email|max:200',
        );

        $messages = array(
            'required' => ':attribute is required.',
            'exists'   => 'Email does not exist in our records.',
        );

        $fieldNames = array(
            'email' => 'Email',
        );

        $validator = Validator::make($request->all(), $rules, $messages);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::whereEmail($request->email)->first();
            $email_controller->forgot_password($user);

            return response()->json([
                'status' => 'success',
                'message' => 'Password reset link has been sent to your email address.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send reset link: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset password
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $rules = array(
            'token'                 => 'required',
            'email'                 => 'required|email|exists:users,email',
            'password'              => 'required|min:6|max:30',
            'password_confirmation' => 'required|same:password',
        );

        $fieldNames = array(
            'token'                 => 'Token',
            'email'                 => 'Email',
            'password'              => 'New Password',
            'password_confirmation' => 'Confirm Password',
        );

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $password_resets = PasswordResets::whereToken($request->token)
                ->where('email', $request->email)
                ->first();

            if (!$password_resets) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid or expired reset token.'
                ], 400);
            }

            // Check if token is expired (1 hour)
            $datetime1 = new DateTime();
            $datetime2 = new DateTime($password_resets->created_at);
            $interval  = $datetime1->diff($datetime2);
            $hours     = $interval->format('%h');

            if ($hours >= 1) {
                $password_resets->delete();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Reset token has expired. Please request a new one.'
                ], 400);
            }

            // Update user password
            $user = User::whereEmail($request->email)->first();
            $user->password = \Hash::make($request->password);
            $user->save();

            // Delete the reset token
            $password_resets->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Password has been reset successfully.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to reset password: ' . $e->getMessage()
            ], 500);
        }
    }
}


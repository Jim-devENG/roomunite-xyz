<?php

namespace App\Http\Controllers\Mobile\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\EmailController;
use App\Http\Helpers\Common;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\UsersVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use DB;

class AuthController extends Controller
{
    protected $helper;

    public function __construct()
    {
        $this->helper = new Common();
    }

    /**
     * Register a new user (Mobile)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request, EmailController $email_controller)
    {
        $rules = array(
            'first_name'      => 'required|max:255',
            'last_name'       => 'required|max:255',
            'email'           => 'required|max:255|email|unique:users',
            'password'        => 'required|min:6',
            'date_of_birth'   => 'check_age',
            'birthday_day'    => 'required',
            'birthday_month'  => 'required',
            'birthday_year'   => 'required',
        );

        $messages = array(
            'required'                => ':attribute is required.',
            'birthday_day.required'   => 'Birth date field is required.',
            'birthday_month.required' => 'Birth date field is required.',
            'birthday_year.required'  => 'Birth date field is required.',
        );

        $fieldNames = array(
            'first_name'      => 'First name',
            'last_name'       => 'Last name',
            'email'           => 'Email',
            'password'        => 'Password',
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
            $user = new User();
            $user->first_name = $request->first_name;
            $user->last_name  = $request->last_name;
            $user->email      = $request->email;
            $user->password   = Hash::make($request->password);
            $user->status     = 'Active';
            $user->save();

            // Create user details
            $user_details = new UserDetails();
            $user_details->user_id = $user->id;
            $user_details->field   = 'date_of_birth';
            $user_details->value   = $request->birthday_year . '-' . $request->birthday_month . '-' . $request->birthday_day;
            $user_details->save();

            // Create user verification record
            $user_verification = new UsersVerification();
            $user_verification->user_id = $user->id;
            $user_verification->save();

            // Create wallet for user
            $user_controller = new \App\Http\Controllers\UserController();
            $user_controller->wallet($user->id);

            // Send welcome email
            $email_controller->welcome_email($user);

            // Generate token (if using Laravel Sanctum/Passport)
            $token = null;
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();
                // If using Sanctum, uncomment:
                // $token = $user->createToken('mobile-auth-token')->plainTextToken;
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Registration successful',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                    ],
                    'token' => $token
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Login user (Mobile)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $rules = array(
            'email'    => 'required|email|max:200',
            'password' => 'required',
        );

        $fieldNames = array(
            'email'    => 'Email',
            'password' => 'Password',
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

        $user = User::where('email', $request->email)->first();

        if (empty($user)) {
            return response()->json([
                'status' => 'error',
                'message' => 'There isn\'t an account associated with this email address.'
            ], 401);
        }

        if ($user->status == 'Inactive') {
            return response()->json([
                'status' => 'error',
                'message' => 'User is inactive. Please contact support.'
            ], 403);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            
            // Generate token (if using Laravel Sanctum/Passport)
            $token = null;
            // If using Sanctum, uncomment:
            // $token = $user->createToken('mobile-auth-token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                    ],
                    'token' => $token
                ]
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid email or password'
            ], 401);
        }
    }
}


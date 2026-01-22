<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\{
    Currency,
    Language,
    Settings,
    StartingCities,
    JoinUs,
    Banners,
    Country,
    Page
};

use View, Config, Schema, Auth, App, Session, Validator, Cache, DB;


class SetDataServiceProvider extends ServiceProvider
{
    public function boot()
    {
        try {
            // Define SITE_NAME constant early, always available
            if (!defined('SITE_NAME')) {
                define('SITE_NAME', 'RoomUnite');
            }
            View::share('site_name', 'RoomUnite');
            
            // Always register credit card validation (doesn't need database)
            $this->creditcard_validation();
            
            // Check if database is configured and accessible
            if (env('DB_DATABASE')) {
                try {
                    // Test database connection first with a quick query (with timeout)
                    try {
                        // Set a timeout for the connection test
                        $originalTimeout = ini_get('default_socket_timeout');
                        ini_set('default_socket_timeout', 3); // 3 second timeout for connection
                        
                        DB::connection()->getPdo();
                        
                        // Restore original timeout
                        ini_set('default_socket_timeout', $originalTimeout);
                    } catch (\Exception $e) {
                        // Database connection failed, skip database-dependent features
                        \Log::warning('Database connection failed in SetDataServiceProvider: ' . $e->getMessage());
                        return;
                    }
                    
                    // Check if tables exist before trying to use them (with individual error handling)
                    // Use a timeout to prevent hanging on schema checks
                    $schemaCheckStart = microtime(true);
                    $schemaTimeout = 2; // 2 seconds max for all schema checks
                    
                    try {
                        if ((microtime(true) - $schemaCheckStart) < $schemaTimeout && Schema::hasTable('currency')) {
                            $this->currency();
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Currency table check failed: ' . $e->getMessage());
                    }

                    try {
                        if ((microtime(true) - $schemaCheckStart) < $schemaTimeout && Schema::hasTable('language')) {
                            $this->language();
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Language table check failed: ' . $e->getMessage());
                    }

                    try {
                        if ((microtime(true) - $schemaCheckStart) < $schemaTimeout && Schema::hasTable('settings')) {
                            $this->settings();
                            $this->api_info_set();
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Settings table check failed: ' . $e->getMessage());
                    }
                    
                    try {
                        if ((microtime(true) - $schemaCheckStart) < $schemaTimeout && Schema::hasTable('pages')) {
                            $this->pages();
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Pages table check failed: ' . $e->getMessage());
                    }

                    try {
                        if ((microtime(true) - $schemaCheckStart) < $schemaTimeout && Schema::hasTable('starting_cities')) {
                            $this->destination();
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Starting cities table check failed: ' . $e->getMessage());
                    }

                    try {
                        if ((microtime(true) - $schemaCheckStart) < $schemaTimeout && Schema::hasTable('banners')) {
                            $this->banner();
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Banners table check failed: ' . $e->getMessage());
                    }
                } catch (\Exception $e) {
                    // If database connection fails, continue without database-dependent features
                    // This allows the app to at least show an error page instead of crashing
                    \Log::error('SetDataServiceProvider boot failed: ' . $e->getMessage());
                    \Log::error('Stack trace: ' . $e->getTraceAsString());
                }
            }
        } catch (\Throwable $e) {
            // Catch any fatal errors
            \Log::error('SetDataServiceProvider fatal error: ' . $e->getMessage());
            \Log::error('File: ' . $e->getFile() . ':' . $e->getLine());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            // Don't rethrow - allow app to continue
        }
    }

    public function creditcard_validation()
    {

        Validator::extend('expires', function ($attribute, $value, $parameters, $validator) {
            $input      = $validator->getData();
            $expiryDate = gmdate('Ym', gmmktime(0, 0, 0, (int) array_get($input, $parameters[0]), 1, (int) array_get($input, $parameters[1])));
            return ($expiryDate > gmdate('Ym')) ? true : false;
        });

        Validator::extend('validate_cc', function ($attribute, $value, $parameters) {
            $str = '';
            foreach (array_reverse(str_split($value)) as $i => $c) {
                $str .= $i % 2 ? $c * 2 : $c;
            }
            return array_sum(str_split($str)) % 10 === 0;
        });
    }

    public function register()
    {
        //
    }

    public function currency()
    {
        try {
            // Get currencies once and reuse
            $allCurrencies = Currency::getAll();
            $currencies = $allCurrencies->where('status', '=', 'Active');
            View::share('currencies', $currencies);
            View::share('currency', $currencies->pluck('code', 'code'));

            if(!\Session::get('currency')) {
                // Try to get currency from geolocation with timeout protection
                $default_currency = null;
                $default_country = null;
                
                try {
                    // Check if REMOTE_ADDR exists and is not empty
                    if(isset($_SERVER["REMOTE_ADDR"]) && !empty($_SERVER["REMOTE_ADDR"])) {
                        // Use stream_context_create with timeout to prevent hanging
                        $context = stream_context_create([
                            'http' => [
                                'timeout' => 2, // 2 second timeout
                                'ignore_errors' => true
                            ]
                        ]);
                        
                        $url = 'http://www.geoplugin.net/php.gp?ip=' . $_SERVER["REMOTE_ADDR"];
                        $remoteDataContent = @file_get_contents($url, false, $context);
                        
                        if($remoteDataContent !== false) {
                            $remoteData = @unserialize($remoteDataContent);
                            if($remoteData && isset($remoteData['geoplugin_currencyCode'])) {
                                // Use already loaded currencies instead of calling getAll() again
                                $default_currency = $currencies->where('code', '=', $remoteData['geoplugin_currencyCode'])->first();
                                $default_country = isset($remoteData['geoplugin_countryCode']) ? $remoteData['geoplugin_countryCode'] : null;
                            }
                        }
                    }
                } catch(\Exception $e) {
                    // Silently fail and use fallback
                }
                
                // Fallback to default currency if geolocation failed - use already loaded data
                if(!$default_currency) {
                    $default_currency = $allCurrencies->firstWhere('default', '=', '1');
                }
            } else {
                // Use already loaded currencies - check if session is available
                if (\Session::isStarted()) {
                    $sessionCurrency = \Session::get('currency');
                    if ($sessionCurrency) {
                        $default_currency = $allCurrencies->firstWhere('code', $sessionCurrency);
                    }
                }
            }

            if(!$default_currency) {
                $default_currency = $allCurrencies->firstWhere('default', '=', '1');
            }

            // If still no currency found, get the first active currency as fallback
            if(!$default_currency) {
                $default_currency = $currencies->first();
            }

            if(!isset($default_country)) {
                try {
                    $firstCountry = Country::getAll()->first();
                    $default_country = $firstCountry ? $firstCountry->short_name : 'US';
                } catch(\Exception $e) {
                    $default_country = 'US';
                }
            }

            View::share('default_country', $default_country);
            View::share('default_currency', $default_currency);
            
            // Only set session if currency exists and session is available
            if($default_currency && \Session::isStarted()) {
                Session::put('currency', $default_currency->code);
                Session::put('symbol', $default_currency->symbol);
            }
        } catch(\Exception $e) {
            // If currency loading fails, continue without currency data
            // This prevents the entire app from crashing
            \Log::warning('Currency loading failed in SetDataServiceProvider: ' . $e->getMessage());
        }
    }

    public function language()
    {
        $language = Language::where('status', '=', 'Active')->pluck('name', 'short_name');
        View::share('language', $language);

        $default_language = Language::where('status', '=', 'Active')->where('default', '=', '1')->limit(1)->get();
        View::share('default_language', $default_language);
        if ($default_language->count() > 0) {
            Session::put('language', $default_language[0]->value);
            App::setLocale($default_language[0]->value);
        }
    }

    public function pages()
    {
        $footer_first  = Page::where('position', 'first')->where('status', 'Active')->get();
        $footer_second = Page::where('position', 'second')->where('status', 'Active')->get();
        $footer_third = Page::where('position', 'third')->where('status', 'Active')->get();
        $footer_fourth = Page::where('position', 'fourth')->where('status', 'Active')->get();
        View::share('footer_first', $footer_first);
        View::share('footer_second', $footer_second);
        View::share('footer_third', $footer_third);
        View::share('footer_fourth', $footer_fourth);
    }

    public function destination()
    {
        $popular_cities  = StartingCities::where('status', 'Active')->get();
        View::share('popular_cities', $popular_cities);
    }

    public function api_info_set()
    {
        $google   = Settings::where('type', 'google')->pluck('value', 'name')->toArray();
        $facebook = Settings::where('type', 'facebook')->pluck('value', 'name')->toArray();
        if (isset($google['client_id'])) {
            \Config::set(['services.google' => [
                    'client_id' => $google['client_id'],
                    'client_secret' => $google['client_secret'],
                    'redirect' => url('/googleAuthenticate'),
                    ]
                ]);
        }

        if (isset($facebook['client_id'])) {
             \Config::set(['services.facebook' => [
                        'client_id' => $facebook['client_id'],
                        'client_secret' => $facebook['client_secret'],
                        'redirect' => url('/facebookAuthenticate'),
                        ]
                        ]);
        }
    }


    public function settings()
    {
        // Set site name first, always available
        $name = 'RoomUnite';
        if (!defined('SITE_NAME')) {
            define('SITE_NAME', $name);
        }
        View::share('site_name', $name);
        Config::set('site_name', $name);

        $settings = Settings::getAll();
        if (!empty($settings)) {

            // General settings
            $general = $settings->where('type', 'general')->pluck('value', 'name')->toArray();

            // Override with settings if available
            if (!empty($general['name'])) {
                $name = $general['name'];
                View::share('site_name', $name);
                Config::set('site_name', $name);
            }


            //App logo
            if (!empty($general['logo']) && file_exists(public_path('front/front/images/logos/'. $general['logo']))) {
                $logo = asset('front/front/images/logos/'. $general['logo']);
            } else {
                $logo = env('APP_LOGO_URL') != '' ? env('APP_LOGO_URL') : asset('front/front/images/logos/logo.png');
            }
            if (!defined('LOGO_URL')) {
                define('LOGO_URL', $logo);
            }
            View::share('logo', $logo);



            //App email logo
            if (!empty($general['email_logo']) && file_exists(public_path('front/front/images/logos/'. $general['email_logo']))) {
                $emailLogo = asset('front/front/images/logos/'. $general['email_logo']);
            } else {
                $emailLogo = env('APP_EMAIL_LOGO_URL') != '' ? env('APP_EMAIL_LOGO_URL') : asset('front/front/images/logos/email_logo.png');
            }
            if (!defined('EMAIL_LOGO_URL')) {
                define('EMAIL_LOGO_URL', $emailLogo);
            }

            //App head code/Analytics code
            $headCode = !empty($general['head_code']) ? $general['head_code'] : env('APP_HEAD_CODE', '');
            View::share('head_code', $headCode);

            //App favicon
            if (!empty($general['favicon']) && file_exists(public_path('front/front/images/logos/'. $general['favicon']))) {

                $favicon = asset('front/front/images/logos/'. $general['favicon']);
            } else {
                $favicon = env('APP_FAVICON_URL') != '' ? env('APP_FAVICON_URL') : asset('front/front/images/logos/favicon.png');
            }
            View::share('favicon', $favicon);

            // Google Map Key
            $map     = $settings->where('type', 'googleMap')->pluck('value', 'name')->toArray();
            if (!empty($map['key'])) {
                    View::share('map_key', $map['key']);
                    define('MAP_KEY', $map['key']);
            }

            // Join us
            $join_us = Settings::where('type', 'join_us')->get();
            View::share('join_us', $join_us);

            View::share('settings', $settings);
        }
    }

    public function banner()
    {
        //App Banner
        $banner = Banners::where('status', 'Active')->first();

        if ( !empty($banner) && file_exists(public_path('front/front/images/banners/'.$banner->image)) )
        {
            $banner_image = asset('front/front/images/banners/'.$banner->image);
        } else {
            $banner_image = asset('images/default-banner.jpg');
        }

        if ( !defined('BANNER_URL') ) {
            define('BANNER_URL', $banner_image);
        }
        
        View::share('banner_url', $banner_image);
    }
}

<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Helpers\Common;
use App\Http\Controllers\Controller;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Cache;


use View, Auth, App, Session, Route;

use App\Models\{
    Currency,
    Properties,
    Page,
    Settings,
    StartingCities,
    Testimonials,
    language,
    Admin,
    User,
    Wallet
};


require base_path() . '/vendor/autoload.php';

class HomeController extends Controller
{
    private $helper;

    public function __construct()
    {
        $this->helper = new Common;
    }

    public function index()
    {
        try {
            // Initialize data array with defaults
            $data = [];
            
            // Safely get starting cities
            try {
                $data['starting_cities'] = StartingCities::getAll();
            } catch (\Exception $e) {
                \Log::warning('Failed to load starting cities: ' . $e->getMessage());
                $data['starting_cities'] = collect([]);
            }
            
            // Safely get properties
            try {
                $data['properties'] = Properties::recommendedHome();
            } catch (\Exception $e) {
                \Log::warning('Failed to load properties: ' . $e->getMessage());
                $data['properties'] = collect([]);
            }
            
            // Safely get testimonials
            try {
                $data['testimonials'] = Testimonials::getAll();
            } catch (\Exception $e) {
                \Log::warning('Failed to load testimonials: ' . $e->getMessage());
                $data['testimonials'] = collect([]);
            }
            
            $sessionLanguage = Session::get('language');
            
            // Safely get language setting with null check
            try {
                $language = Settings::getAll()->where('name', 'default_language')->where('type', 'general')->first();
                
                if ($language && $language->value) {
                    $languageDetails = language::where(['id' => $language->value])->first();

                    if (!($sessionLanguage) && $languageDetails) {
                        Session::pull('language');
                        Session::put('language', $languageDetails->short_name);
                        App::setLocale($languageDetails->short_name);
                    }
                } else {
                    // Fallback to default language if settings not found
                    if (!($sessionLanguage)) {
                        Session::put('language', 'en');
                        App::setLocale('en');
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to load language settings: ' . $e->getMessage());
                if (!($sessionLanguage)) {
                    Session::put('language', 'en');
                    App::setLocale('en');
                }
            }

            // Safely get settings
            try {
                $pref = Settings::getAll();
                $prefer = [];

                if (!empty($pref)) {
                    foreach ($pref as $value) {
                        $prefer[$value->name] = $value->value;
                    }
                    Session::put($prefer);
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to load settings: ' . $e->getMessage());
            }
            
            // Safely get date format with null check
            try {
                $dateFormatSetting = Settings::getAll()->firstWhere('name', 'date_format_type');
                $data['date_format'] = $dateFormatSetting ? $dateFormatSetting->value : 'Y-m-d';
            } catch (\Exception $e) {
                \Log::warning('Failed to load date format: ' . $e->getMessage());
                $data['date_format'] = 'Y-m-d';
            }

            return view('home.home', $data);
        } catch (\Exception $e) {
            // Log the error and show a basic error page
            \Log::error('HomeController@index error: ' . $e->getMessage());
            \Log::error('File: ' . $e->getFile() . ':' . $e->getLine());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Return a simple error view or redirect
            abort(500, 'An error occurred while loading the page. Please check the logs.');
        }
    }

    public function phpinfo()
    {
        echo phpinfo();
    }

    public function login()
    {
        return view('home.login');
    }

    public function setSession(Request $request)
    {
        if ($request->currency) {
            Session::put('currency', $request->currency);
            $symbol = Currency::code_to_symbol($request->currency);
            Session::put('symbol', $symbol);
        } elseif ($request->language) {
            Session::put('language', $request->language);
            $name = language::name($request->language);
            Session::put('language_name', $name);
            App::setLocale($request->language);
        }
    }

    public function cancellation_policies()
    {
        return view('home.cancellation_policies');
    }

    public function staticPages(Request $request)
    {
        $pages          = Page::where(['url'=>$request->name, 'status'=>'Active']);
        if (!$pages->count()) {
            abort('404');
        }
        $pages           = $pages->first();
        $data['content'] = str_replace(['SITE_NAME', 'SITE_URL'], [SITE_NAME, url('/')], $pages->content);
        $data['title']   = $pages->url;
        $data['url']     = url('/').'/';
        $data['img']     = $data['url'].'public/images/2222hotel_room2.jpg';

        return view('home.static_pages', $data);
    }


    public function activateDebugger()
    {
      setcookie('debugger', 0);
    }

    public function walletUser(Request $request){

        $users = User::all();
        $wallet = Wallet::all();


        if (!$users->isEmpty() && $wallet->isEmpty() ) {
            foreach ($users as $key => $user) {

                Wallet::create([
                    'user_id' => $user->id,
                    'currency_id' => 1,
                    'balance' => 0,
                    'is_active' => 0
                ]);
            }
        }

        return redirect('/');

    }

}

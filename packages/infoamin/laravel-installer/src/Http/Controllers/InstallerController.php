<?php

namespace Infoamin\Installer\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class InstallerController extends Controller
{
    /**
     * Display the installer welcome page.
     *
     * @return \Illuminate\View\View
     */
    public function welcome()
    {
        return view('installer::welcome');
    }

    /**
     * Display the requirements check page.
     *
     * @return \Illuminate\View\View
     */
    public function requirements()
    {
        $requirements = $this->checkRequirements();
        return view('installer::requirements', compact('requirements'));
    }

    /**
     * Display the permissions check page.
     *
     * @return \Illuminate\View\View
     */
    public function permissions()
    {
        $permissions = $this->checkPermissions();
        return view('installer::permissions', compact('permissions'));
    }

    /**
     * Display the database configuration page.
     *
     * @return \Illuminate\View\View
     */
    public function database()
    {
        return view('installer::database');
    }

    /**
     * Save database configuration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveDatabase(Request $request)
    {
        $request->validate([
            'host' => 'required',
            'database' => 'required',
            'username' => 'required',
        ]);

        // Update .env file with database configuration
        $envFile = base_path('.env');
        $envContent = File::get($envFile);
        
        $envContent = preg_replace('/DB_HOST=(.*)/', 'DB_HOST=' . $request->host, $envContent);
        $envContent = preg_replace('/DB_DATABASE=(.*)/', 'DB_DATABASE=' . $request->database, $envContent);
        $envContent = preg_replace('/DB_USERNAME=(.*)/', 'DB_USERNAME=' . $request->username, $envContent);
        $envContent = preg_replace('/DB_PASSWORD=(.*)/', 'DB_PASSWORD=' . $request->password, $envContent);
        
        File::put($envFile, $envContent);

        return redirect()->route('LaravelInstaller::purchasecode');
    }

    /**
     * Display the purchase code page.
     *
     * @return \Illuminate\View\View
     */
    public function purchasecode()
    {
        return view('installer::purchasecode');
    }

    /**
     * Save purchase code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function savePurchaseCode(Request $request)
    {
        $request->validate([
            'purchase_code' => 'required',
        ]);

        // Save purchase code to .env or config
        $envFile = base_path('.env');
        $envContent = File::get($envFile);
        
        if (strpos($envContent, 'PURCHASE_CODE=') === false) {
            $envContent .= "\nPURCHASE_CODE=" . $request->purchase_code;
        } else {
            $envContent = preg_replace('/PURCHASE_CODE=(.*)/', 'PURCHASE_CODE=' . $request->purchase_code, $envContent);
        }
        
        File::put($envFile, $envContent);

        return redirect()->route('LaravelInstaller::register');
    }

    /**
     * Display the registration page.
     *
     * @return \Illuminate\View\View
     */
    public function register()
    {
        return view('installer::register');
    }

    /**
     * Save registration information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveRegister(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Run migrations and create admin user
        try {
            Artisan::call('migrate', ['--force' => true]);
            
            // Create admin user logic here
            // This would typically create the first admin user in the database
            
            return redirect()->route('LaravelInstaller::finish');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the finish page.
     *
     * @return \Illuminate\View\View
     */
    public function finish()
    {
        // Mark installation as complete
        $envFile = base_path('.env');
        $envContent = File::get($envFile);
        
        if (strpos($envContent, 'APP_INSTALLED=') === false) {
            $envContent .= "\nAPP_INSTALLED=true";
        } else {
            $envContent = preg_replace('/APP_INSTALLED=(.*)/', 'APP_INSTALLED=true', $envContent);
        }
        
        File::put($envFile, $envContent);

        return view('installer::finish');
    }

    /**
     * Check PHP requirements.
     *
     * @return array
     */
    private function checkRequirements()
    {
        $requirements = [
            'php_version' => version_compare(PHP_VERSION, '8.0.0', '>='),
            'openssl' => extension_loaded('openssl'),
            'pdo' => extension_loaded('pdo'),
            'mbstring' => extension_loaded('mbstring'),
            'tokenizer' => extension_loaded('tokenizer'),
            'json' => extension_loaded('json'),
            'ctype' => extension_loaded('ctype'),
            'fileinfo' => extension_loaded('fileinfo'),
            'gd' => extension_loaded('gd'),
        ];

        return $requirements;
    }

    /**
     * Check directory permissions.
     *
     * @return array
     */
    private function checkPermissions()
    {
        $permissions = [
            'storage' => is_writable(storage_path()),
            'bootstrap/cache' => is_writable(base_path('bootstrap/cache')),
        ];

        return $permissions;
    }
}









<?php

namespace App\Http\Controllers\Install;

use App\Http\Controllers\Controller;
use App\Support\Install\InstallLock;
use App\Support\Install\InstallRequirements;
use App\Support\Install\InstallService;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class InstallController extends Controller
{
    public function requirements(InstallRequirements $requirements): View
    {
        return view('install.requirements', [
            'checks' => $requirements->checks(),
            'passed' => $requirements->passes(),
        ]);
    }

    public function storeRequirements(InstallRequirements $requirements): RedirectResponse
    {
        if (! $requirements->passes()) {
            return back()->withErrors([
                'requirements' => 'Resolve all requirement failures before continuing.',
            ]);
        }

        session(['install.requirements_passed' => true]);

        return redirect()->route('install.application');
    }

    public function application(): View|RedirectResponse
    {
        if (! session('install.requirements_passed')) {
            return redirect()->route('install.requirements');
        }

        return view('install.application', [
            'appName' => old('app_name', config('app.name', 'ContentFlow CMS')),
            'appUrl' => old('app_url', config('app.url', 'http://localhost')),
        ]);
    }

    public function storeApplication(Request $request, InstallService $installService): RedirectResponse
    {
        if (! session('install.requirements_passed')) {
            return redirect()->route('install.requirements');
        }

        $validated = $request->validate([
            'app_name' => ['required', 'string', 'max:150', 'regex:/^[^\r\n]+$/'],
            'app_url' => ['required', 'url', 'max:255'],
        ]);

        try {
            $installService->saveApplicationConfiguration([
                'app_name' => $validated['app_name'],
                'app_url' => $validated['app_url'],
            ]);
        } catch (\InvalidArgumentException $exception) {
            return back()->withInput()->withErrors(['app_name' => $exception->getMessage()]);
        }

        session([
            'install.application' => [
                'app_name' => $validated['app_name'],
                'app_url' => $validated['app_url'],
            ],
        ]);

        return redirect()->route('install.database');
    }

    public function database(): View|RedirectResponse
    {
        if (! session('install.application')) {
            return redirect()->route('install.application');
        }

        return view('install.database', [
            'host' => old('host', '127.0.0.1'),
            'port' => old('port', '3306'),
            'database' => old('database', 'contentflow'),
            'username' => old('username', 'root'),
        ]);
    }

    public function storeDatabase(Request $request, InstallService $installService): RedirectResponse
    {
        if (! session('install.application')) {
            return redirect()->route('install.application');
        }

        $validated = $request->validate([
            'host' => ['required', 'string', 'max:255', 'regex:/^[^\r\n;]+$/'],
            'port' => ['required', 'integer', 'min:1', 'max:65535'],
            'database' => ['required', 'string', 'max:255', 'regex:/^[^\r\n;]+$/'],
            'username' => ['required', 'string', 'max:255', 'regex:/^[^\r\n;]+$/'],
            'password' => ['nullable', 'string', 'max:255', 'regex:/^[^\r\n;]*$/'],
        ]);

        try {
            $installService->configureDatabase([
                'host' => $validated['host'],
                'port' => (int) $validated['port'],
                'database' => $validated['database'],
                'username' => $validated['username'],
                'password' => $validated['password'] ?? null,
            ]);

            $installService->migrateAndSeed();
        } catch (\InvalidArgumentException $exception) {
            return back()
                ->withInput($request->except('password'))
                ->withErrors(['database' => $exception->getMessage()]);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            return back()
                ->withInput($request->except('password'))
                ->withErrors($exception->errors());
        }

        session([
            'install.database_configured' => true,
        ]);

        return redirect()->route('install.administrator');
    }

    public function administrator(): View|RedirectResponse
    {
        if (! session('install.database_configured')) {
            return redirect()->route('install.database');
        }

        return view('install.administrator');
    }

    public function storeAdministrator(Request $request): RedirectResponse
    {
        if (! session('install.database_configured')) {
            return redirect()->route('install.database');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);

        session([
            'install.administrator' => [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ],
            'install.administrator_password' => Crypt::encryptString($validated['password']),
        ]);

        return redirect()->route('install.settings');
    }

    public function settings(): View|RedirectResponse
    {
        if (! session('install.administrator')) {
            return redirect()->route('install.administrator');
        }

        $application = session('install.application', []);

        return view('install.settings', [
            'siteName' => old('site_name', $application['app_name'] ?? config('app.name', 'ContentFlow CMS')),
            'timezone' => old('timezone', config('app.timezone', 'UTC')),
            'locale' => old('locale', config('app.locale', 'en')),
            'timezones' => timezone_identifiers_list(),
        ]);
    }

    public function storeSettings(Request $request, InstallService $installService): RedirectResponse
    {
        if (! session('install.administrator') || ! session('install.administrator_password')) {
            return redirect()->route('install.administrator');
        }

        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:150'],
            'timezone' => ['required', 'string', Rule::in(timezone_identifiers_list())],
            'locale' => ['required', 'string', 'max:10'],
        ]);

        $admin = session('install.administrator');

        try {
            $password = Crypt::decryptString(session('install.administrator_password'));
        } catch (DecryptException) {
            return redirect()
                ->route('install.administrator')
                ->withErrors([
                    'password' => 'Your session expired. Enter the administrator password again.',
                ]);
        }

        try {
            $installService->finalizeInstallation(
                [
                    'name' => $admin['name'],
                    'email' => $admin['email'],
                    'password' => $password,
                ],
                [
                    'site_name' => $validated['site_name'],
                    'timezone' => $validated['timezone'],
                    'locale' => $validated['locale'],
                ],
            );
        } catch (\Illuminate\Validation\ValidationException $exception) {
            return back()->withInput()->withErrors($exception->errors());
        }

        session()->forget([
            'install.requirements_passed',
            'install.application',
            'install.database_configured',
            'install.administrator',
            'install.administrator_password',
        ]);

        session(['install.completed' => true]);

        return redirect()->route('install.complete');
    }

    public function complete(): View|RedirectResponse
    {
        if (! InstallLock::isLocked() && ! session('install.completed')) {
            return redirect()->route('install.requirements');
        }

        if (session('install.completed')) {
            session()->forget('install.completed');
        }

        return view('install.complete');
    }
}

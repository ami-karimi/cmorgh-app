<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


Route::get('/', [\App\Http\Controllers\WelcomeController::class,'index'])->name('main');
Route::get('/convert', [\App\Http\Controllers\WelcomeController::class,'convert'])->name('convert');


Route::middleware('guest')->group(function () {
    Route::get('/login', \App\Livewire\Auth\Login::class)->name('login');
});


Route::middleware(['auth'])->group(function () {
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();

        $request->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');

    Route::get('/dashboard')->name('user.dashboard');
});

Route::middleware(['auth', 'is_customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();

        $request->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');

    Route::get('/dashboard', \App\Livewire\Customer\Dashboard::class)->name('dashboard');
    Route::get('/my-orders', \App\Livewire\Customer\MyOrders::class)->name('orders');
});

 Route::get('/store', \App\Livewire\Store\Storefront::class)->name('store.index');
 Route::get('/tutorials', \App\Livewire\User\ConnectionGuide::class)->name('tutorials.index');

Route::middleware(['auth', 'is_reseller'])->prefix('reseller')->name('reseller.')->group(function () {

    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();

        $request->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');

    Route::get('/',\App\Livewire\Agent\Dashboard::class)->name('dashboard');
    Route::get('/customers',\App\Livewire\Agent\CustomerManager::class)->name('customers');
    Route::get('/settings',\App\Livewire\Agent\AgentSettingsManager::class)->name('settings');
    Route::get('/financial',\App\Livewire\Agent\FinancialManager::class)->name('financial');
    Route::get('/account/accounts', \App\Livewire\Agent\AccountManager::class)->name('accounts.index');
    Route::get('/profile', \App\Livewire\Agent\ProfileSettings::class)->name('profile.show');
    Route::get('/store-order', \App\Livewire\Agent\AgentOrdersManager::class)->name('store.orders');
    Route::get('/sub-agents', \App\Livewire\Agent\SubAgentManager::class)->name('sub-agents');
    Route::get('/account/create/{customer_id?}', \App\Livewire\Agent\AccountCreate::class)->name('accounts.create');
    Route::get('/users/{id}', \App\Livewire\Agent\UserDetails::class)->name('users.show');
    Route::get('/account/{id}', \App\Livewire\Agent\AccountDetail::class)->name('accounts.show');
});


Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/',\App\Livewire\Admin\Dashboard::class)->name('dashboard');
    Route::get('/tutorial', \App\Livewire\Admin\TutorialManager::class)->name('tutorial');
    Route::get('/store-orders', \App\Livewire\Admin\StoreOrdersManager::class)->name('store.orders');
    Route::get('/managers/list',\App\Livewire\Admin\ManagerList::class)->name('managers.list');
    Route::get('/managers/{manager}/edit', \App\Livewire\Admin\ManagerEdit::class)->name('managers.edit');
    Route::get('/announcements', \App\Livewire\Admin\AnnouncementsManager::class)->name('announcements');
    Route::get('/financial-transactions', \App\Livewire\Admin\AdminFinancialManager::class)->name('financial');
    Route::get('/payment-methods', \App\Livewire\Admin\AdminPaymentMethods::class)->name('payment.methods');
    Route::get('/system-monitor', \App\Livewire\Admin\SystemMonitor::class)->name('system.monitor');
    Route::get('/charge/list',\App\Livewire\Admin\ChargeManager::class)->name('charge.list');
    Route::get('/nas/list',\App\Livewire\Admin\ServerManager::class)->name('nas.list');
    Route::get('/groups/list',\App\Livewire\Admin\GroupManager::class)->name('groups.list');
    Route::get('/accounts/list',\App\Livewire\Admin\AccountManager::class)->name('accounts.list');
    Route::get('/accounts/activity',\App\Livewire\Admin\ActivityLogManager::class)->name('accounts.logs');
    Route::get('/accounts/create',\App\Livewire\Admin\AccountCreate::class)->name('accounts.create');
    Route::get('/accounts/{id}',\App\Livewire\Admin\AccountDetails::class)->name('accounts.show');
    Route::get('/users',\App\Livewire\Admin\UserManager::class)->name('users.index');
    Route::get('/settings',\App\Livewire\Admin\SiteSettings::class)->name('settings');
    Route::get('/users/{id}',\App\Livewire\Admin\UserDetails::class)->name('users.show');

});


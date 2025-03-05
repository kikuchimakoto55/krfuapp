<?php

namespace App\Providers;

<<<<<<< HEAD
// use Illuminate\Support\Facades\Gate;
=======
use Illuminate\Auth\Notifications\ResetPassword;
>>>>>>> b7b2b51 (Initial commit: Laravel project setup)
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
<<<<<<< HEAD
     * The model to policy mappings for the application.
=======
     * The policy mappings for the application.
>>>>>>> b7b2b51 (Initial commit: Laravel project setup)
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
<<<<<<< HEAD
        //
=======
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
>>>>>>> b7b2b51 (Initial commit: Laravel project setup)
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
<<<<<<< HEAD
=======
        $this->registerPolicies();

        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

>>>>>>> b7b2b51 (Initial commit: Laravel project setup)
        //
    }
}

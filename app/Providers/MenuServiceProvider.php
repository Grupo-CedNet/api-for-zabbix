<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;

class MenuServiceProvider extends ServiceProvider
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
        Event::listen(BuildingMenu::class, function (BuildingMenu $event) {
            // Add some items to the menu...


            $event->menu->add(['header' => 'reports']); // Sessão de relatórios.

            $event->menu->add([
                "text" => "dbm-reports",
                "icon" => "fas fa-fw fa-chart-line",
                "submenu" => [
                    [
                        "text" => "collections-olt-show",
                        "route" => 'admin.collections.olt.config',
                        "icon" => "fas fa-fw fa-list"
                    ],
                    [
                        "text" => "collections-dbm-show",
                        "route" => 'admin.collections.dbms.pons',
                        "icon" => "fas fa-fw fa-list",
                    ]
                ]
            ]);


            // Configurações Globais de Ambiente.
            $event->menu->add(['header' => 'global_settings']); // Sessão de configurações de conta.

            // Configurações de variáveis de ambiente.
            $event->menu->add([
                'text' => 'variables',
                'route'  => 'admin.variables.globals.index',
                'icon' => 'fas fa-circle-notch',
            ]);

            $event->menu->add(['header' => 'administration']); // Sessão administração

            // Insere link de acesso a lista de apis.
            if(checkNivel(auth()->user()->id, "keyaccess") || checkNivel(auth()->user()->id, "*")) {
                $event->menu->add([
                    'text' => 'apis',
                    'route'  => 'admin.apis.list',
                    'icon' => 'fas fa-fw fa-key',
                ]);
            }

            // Insere link de acesso a lista de grupo de usuários.
            if(checkNivel(auth()->user()->id, "ugroup") || checkNivel(auth()->user()->id, "*")) {
                $event->menu->add([
                    'text' => 'gpusers',
                    'route'  => 'admin.users.group',
                    'icon' => 'fas fa-fw fa-users-cog',
                ]);
            }

            // Insere link de acesso a lista de usuários.
            if(checkNivel(auth()->user()->id, "ulist") || checkNivel(auth()->user()->id, "*")) {
                $event->menu->add([
                    'text' => 'users',
                    'route'  => 'admin.users',
                    'icon' => 'fas fa-fw fa-users',
                ]);
            }


            // Configurações de Conta.
            $event->menu->add(['header' => 'account_settings']); // Sessão de configurações de conta.

            // Insere link de acesso ao perfil.
            $event->menu->add([
                'text' => 'profile',
                'route'  => 'app.profile',
                'icon' => 'fas fa-fw fa-user',
            ]);
        });
    }
}


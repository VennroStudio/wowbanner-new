<?php

declare(strict_types=1);

use App\Components\Http\Middleware\Cookie\ExtractCookies;
use App\Components\Http\Middleware\Identity\Authenticate;
use App\Components\Router\StaticRouteGroup as Group;
use App\Http\Action\v1\Auth\ConfirmEmailAction;
use App\Http\Action\v1\Auth\LoginAction;
use App\Http\Action\v1\Auth\LogoutAction;
use App\Http\Action\v1\Auth\PasswordResetAction;
use App\Http\Action\v1\Auth\PasswordResetConfirmAction;
use App\Http\Action\v1\Auth\RefreshTokenAction;
use App\Http\Action\v1\Material\CreateMaterialAction;
use App\Http\Action\v1\Material\DeleteMaterialAction;
use App\Http\Action\v1\Material\GetMaterialByIdAction;
use App\Http\Action\v1\Material\GetMaterialsAction;
use App\Http\Action\v1\Material\UpdateMaterialAction;
use App\Http\Action\v1\OpenApiAction;
use App\Http\Action\v1\Printing\CreatePrintingAction;
use App\Http\Action\v1\Printing\DeletePrintingAction;
use App\Http\Action\v1\Printing\GetPrintingByIdAction;
use App\Http\Action\v1\Printing\GetPrintingsAction;
use App\Http\Action\v1\Printing\UpdatePrintingAction;
use App\Http\Action\v1\User\CreateUserAction;
use App\Http\Action\v1\User\DeleteAvatarAction;
use App\Http\Action\v1\User\DeleteUserAction;
use App\Http\Action\v1\User\GetUserByIdAction;
use App\Http\Action\v1\User\GetUserRolesAction;
use App\Http\Action\v1\User\GetUsersAction;
use App\Http\Action\v1\User\UploadAvatarAction;
use App\Http\Action\v1\User\UserUpdateAction;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

/** @param App<ContainerInterface> $app */
return static function (App $app): void {
    $app->group('/v1', new Group(static function (RouteCollectorProxy $group): void {
        $group->get('', OpenApiAction::class);

        $group->group('/users', new Group(static function (RouteCollectorProxy $group): void {
            $group->get('', GetUsersAction::class)->add(Authenticate::class);
            $group->post('/create', CreateUserAction::class);
            $group->get('/roles', GetUserRolesAction::class)->add(Authenticate::class);
            $group->get('/{id}', GetUserByIdAction::class)->add(Authenticate::class);
            $group->patch('/update/{id}', UserUpdateAction::class)->add(Authenticate::class);
            $group->delete('/delete/{id}', DeleteUserAction::class)->add(Authenticate::class);
            $group->post('/{id}/avatar', UploadAvatarAction::class)->add(Authenticate::class);
            $group->delete('/{id}/avatar', DeleteAvatarAction::class)->add(Authenticate::class);
        }));

        $group->group('/materials', new Group(static function (RouteCollectorProxy $group): void {
            $group->get('', GetMaterialsAction::class)->add(Authenticate::class);
            $group->post('/create', CreateMaterialAction::class)->add(Authenticate::class);
            $group->patch('/update/{id}', UpdateMaterialAction::class)->add(Authenticate::class);
            $group->delete('/delete/{id}', DeleteMaterialAction::class)->add(Authenticate::class);
            $group->get('/{id}', GetMaterialByIdAction::class)->add(Authenticate::class);
        }));

        $group->group('/printings', new Group(static function (RouteCollectorProxy $group): void {
            $group->get('', GetPrintingsAction::class)->add(Authenticate::class);
            $group->post('/create', CreatePrintingAction::class)->add(Authenticate::class);
            $group->patch('/update/{id}', UpdatePrintingAction::class)->add(Authenticate::class);
            $group->delete('/delete/{id}', DeletePrintingAction::class)->add(Authenticate::class);
            $group->get('/{id}', GetPrintingByIdAction::class)->add(Authenticate::class);
        }));

        $group->group('/auth', new Group(static function (RouteCollectorProxy $group): void {
            $group->post('/login', LoginAction::class);
            $group->post('/refresh', RefreshTokenAction::class)->add(ExtractCookies::class);
            $group->post('/logout', LogoutAction::class)->add(ExtractCookies::class);
            $group->post('/confirm-email', ConfirmEmailAction::class);
            $group->post('/password-reset', PasswordResetAction::class);
            $group->post('/password-reset/confirm', PasswordResetConfirmAction::class);
        }));
    }));
};

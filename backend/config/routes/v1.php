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
use App\Http\Action\v1\Client\CreateClientAction;
use App\Http\Action\v1\Material\CreateMaterialAction;
use App\Http\Action\v1\Material\DeleteMaterialAction;
use App\Http\Action\v1\Material\GetMaterialByIdAction;
use App\Http\Action\v1\Material\GetMaterialsAction;
use App\Http\Action\v1\Material\MaterialImage\CreateMaterialImageAction;
use App\Http\Action\v1\Material\MaterialImage\DeleteMaterialImageAction;
use App\Http\Action\v1\Material\MaterialImage\UpdateMaterialImageAction;
use App\Http\Action\v1\Material\UpdateMaterialAction;
use App\Http\Action\v1\OpenApiAction;
use App\Http\Action\v1\Printing\CreatePrintingAction;
use App\Http\Action\v1\Printing\DeletePrintingAction;
use App\Http\Action\v1\Printing\GetPrintingByIdAction;
use App\Http\Action\v1\Printing\GetPrintingsAction;
use App\Http\Action\v1\Printing\UpdatePrintingAction;
use App\Http\Action\v1\Processing\CreateProcessingAction;
use App\Http\Action\v1\Processing\DeleteProcessingAction;
use App\Http\Action\v1\Processing\GetProcessingByIdAction;
use App\Http\Action\v1\Processing\GetProcessingsAction;
use App\Http\Action\v1\Processing\GetProcessingTypesAction;
use App\Http\Action\v1\Processing\ProcessingImage\CreateProcessingImageAction;
use App\Http\Action\v1\Processing\ProcessingImage\DeleteProcessingImageAction;
use App\Http\Action\v1\Processing\ProcessingImage\UpdateProcessingImageAction;
use App\Http\Action\v1\Processing\UpdateProcessingAction;
use App\Http\Action\v1\User\Admin\AdminUpdateUserAction;
use App\Http\Action\v1\User\CreateUserAction;

use App\Http\Action\v1\User\DeleteAvatarAction;
use App\Http\Action\v1\User\DeleteUserAction;
use App\Http\Action\v1\User\GetUserByIdAction;
use App\Http\Action\v1\User\GetUserRolesAction;
use App\Http\Action\v1\User\GetUsersAction;
use App\Http\Action\v1\User\UploadAvatarAction;
use App\Http\Action\v1\User\UserUpdateAction;
use App\Http\Action\v1\Client\DeleteClientAction;
use App\Http\Action\v1\Client\GetClientByIdAction;
use App\Http\Action\v1\Client\GetClientsAction;
use App\Http\Action\v1\Client\UpdateClientAction;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

/** @param App<ContainerInterface> $app */
return static function (App $app): void {
    $app->group('/v1', new Group(static function (RouteCollectorProxy $group): void {
        $group->get('', OpenApiAction::class);

        $group->group('/admin', new Group(static function (RouteCollectorProxy $group): void {
            $group->patch('/users/update/{id}', AdminUpdateUserAction::class)->add(Authenticate::class);
        }));

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

            // MaterialImage
            $group->post('/{id}/images', CreateMaterialImageAction::class)->add(Authenticate::class);
            $group->patch('/images/{imageId}', UpdateMaterialImageAction::class)->add(Authenticate::class);
            $group->delete('/images/{imageId}', DeleteMaterialImageAction::class)->add(Authenticate::class);
        }));

        $group->group('/printings', new Group(static function (RouteCollectorProxy $group): void {
            $group->get('', GetPrintingsAction::class)->add(Authenticate::class);
            $group->post('/create', CreatePrintingAction::class)->add(Authenticate::class);
            $group->patch('/update/{id}', UpdatePrintingAction::class)->add(Authenticate::class);
            $group->delete('/delete/{id}', DeletePrintingAction::class)->add(Authenticate::class);
            $group->get('/{id}', GetPrintingByIdAction::class)->add(Authenticate::class);
        }));

        $group->group('/processings', new Group(static function (RouteCollectorProxy $group): void {
            $group->get('', GetProcessingsAction::class)->add(Authenticate::class);
            $group->post('/create', CreateProcessingAction::class)->add(Authenticate::class);
            $group->patch('/update/{id}', UpdateProcessingAction::class)->add(Authenticate::class);
            $group->delete('/delete/{id}', DeleteProcessingAction::class)->add(Authenticate::class);
            $group->get('/types', GetProcessingTypesAction::class)->add(Authenticate::class);
            $group->get('/{id}', GetProcessingByIdAction::class)->add(Authenticate::class);

            // ProcessingImage
            $group->post('/{id}/images', CreateProcessingImageAction::class)->add(Authenticate::class);
            $group->patch('/images/{imageId}', UpdateProcessingImageAction::class)->add(Authenticate::class);
            $group->delete('/images/{imageId}', DeleteProcessingImageAction::class)->add(Authenticate::class);
        }));

        $group->group('/clients', new Group(static function (RouteCollectorProxy $group): void {
            $group->get('', GetClientsAction::class)->add(Authenticate::class);
            $group->post('/create', CreateClientAction::class)->add(Authenticate::class);
            $group->get('/{id}', GetClientByIdAction::class)->add(Authenticate::class);
            $group->patch('/update/{id}', UpdateClientAction::class)->add(Authenticate::class);
            $group->delete('/delete/{id}', DeleteClientAction::class)->add(Authenticate::class);
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

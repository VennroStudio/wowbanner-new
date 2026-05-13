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
use App\Http\Action\v1\Client\GetClientDocsTypesAction;
use App\Http\Action\v1\Client\GetClientPhoneTypesAction;
use App\Http\Action\v1\Client\GetClientTypesAction;
use App\Http\Action\v1\Material\CreateMaterialAction;
use App\Http\Action\v1\Material\DeleteMaterialAction;
use App\Http\Action\v1\Material\GetMaterialAreaRangeTypesAction;
use App\Http\Action\v1\Material\GetMaterialByIdAction;
use App\Http\Action\v1\Material\GetMaterialDpiTypesAction;
use App\Http\Action\v1\Material\GetMaterialOptionSelectAction;
use App\Http\Action\v1\Material\GetMaterialOptionPricingTypesAction;
use App\Http\Action\v1\Material\GetMaterialOptionAction;
use App\Http\Action\v1\Material\GetMaterialPricingCutTypesAction;
use App\Http\Action\v1\Material\GetMaterialSelectAction;
use App\Http\Action\v1\Material\GetMaterialsAction;
use App\Http\Action\v1\Material\GetMaterialVariantTypesAction;
use App\Http\Action\v1\Material\MaterialImage\CreateMaterialImageAction;
use App\Http\Action\v1\Material\MaterialImage\DeleteMaterialImageAction;
use App\Http\Action\v1\Material\MaterialImage\UpdateMaterialImageAction;
use App\Http\Action\v1\Material\UpdateMaterialAction;
use App\Http\Action\v1\OpenApiAction;
use App\Http\Action\v1\Order\CreateOrderAction;
use App\Http\Action\v1\Order\DeleteOrderAction;
use App\Http\Action\v1\Order\DeleteOrderFileAction;
use App\Http\Action\v1\Order\GetOrderByIdAction;
use App\Http\Action\v1\Order\GetOrderDeliveryTypesAction;
use App\Http\Action\v1\Order\GetOrderFileDownloadAction;
use App\Http\Action\v1\Order\GetOrderPaymentOperationTypesAction;
use App\Http\Action\v1\Order\GetOrderPaymentTypesAction;
use App\Http\Action\v1\Order\GetOrderSectionTypesAction;
use App\Http\Action\v1\Order\GetOrderServiceTypesAction;
use App\Http\Action\v1\Order\GetOrderStatusTypesAction;
use App\Http\Action\v1\Order\GetOrderStorageTypesAction;
use App\Http\Action\v1\Order\GetOrdersAction;
use App\Http\Action\v1\Order\UpdateOrderAction;
use App\Http\Action\v1\Printing\CreatePrintingAction;
use App\Http\Action\v1\Printing\DeletePrintingAction;
use App\Http\Action\v1\Printing\GetPrintingByIdAction;
use App\Http\Action\v1\Printing\GetPrintingSelectAction;
use App\Http\Action\v1\Printing\GetPrintingsAction;
use App\Http\Action\v1\Printing\UpdatePrintingAction;
use App\Http\Action\v1\Processing\CreateProcessingAction;
use App\Http\Action\v1\Processing\DeleteProcessingAction;
use App\Http\Action\v1\Processing\GetProcessingByIdAction;
use App\Http\Action\v1\Processing\GetProcessingSelectAction;
use App\Http\Action\v1\Processing\GetProcessingsAction;
use App\Http\Action\v1\Processing\GetProcessingTypesAction;
use App\Http\Action\v1\Processing\ProcessingImage\CreateProcessingImageAction;
use App\Http\Action\v1\Processing\ProcessingImage\DeleteProcessingImageAction;
use App\Http\Action\v1\Processing\ProcessingImage\UpdateProcessingImageAction;
use App\Http\Action\v1\Processing\UpdateProcessingAction;
use App\Http\Action\v1\Product\CreateProductAction;
use App\Http\Action\v1\Product\DeleteProductAction;
use App\Http\Action\v1\Product\GetProductByIdAction;
use App\Http\Action\v1\Product\GetProductSelectAction;
use App\Http\Action\v1\Product\GetProductsAction;
use App\Http\Action\v1\Product\UpdateProductAction;
use App\Http\Action\v1\User\Admin\AdminUpdateUserAction;
use App\Http\Action\v1\User\CreateUserAction;

use App\Http\Action\v1\User\DeleteAvatarAction;
use App\Http\Action\v1\User\DeleteUserAction;
use App\Http\Action\v1\User\GetUserByIdAction;
use App\Http\Action\v1\User\GetUserRolesAction;
use App\Http\Action\v1\User\GetUserSelectAction;
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
            $group->get('/select', GetUserSelectAction::class)->add(Authenticate::class);
            $group->post('/create', CreateUserAction::class)->add(Authenticate::class);
            $group->get('/roles', GetUserRolesAction::class)->add(Authenticate::class);
            $group->get('/{id}', GetUserByIdAction::class)->add(Authenticate::class);
            $group->patch('/update/{id}', UserUpdateAction::class)->add(Authenticate::class);
            $group->delete('/delete/{id}', DeleteUserAction::class)->add(Authenticate::class);
            $group->post('/{id}/avatar', UploadAvatarAction::class)->add(Authenticate::class);
            $group->delete('/{id}/avatar', DeleteAvatarAction::class)->add(Authenticate::class);
        }));

        $group->group('/materials', new Group(static function (RouteCollectorProxy $group): void {
            $group->get('', GetMaterialsAction::class)->add(Authenticate::class);
            $group->get('/select', GetMaterialSelectAction::class)->add(Authenticate::class);
            $group->get('/option-pricing-types', GetMaterialOptionPricingTypesAction::class)->add(Authenticate::class);
            $group->get('/area-range-types', GetMaterialAreaRangeTypesAction::class)->add(Authenticate::class);
            $group->get('/dpi-types', GetMaterialDpiTypesAction::class)->add(Authenticate::class);
            $group->get('/variant-types', GetMaterialVariantTypesAction::class)->add(Authenticate::class);
            $group->get('/pricing-cut-types', GetMaterialPricingCutTypesAction::class)->add(Authenticate::class);
            $group->get('/{id}/options/select', GetMaterialOptionSelectAction::class)->add(Authenticate::class);
            $group->get('/{materialId}/option/{optionId}', GetMaterialOptionAction::class)->add(Authenticate::class);
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
            $group->get('/select', GetPrintingSelectAction::class)->add(Authenticate::class);
            $group->post('/create', CreatePrintingAction::class)->add(Authenticate::class);
            $group->patch('/update/{id}', UpdatePrintingAction::class)->add(Authenticate::class);
            $group->delete('/delete/{id}', DeletePrintingAction::class)->add(Authenticate::class);
            $group->get('/{id}', GetPrintingByIdAction::class)->add(Authenticate::class);
        }));

        $group->group('/processings', new Group(static function (RouteCollectorProxy $group): void {
            $group->get('', GetProcessingsAction::class)->add(Authenticate::class);
            $group->get('/select', GetProcessingSelectAction::class)->add(Authenticate::class);
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

        $group->group('/products', new Group(static function (RouteCollectorProxy $group): void {
            $group->get('', GetProductsAction::class)->add(Authenticate::class);
            $group->get('/select', GetProductSelectAction::class)->add(Authenticate::class);
            $group->post('/create', CreateProductAction::class)->add(Authenticate::class);
            $group->patch('/update/{id}', UpdateProductAction::class)->add(Authenticate::class);
            $group->delete('/delete/{id}', DeleteProductAction::class)->add(Authenticate::class);
            $group->get('/{id}', GetProductByIdAction::class)->add(Authenticate::class);
        }));

        $group->group('/orders', new Group(static function (RouteCollectorProxy $group): void {
            $group->get('', GetOrdersAction::class)->add(Authenticate::class);
            $group->get('/status-types', GetOrderStatusTypesAction::class)->add(Authenticate::class);
            $group->get('/storage-types', GetOrderStorageTypesAction::class)->add(Authenticate::class);
            $group->get('/delivery-types', GetOrderDeliveryTypesAction::class)->add(Authenticate::class);
            $group->get('/payment-operation-types', GetOrderPaymentOperationTypesAction::class)->add(Authenticate::class);
            $group->get('/payment-types', GetOrderPaymentTypesAction::class)->add(Authenticate::class);
            $group->get('/section-types', GetOrderSectionTypesAction::class)->add(Authenticate::class);
            $group->get('/service-types', GetOrderServiceTypesAction::class)->add(Authenticate::class);
            $group->post('/create', CreateOrderAction::class)->add(Authenticate::class);
            $group->post('/update/{id}', UpdateOrderAction::class)->add(Authenticate::class);
            $group->patch('/update/{id}', UpdateOrderAction::class)->add(Authenticate::class);
            $group->get('/files/{id}/download', GetOrderFileDownloadAction::class)->add(Authenticate::class);
            $group->delete('/files/{id}', DeleteOrderFileAction::class)->add(Authenticate::class);
            $group->delete('/delete/{id}', DeleteOrderAction::class)->add(Authenticate::class);
            $group->get('/{id}', GetOrderByIdAction::class)->add(Authenticate::class);
        }));

        $group->group('/clients', new Group(static function (RouteCollectorProxy $group): void {
            $group->get('', GetClientsAction::class)->add(Authenticate::class);
            $group->get('/types', GetClientTypesAction::class)->add(Authenticate::class);
            $group->get('/docs-types', GetClientDocsTypesAction::class)->add(Authenticate::class);
            $group->get('/phone-types', GetClientPhoneTypesAction::class)->add(Authenticate::class);
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

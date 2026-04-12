<?php

declare(strict_types=1);

namespace App\Modules\User\Service;

use App\Components\Exception\AccessDeniedException;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;
use App\Modules\User\Permission\UserPermission;

final readonly class UserPermissionService
{
    /**
     * @throws AccessDeniedException
     */
    public function checkOwner(int $currentUserId, int $userId, UserPermission $action): void
    {
        if ($currentUserId !== $userId) {
            throw new AccessDeniedException();
        }

        if (!\in_array($action, $this->getAllowedActionsForOwner(), true)) {
            throw new AccessDeniedException();
        }
    }

    /**
     * @throws AccessDeniedException
     */
    public function checkRole(UserRole $currentUserRole, UserPermission $action): void
    {
        if (!\in_array($currentUserRole, $this->getAllowedRolesForAction($action), true)) {
            throw new AccessDeniedException();
        }
    }

    /**
     * @throws AccessDeniedException
     */
    public function checkOwnerOrRole(int $currentUserId, int $userId, UserRole $currentUserRole, UserPermission $action): void
    {
        if ($currentUserId === $userId) {
            $this->checkOwner($currentUserId, $userId, $action);
            return;
        }

        $this->checkRole($currentUserRole, $action);
    }

    /**
     * @return list<UserPermission>
     */
    private function getAllowedActionsForOwner(): array
    {
        return [
            UserPermission::UPDATE,
        ];
    }

    /**
     * @return list<UserRole>
     */
    private function getAllowedRolesForAction(UserPermission $action): array
    {
        $adminRoles = [
            UserRole::ADMIN,
        ];

        return match ($action) {
            UserPermission::UPDATE,
            UserPermission::DELETE => $adminRoles,
        };
    }
}
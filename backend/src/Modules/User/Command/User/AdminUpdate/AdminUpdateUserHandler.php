<?php

declare(strict_types=1);

namespace App\Modules\User\Command\User\AdminUpdate;

use App\Components\Cacher\Cacher;
use App\Components\Exception\AccessDeniedException;
use App\Components\Exception\DomainExceptionModule;
use App\Components\Flusher\FlusherInterface;
use App\Modules\User\Command\User\AdminUpdate\AdminUpdateUserCommand;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;
use App\Modules\User\Entity\User\UserRepository;
use App\Modules\User\Permission\UserPermission;
use App\Modules\User\Query\User\FindByEmail\UserFindByEmailFetcher;
use App\Modules\User\Query\User\FindByEmail\UserFindByEmailQuery;
use App\Modules\User\Service\UserPermissionService;
use DateMalformedStringException;
use Doctrine\DBAL\Exception;

final readonly class AdminUpdateUserHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private FlusherInterface $flusher,
        private UserPermissionService $userPermissionService,
        private UserFindByEmailFetcher $userByEmailFetcher,
        private Cacher $cacher,
    ) {}

    /**
     * @throws DateMalformedStringException
     * @throws AccessDeniedException
     * @throws Exception
     */
    public function handle(AdminUpdateUserCommand $command): void
    {
        $user = $this->userRepository->getById($command->userId);

        $this->userPermissionService->checkRole(
            currentUserRole: UserRole::from($command->currentUserRole),
            action: UserPermission::UPDATE,
        );

        if ($command->userId === $command->currentUserId && $user->role->value !== $command->role) {
            throw new DomainExceptionModule(
                module: 'user',
                message: 'error.admin_cannot_change_own_role',
                code: 12
            );
        }

        $email = mb_strtolower($command->email);
        $this->assertEmailNotTakenByOther($email, $command->userId);

        $user->editByAdmin(
            lastName: $command->lastName,
            firstName: $command->firstName,
            email: $email,
            role: UserRole::from($command->role),
        );

        $this->cacher->delete('user_identity_' . $command->userId);

        $this->flusher->flush();
    }

    /**
     * @throws Exception
     */
    private function assertEmailNotTakenByOther(string $email, int $userId): void
    {
        $user = $this->userByEmailFetcher->fetchAny(new UserFindByEmailQuery($email));

        if ($user !== null && (int)$user->id !== $userId) {
            throw new DomainExceptionModule(
                module: 'user',
                message: 'error.email_already_registered',
                code: 8
            );
        }
    }
}

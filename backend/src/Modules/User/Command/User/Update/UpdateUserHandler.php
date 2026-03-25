<?php

declare(strict_types=1);

namespace App\Modules\User\Command\User\Update;

use App\Components\Cacher\Cacher;
use App\Components\Exception\AccessDeniedException;
use App\Components\Exception\DomainExceptionModule;
use App\Components\Flusher\FlusherInterface;
use App\Modules\User\Entity\User\Fields\Enums\UserRole;
use App\Modules\User\Entity\User\UserRepository;
use App\Modules\User\Permission\UserPermission;
use App\Modules\User\Query\User\FindByEmail\UserFindByEmailFetcher;
use App\Modules\User\Query\User\FindByEmail\UserFindByEmailQuery;
use App\Modules\User\Service\UserPermissionService;
use DateMalformedStringException;
use Doctrine\DBAL\Exception;

final readonly class UpdateUserHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private FlusherInterface $flusher,
        private UserFindByEmailFetcher $userFindByEmailFetcher,
        private UserPermissionService $userPermissionService,
        private Cacher $cacher,
    ) {}

    /**
     * @throws DateMalformedStringException
     * @throws Exception
     * @throws AccessDeniedException
     */
    public function handle(UpdateUserCommand $command): void
    {
        $user = $this->userRepository->getById($command->userId);

        $this->userPermissionService->check(
            currentUserId: $command->currentUserId,
            currentUserRole: UserRole::from($command->currentUserRole),
            userId: $command->userId,
            action: UserPermission::UPDATE,
        );

        $email = mb_strtolower($command->email);
        $this->assertEmailNotTakenByOther($email, $command->userId);

        $user->edit(
            $command->lastName,
            $command->firstName,
            $email,
        );

        $this->cacher->delete('user_identity_' . $command->userId);

        $this->flusher->flush();
    }

    /**
     * @throws Exception
     */
    private function assertEmailNotTakenByOther(string $email, int $currentUserId): void
    {
        $existing = $this->userFindByEmailFetcher->fetchAny(new UserFindByEmailQuery($email));
        if ($existing !== null && $existing->id !== $currentUserId) {
            throw new DomainExceptionModule(
                module: 'user',
                message: 'error.email_already_registered',
                code: 8
            );
        }
    }
}

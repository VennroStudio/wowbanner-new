<?php

declare(strict_types=1);

namespace App\Http\Unifier\User;

use App\Components\Http\Unifier\UnifierInterface;
use App\Components\Storage\S3Transformer;
use App\Modules\User\Query\UserPhone\GetByUserIds\UserPhoneFetcher;
use App\Modules\User\Query\UserProfile\GetByUserIds\UserProfileFetcher;
use App\Modules\User\ReadModel\User\Interface\UserModelInterface;
use App\Modules\User\ReadModel\UserPhone\Interface\UserPhoneViewInterface;
use App\Modules\User\ReadModel\UserProfile\Interface\UserProfileViewInterface;
use Override;

final readonly class UserUnifier implements UnifierInterface
{
    public function __construct(
        private S3Transformer $s3Transformer,
        private UserProfileFetcher $profileFetcher,
        private UserPhoneFetcher $phoneFetcher,
    ) {}

    #[Override]
    public function unifyOne(?int $userId, ?object $item): array
    {
        if ($item === null) {
            return [];
        }

        return $this->unify($userId, [$item])[0] ?? [];
    }

    /**
     * @param list<UserModelInterface> $items
     * @return list<array<string,mixed>>
     */
    #[Override]
    public function unify(?int $userId, array $items): array
    {
        if ($items === []) {
            return [];
        }

        $ids = array_map(static fn(UserModelInterface $item): int => $item->getId(), $items);

        $profiles = $this->groupProfiles($this->profileFetcher->fetchByUserIds($ids));
        $phones   = $this->groupPhones($this->phoneFetcher->fetchByUserIds($ids));

        return array_map(
            fn(UserModelInterface $item): array => $this->map($item, $profiles, $phones),
            $items
        );
    }

    #[Override]
    public function map(object $item, array $profiles = [], array $phones = []): array
    {
        /** @var UserModelInterface $item */
        $data = $item->toArray();

        $data['avatar']  = $this->s3Transformer->buildUrl($data['avatar']);
        $data['profile'] = $profiles[$item->getId()] ?? null;
        $data['phones']  = $phones[$item->getId()] ?? [];

        return $data;
    }

    /**
     * @param list<UserProfileViewInterface> $profiles
     * @return array<int, array<string,mixed>>
     */
    private function groupProfiles(array $profiles): array
    {
        $grouped = [];

        foreach ($profiles as $profile) {
            $grouped[$profile->userId] = $profile->toArray();
        }

        return $grouped;
    }

    /**
     * @param list<UserPhoneViewInterface> $phones
     * @return array<int, list<array<string,mixed>>>
     */
    private function groupPhones(array $phones): array
    {
        $grouped = [];

        foreach ($phones as $phone) {
            $grouped[$phone->userId][] = $phone->toArray();
        }

        return $grouped;
    }
}
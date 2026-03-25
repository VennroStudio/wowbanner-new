import React from 'react';
import { User as UserIcon } from 'lucide-react';

interface UserAvatarProps {
    avatarUrl?: string | null;
}

export const UserAvatar: React.FC<UserAvatarProps> = ({ avatarUrl }) => {
    if (avatarUrl) {
        return <img src={avatarUrl} alt="Аватар пользователя" className="w-full h-full object-cover rounded-full" />;
    }
    return <UserIcon size={32} />;
};
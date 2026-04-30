import React from 'react';
import { User as UserIcon } from 'lucide-react';

interface UserAvatarProps {
  avatarUrl?: string | null;
}

export const UserAvatar: React.FC<UserAvatarProps> = ({ avatarUrl }) => {
  if (avatarUrl) {
    return (
      <img 
        src={avatarUrl} 
        alt="Аватар" 
        className="w-8 h-8 rounded-full object-cover border border-slate-600" 
      />
    );
  }
  
  return (
    <div className="w-8 h-8 rounded-full border border-slate-600 bg-slate-700 flex items-center justify-center text-slate-300">
      <UserIcon size={18} />
    </div>
  );
};

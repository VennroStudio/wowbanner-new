import React from 'react';
import { UserAvatar } from '../UserAvatar';

interface Props {
  avatarUrl?: string | null;
  displayName: string;
  onClick: () => void;
}

export const UserDropdownTrigger: React.FC<Props> = ({ avatarUrl, displayName, onClick }) => (
  <div
    onClick={onClick}
    className="flex items-center gap-2 cursor-pointer hover:bg-slate-700 px-2 py-1 rounded-md transition-colors border border-transparent hover:border-slate-600"
  >
    <UserAvatar avatarUrl={avatarUrl} />
    <div className="text-sm font-medium hidden sm:block">{displayName}</div>
  </div>
);

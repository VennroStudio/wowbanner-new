import React from 'react';
import type { LucideIcon } from 'lucide-react';

interface Props {
  icon: LucideIcon;
  label: string;
  onClick?: () => void;
  variant?: 'default' | 'danger';
}

export const UserDropdownMenuItem: React.FC<Props> = ({ icon: Icon, label, onClick, variant = 'default' }) => {
  const styles = variant === 'danger'
    ? 'text-red-600 hover:bg-red-50 font-medium'
    : 'text-slate-700 hover:bg-slate-50';

  return (
    <button
      onClick={onClick}
      className={`w-full text-left px-4 py-2 text-sm flex items-center gap-3 cursor-pointer transition-colors ${styles}`}
    >
      <Icon size={16} className={variant === 'danger' ? 'text-red-500' : 'text-slate-400'} />
      {label}
    </button>
  );
};

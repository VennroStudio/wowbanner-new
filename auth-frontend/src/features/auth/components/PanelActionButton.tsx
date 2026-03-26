import React from 'react';
import type { LucideIcon } from 'lucide-react';

interface PanelActionButtonProps {
  icon?: LucideIcon;
  label: string;
  onClick?: () => void;
  disabled?: boolean;
}

export const PanelActionButton: React.FC<PanelActionButtonProps> = ({
  icon: Icon,
  label,
  onClick,
  disabled
}) => {
  if (disabled) {
    return (
      <button
        disabled
        className="group flex flex-col items-center justify-center p-4 bg-slate-50 border border-slate-100 rounded-2xl opacity-60 cursor-not-allowed transition-all duration-300"
      >
        <div className="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center mb-2 transition-all duration-300">
          <div className="w-5 h-5 rounded-md border-2 border-slate-300 border-dashed" />
        </div>
        <span className="text-xs font-medium text-slate-400">{label}</span>
      </button>
    );
  }

  return (
    <button
      onClick={onClick}
      className="group flex flex-col items-center justify-center p-4 bg-slate-50 border border-slate-100 rounded-2xl hover:bg-white hover:border-blue-200 hover:shadow-md hover:shadow-blue-500/5 transition-all duration-300 cursor-pointer"
    >
      <div className="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center mb-2 group-hover:scale-110 group-hover:bg-blue-50 group-hover:border-blue-100 transition-all duration-300">
        {Icon && <Icon size={20} className="text-slate-500 group-hover:text-blue-600" />}
      </div>
      <span className="text-xs font-medium text-slate-600 group-hover:text-slate-900">
        {label}
      </span>
    </button>
  );
};

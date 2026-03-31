import React from 'react';
import type { LucideIcon } from 'lucide-react';

const accentConfig = {
  blue: 'bg-blue-100 text-blue-600',
  emerald: 'bg-emerald-100 text-emerald-600',
  red: 'bg-red-100 text-red-600',
  amber: 'bg-amber-100 text-amber-500',
  slate: 'bg-slate-50 text-slate-400',
} as const;

export interface PageCardHeaderProps {
  icon?: LucideIcon;
  logo?: { src: string; alt: string };
  title?: string;
  description?: React.ReactNode;
  accent?: keyof typeof accentConfig;
  className?: string;
}

export const PageCardHeader: React.FC<PageCardHeaderProps> = ({
  icon: Icon,
  logo,
  title,
  description,
  accent = 'blue',
  className,
}) => {
  return (
    <div className={['text-center mb-8', className].filter(Boolean).join(' ')}>
      {logo ? (
        <div className="bg-blue-50 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4 overflow-hidden p-2">
          <img src={logo.src} alt={logo.alt} className="w-full h-auto object-contain" />
        </div>
      ) : (
        Icon && (
          <div
            className={`w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4 ${accentConfig[accent]}`}
          >
            <Icon size={24} strokeWidth={2} />
          </div>
        )
      )}

      {title != null && title !== '' && (
        <h1 className="text-2xl font-bold text-slate-800">{title}</h1>
      )}
      {description != null && (
        <div
          className={
            title != null && title !== ''
              ? 'text-slate-500 text-sm mt-1 leading-relaxed'
              : 'text-slate-500 text-sm leading-relaxed'
          }
        >
          {description}
        </div>
      )}
    </div>
  );
};

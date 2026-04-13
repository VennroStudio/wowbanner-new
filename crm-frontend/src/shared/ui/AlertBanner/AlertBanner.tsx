import type { ReactNode } from 'react';

export type AlertBannerVariant = 'success' | 'error' | 'info';

const variantClass: Record<AlertBannerVariant, string> = {
  success: 'bg-emerald-50 border-emerald-100 text-emerald-800',
  error: 'bg-red-50 border-red-100 text-red-800',
  info: 'bg-slate-50 border-slate-200 text-slate-700',
};

interface AlertBannerProps {
  children: ReactNode;
  variant?: AlertBannerVariant;
  className?: string;
}

export const AlertBanner = ({
  children,
  variant = 'info',
  className = '',
}: AlertBannerProps) => (
  <div
    role="status"
    className={`rounded-lg border text-sm px-4 py-2 ${variantClass[variant]} ${className}`.trim()}
  >
    {children}
  </div>
);

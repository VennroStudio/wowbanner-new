import React from 'react';
import { Loader2 } from 'lucide-react';

interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  isLoading?: boolean;
  variant?: 'primary' | 'secondary';
  children: React.ReactNode;
}

export const Button: React.FC<ButtonProps> = ({ children, isLoading, variant = 'primary', ...props }) => {
  const styles =
    variant === 'secondary'
      ? 'bg-slate-100 hover:bg-slate-200 text-slate-800 shadow-none'
      : 'bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white shadow-lg shadow-blue-600/20';

  return (
    <button
      disabled={isLoading}
      className={`w-full font-medium py-2.5 rounded-xl transition-all flex items-center justify-center gap-2 ${styles}`}
      {...props}
    >
      {isLoading ? <Loader2 className="animate-spin" size={20} /> : children}
    </button>
  );
};

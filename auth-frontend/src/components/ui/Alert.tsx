import React from 'react';
import { AlertCircle, CheckCircle2 } from 'lucide-react';

interface AlertProps {
  message: string;
  type?: 'error' | 'success';
}

export const Alert: React.FC<AlertProps> = ({ message, type = 'error' }) => {
  if (!message) return null;

  return (
    <div
      className={`p-3 rounded-xl mb-4 flex items-center gap-2 text-sm ${
        type === 'error'
          ? 'bg-red-50 text-red-600 border border-red-100'
          : 'bg-emerald-50 text-emerald-600 border border-emerald-100'
      }`}
    >
      {type === 'error' ? <AlertCircle size={18} /> : <CheckCircle2 size={18} />}
      {message}
    </div>
  );
};

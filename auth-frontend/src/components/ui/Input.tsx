import React from 'react';

interface InputProps extends React.InputHTMLAttributes<HTMLInputElement> {
  label: string;
  icon?: React.ComponentType<{ className?: string; size?: number }>;
  error?: string;
}

export const Input: React.FC<InputProps> = ({ label, icon: Icon, error, ...props }) => (
  <div className="mb-4">
    <label className="block text-sm font-medium text-slate-700 mb-1">{label}</label>
    <div className="relative">
      {Icon && <Icon className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" size={18} />}
      <input
        className={`w-full bg-slate-50 border ${error ? 'border-red-500 focus:ring-red-500' : 'border-slate-200 focus:ring-blue-500'} rounded-xl px-4 py-2.5 ${Icon ? 'pl-10' : ''} outline-none focus:ring-2 transition-all`}
        {...props}
      />
    </div>
    {error && <p className="text-red-500 text-xs mt-1">{error}</p>}
  </div>
);

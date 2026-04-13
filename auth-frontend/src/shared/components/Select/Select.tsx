import React from 'react';

interface SelectOption {
  value: string | number;
  label: string;
}

interface SelectProps extends React.SelectHTMLAttributes<HTMLSelectElement> {
  label: string;
  icon?: React.ComponentType<{ className?: string; size?: number }>;
  error?: string;
  options: SelectOption[];
  placeholder?: string;
}

export const Select: React.FC<SelectProps> = ({ 
  label, 
  icon: Icon, 
  error, 
  options, 
  placeholder = 'Выберите...', 
  ...props 
}) => (
  <div className="mb-4">
    <label className="block text-sm font-medium text-slate-700 mb-1">
      {label} {props.required && <span className="text-red-500">*</span>}
    </label>
    <div className="relative">
      {Icon && <Icon className="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" size={18} />}
      <select
        className={`w-full bg-slate-50 border ${error ? 'border-red-500 focus:ring-red-500' : 'border-slate-200 focus:ring-blue-500'} rounded-xl px-4 py-2.5 ${Icon ? 'pl-10' : ''} outline-none focus:ring-2 transition-all cursor-pointer disabled:opacity-50`}
        {...props}
      >
        <option value="" disabled hidden>{placeholder}</option>
        {options.map((opt, idx) => (
          <option key={`opt-${opt.value}-${idx}`} value={opt.value}>
            {opt.label}
          </option>
        ))}
      </select>
    </div>
    {error && <p className="text-red-500 text-xs mt-1">{error}</p>}
  </div>
);

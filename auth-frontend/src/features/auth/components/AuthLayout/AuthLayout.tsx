import React from 'react';
import { BackButton } from '@/shared/components';

interface AuthLayoutProps {
  children: React.ReactNode;
  title?: string;
  onBack?: () => void;
  showLogo?: boolean;
}

export const AuthLayout: React.FC<AuthLayoutProps> = ({ 
  children, 
  title, 
  onBack, 
  showLogo = true 
}) => {
  return (
    <div className="w-full max-w-md bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/50">
      {onBack && <BackButton onClick={onBack} />}
      
      {showLogo && (
        <div className="text-center mb-8">
          <div className="bg-blue-50 w-32 h-32 rounded-full flex items-center justify-center mx-auto mb-4 overflow-hidden p-6">
            <img 
              src="https://storage.vennro.ru/vs-project/assets/logo-wowbanner.png" 
              alt="Logo" 
              className="w-full h-auto object-contain"
            />
          </div>
        </div>
      )}

      {title && <h1 className="text-2xl font-bold text-slate-800 mb-6 text-center">{title}</h1>}

      {children}
    </div>
  );
};

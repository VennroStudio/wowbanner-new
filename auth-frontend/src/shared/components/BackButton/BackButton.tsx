import React from 'react';
import { ChevronLeft } from 'lucide-react';

interface BackButtonProps {
  onClick: () => void;
}

export const BackButton: React.FC<BackButtonProps> = ({ onClick }) => (
  <button
    onClick={onClick}
    className="flex items-center gap-1 text-sm text-slate-500 hover:text-slate-700 mb-6 transition-colors"
  >
    <ChevronLeft size={16} /> Назад
  </button>
);

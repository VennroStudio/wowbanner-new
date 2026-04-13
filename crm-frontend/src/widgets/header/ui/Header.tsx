import React from 'react';
import { Menu, Bell } from 'lucide-react';

interface HeaderProps {
  toggleSidebar: () => void;
}

export const Header = ({ toggleSidebar }: HeaderProps) => {
  return (
    <header className="sticky top-0 z-50 w-full h-14 bg-[#1e293b] text-white flex items-center justify-between px-4 shadow-md shrink-0">
      <div className="flex items-center gap-4">
        <button 
          onClick={toggleSidebar} 
          className="p-1.5 hover:bg-slate-700 rounded-md transition-colors cursor-pointer"
        >
          <Menu size={20} />
        </button>
        <div className="font-bold text-xl tracking-tight">WowBanner <span className="text-blue-400">CRM</span></div>
      </div>
      
      <div className="flex items-center gap-4">
        <button className="p-1.5 hover:bg-slate-700 rounded-md transition-colors cursor-pointer">
          <Bell size={20} />
        </button>
        <div className="flex items-center gap-2 cursor-pointer hover:bg-slate-700 px-2 py-1 rounded-md transition-colors border border-transparent hover:border-slate-600">
          <img 
            src="https://ui-avatars.com/api/?name=Виктор&background=3b82f6&color=fff" 
            alt="Avatar" 
            className="w-8 h-8 rounded-full border border-slate-600"
          />
          <div className="text-sm font-medium hidden sm:block">Виктор</div>
        </div>
      </div>
    </header>
  );
};

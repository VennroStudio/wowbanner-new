import React from 'react';
import { Menu, Bell } from 'lucide-react';
import { UserDropdown } from '@/features/user';

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
        <UserDropdown />
      </div>
    </header>
  );
};

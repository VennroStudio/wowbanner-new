import React from 'react';
import { Home, Users, Briefcase, BarChart2, Settings, MessageSquare } from 'lucide-react';

interface SidebarProps {
  isOpen: boolean;
}

export const Sidebar = ({ isOpen }: SidebarProps) => {
  const menuItems = [
    { icon: <Home size={20} />, label: 'Главная', active: false },
    { icon: <Briefcase size={20} />, label: 'Заказы', active: true },
    { icon: <Users size={20} />, label: 'Клиенты', active: false },
    { icon: <BarChart2 size={20} />, label: 'Аналитика', active: false },
    { icon: <MessageSquare size={20} />, label: 'Чаты', active: false },
    { icon: <Settings size={20} />, label: 'Настройки', active: false },
  ];

  return (
    <aside 
      className={`bg-[#0f172a] text-slate-300 flex flex-col transition-all duration-300 ease-in-out border-r border-slate-800 shrink-0 ${
        isOpen ? 'w-64' : 'w-16'
      }`}
    >
      <div className="flex flex-col gap-1 p-2 mt-2">
        {menuItems.map((item, index) => (
          <button 
            key={index}
            className={`flex items-center gap-3 p-3 rounded-lg transition-colors overflow-hidden whitespace-nowrap cursor-pointer ${
              item.active 
                ? 'bg-blue-600 text-white shadow-md' 
                : 'hover:bg-slate-800 hover:text-white'
            }`}
            title={!isOpen ? item.label : undefined}
          >
            <div className="shrink-0">{item.icon}</div>
            {isOpen && <span className="text-sm font-medium">{item.label}</span>}
          </button>
        ))}
      </div>
    </aside>
  );
};

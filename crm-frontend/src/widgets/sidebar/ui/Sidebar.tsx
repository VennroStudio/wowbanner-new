import { NavLink } from 'react-router-dom';
import type { LucideIcon } from 'lucide-react';
import { Home, Users, Briefcase, BarChart2, Settings, MessageSquare, Layers } from 'lucide-react';
import { ROUTES } from '@/shared/constants';

interface SidebarProps {
  isOpen: boolean;
}

type MenuLink = { to: string; icon: LucideIcon; label: string };
type MenuDisabled = { disabled: true; icon: LucideIcon; label: string };
type MenuItem = MenuLink | MenuDisabled;

const menuItems: MenuItem[] = [
  { to: ROUTES.HOME, icon: Home, label: 'Главная' },
  { to: ROUTES.CLIENTS, icon: Users, label: 'Клиенты' },
  { to: ROUTES.MATERIALS, icon: Layers, label: 'Материалы' },
  { disabled: true, icon: Briefcase, label: 'Заказы' },
  { disabled: true, icon: BarChart2, label: 'Аналитика' },
  { disabled: true, icon: MessageSquare, label: 'Чаты' },
  { disabled: true, icon: Settings, label: 'Настройки' },
];

const linkClass = (isActive: boolean) =>
  `flex items-center gap-3 p-3 rounded-lg transition-colors overflow-hidden whitespace-nowrap w-full text-left ${
    isActive ? 'bg-blue-600 text-white shadow-md' : 'hover:bg-slate-800 hover:text-white text-slate-300'
  }`;

const disabledClass =
  'flex items-center gap-3 p-3 rounded-lg overflow-hidden whitespace-nowrap w-full text-left opacity-40 cursor-not-allowed text-slate-500';

export const Sidebar = ({ isOpen }: SidebarProps) => {
  return (
    <aside
      className={`bg-[#0f172a] text-slate-300 flex flex-col transition-all duration-300 ease-in-out border-r border-slate-800 shrink-0 ${
        isOpen ? 'w-64' : 'w-16'
      }`}
    >
      <div className="flex flex-col gap-1 p-2 mt-2">
        {menuItems.map((item) => {
          const Icon = item.icon;
          if ('to' in item) {
            return (
              <NavLink
                key={item.to}
                to={item.to}
                end={item.to === ROUTES.HOME}
                className={({ isActive }) => linkClass(isActive)}
                title={!isOpen ? item.label : undefined}
              >
                <Icon size={20} className="shrink-0" />
                {isOpen && <span className="text-sm font-medium">{item.label}</span>}
              </NavLink>
            );
          }
          return (
            <button
              key={item.label}
              type="button"
              disabled
              className={disabledClass}
              title={!isOpen ? item.label : undefined}
            >
              <Icon size={20} className="shrink-0" />
              {isOpen && <span className="text-sm font-medium">{item.label}</span>}
            </button>
          );
        })}
      </div>
    </aside>
  );
};

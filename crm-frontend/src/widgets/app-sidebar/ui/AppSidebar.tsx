import { NavLink } from 'react-router-dom';
import { ROUTES } from '@/shared/routing/routes';
import { navigationItems } from '../model/navigation';

interface AppSidebarProps {
  isOpen: boolean;
}

const linkClass = (isActive: boolean) =>
  `flex items-center gap-3 p-3 rounded-lg transition-colors overflow-hidden whitespace-nowrap w-full text-left ${
    isActive ? 'bg-blue-600 text-white shadow-md' : 'hover:bg-slate-800 hover:text-white text-slate-300'
  }`;

const disabledClass =
  'flex items-center gap-3 p-3 rounded-lg overflow-hidden whitespace-nowrap w-full text-left opacity-40 cursor-not-allowed text-slate-500';

export const AppSidebar = ({ isOpen }: AppSidebarProps) => {
  return (
    <aside
      className={`bg-[#0f172a] text-slate-300 flex flex-col transition-all duration-300 ease-in-out border-r border-slate-800 shrink-0 ${
        isOpen ? 'w-64' : 'w-16'
      }`}
    >
      <div className="flex flex-col gap-1 p-2 mt-2">
        {navigationItems.map((item) => {
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

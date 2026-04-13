import React from 'react';
import { UserRoundPlus, Users } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import { PanelActionButton } from '../PanelActionButton';
import { ROUTES } from '@/shared/constants';

export const AdminPanel: React.FC = () => {
  const navigate = useNavigate();
  return (
    <div className="mb-8 text-left">
      <h3 className="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4 px-1">
        Админ панель
      </h3>
      <div className="grid grid-cols-2 gap-3">
        <PanelActionButton
          icon={UserRoundPlus}
          label="Пригласить"
          onClick={() => navigate(ROUTES.REGISTER)}
        />

        <PanelActionButton
          icon={Users}
          label="Управление"
          onClick={() => navigate(ROUTES.ADMIN_USERS)}
        />
      </div>
    </div>
  );
};

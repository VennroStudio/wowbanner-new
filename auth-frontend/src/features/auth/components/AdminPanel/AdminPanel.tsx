import React from 'react';
import { UserRoundPlus } from 'lucide-react';
import { AdminActionButton } from './AdminActionButton';
import { useRouter } from '@/shared/hooks';

export const AdminPanel: React.FC = () => {
  const { navigate } = useRouter();
  return (
    <div className="mb-8 text-left">
      <h3 className="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4 px-1">
        Панель управления
      </h3>
      <div className="grid grid-cols-2 gap-3">
        <AdminActionButton
          icon={UserRoundPlus}
          label="Пригласить"
          onClick={() => navigate('/register')}
        />

        <AdminActionButton
          label="Управление"
          disabled
        />
      </div>
    </div>
  );
};

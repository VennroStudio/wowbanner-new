import React from 'react';
import { AdminUsersTable } from '@/features/user/components/AdminUsersTable';
import { BackButton } from '@/shared/components';
import { ROUTES } from '@/shared/constants';
import { useRouter } from '@/shared/hooks';

export const AdminUsersPage: React.FC = () => {
  const { navigate } = useRouter();

  return (
    <div className="w-full max-w-4xl bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/50 text-left">
      <BackButton onClick={() => navigate(ROUTES.HOME)} />
      <h1 className="text-2xl font-bold text-slate-800 mb-1">Управление пользователями</h1>
      <p className="text-slate-500 text-sm mb-6">Список пользователей, роли и статусы</p>
      <AdminUsersTable />
    </div>
  );
};

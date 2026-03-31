import React from 'react';
import { AdminUsersTable } from '@/features/user/components/AdminUsersTable';
import { BackButton, PageCard } from '@/shared/components';
import { ROUTES } from '@/shared/constants';
import { useRouter } from '@/shared/hooks';

export const AdminUsersPage: React.FC = () => {
  const { navigate } = useRouter();

  return (
    <PageCard>
      <BackButton onClick={() => navigate(ROUTES.HOME)} />
      <h1 className="text-2xl font-bold text-slate-800 mb-1">Управление пользователями</h1>
      <p className="text-slate-500 text-sm mb-6">Список пользователей, роли и статусы</p>
      <AdminUsersTable />
    </PageCard>
  );
};

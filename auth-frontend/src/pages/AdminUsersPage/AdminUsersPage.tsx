import React from 'react';
import { Users } from 'lucide-react';
import { AdminUsersTable } from '@/features/user/components/AdminUsersTable';
import { BackButton, PageCard, PageCardHeader } from '@/shared/components';
import { useNavigate } from 'react-router-dom';
import { ROUTES } from '@/shared/constants';

export const AdminUsersPage: React.FC = () => {
  const navigate = useNavigate();

  return (
    <PageCard>
      <BackButton onClick={() => navigate(ROUTES.HOME)} />
      <PageCardHeader
        icon={Users}
        title="Управление пользователями"
        description="Список пользователей, роли и статусы"
      />
      <AdminUsersTable />
    </PageCard>
  );
};

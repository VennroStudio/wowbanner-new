import React from 'react';
import { Users } from 'lucide-react';
import { AdminUsersTable } from '@/features/user/components/AdminUsersTable';
import { BackButton, PageCard, PageCardHeader } from '@/shared/components';
import { ROUTES } from '@/shared/constants';
import { useRouter } from '@/shared/hooks';

export const AdminUsersPage: React.FC = () => {
  const { navigate } = useRouter();

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

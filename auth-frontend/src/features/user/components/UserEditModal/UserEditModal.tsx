import React, { useState } from 'react';
import type { User } from '@/entities/user';
import { useAdminUpdateUserCommand, useRolesQuery } from '@/entities/user';
import { Button, Input, SmallModal, Select } from '@/shared/components';

interface UserEditModalProps {
  user: User | null;
  onClose: () => void;
}

export const UserEditModal: React.FC<UserEditModalProps> = ({ user, onClose }) => {
  return (
    <SmallModal
      isOpen={!!user}
      onClose={onClose}
      title="Редактировать пользователя"
      titleId="edit-user-modal-title"
    >
      {user ? <UserEditForm key={user.id} user={user} onClose={onClose} /> : null}
    </SmallModal>
  );
};

interface UserEditFormProps {
  user: User;
  onClose: () => void;
}

const UserEditForm: React.FC<UserEditFormProps> = ({ user, onClose }) => {
  const updateUser = useAdminUpdateUserCommand();
  const { data: roles = [], isLoading: isRolesLoading } = useRolesQuery();

  const [firstName, setFirstName] = useState(user.first_name);
  const [lastName, setLastName] = useState(user.last_name);
  const [email, setEmail] = useState(user.email);
  const [role, setRole] = useState<number | ''>(user.role?.id ?? '');

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (role === '') return;
    await updateUser.mutateAsync({
      id: user.id,
      data: { firstName, lastName, email, role: Number(role) },
    });
    onClose();
  };

  return (
    <form onSubmit={handleSubmit}>
      <Input
        label="Имя"
        name="firstName"
        value={firstName}
        onChange={(e) => setFirstName(e.target.value)}
        required
        autoComplete="given-name"
      />
      <Input
        label="Фамилия"
        name="lastName"
        value={lastName}
        onChange={(e) => setLastName(e.target.value)}
        required
        autoComplete="family-name"
      />
      <Input
        label="Email"
        name="email"
        type="email"
        value={email}
        onChange={(e) => setEmail(e.target.value)}
        required
        autoComplete="email"
      />
      <Select
        label="Роль"
        options={roles}
        placeholder="Выберите роль..."
        value={role}
        onChange={(e) => {
          const nextRole = e.target.value;
          setRole(nextRole === '' ? '' : Number(nextRole));
        }}
        required
        disabled={isRolesLoading}
      />
      <div className="flex gap-3 mt-2">
        <Button
          type="button"
          variant="secondary"
          className="flex-1"
          onClick={onClose}
          disabled={updateUser.isPending}
        >
          Отмена
        </Button>
        <Button type="submit" className="flex-1" isLoading={updateUser.isPending}>
          Сохранить
        </Button>
      </div>
    </form>
  );
};

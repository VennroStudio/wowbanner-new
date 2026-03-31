import React, { useEffect, useState } from 'react';
import type { User } from '@/entities/user';
import { useUpdateUserCommand } from '@/entities/user';
import { Button, Input, SmallModal } from '@/shared/components';

interface UserEditModalProps {
  user: User | null;
  onClose: () => void;
}

export const UserEditModal: React.FC<UserEditModalProps> = ({ user, onClose }) => {
  const updateUser = useUpdateUserCommand();
  const [firstName, setFirstName] = useState('');
  const [lastName, setLastName] = useState('');
  const [email, setEmail] = useState('');

  useEffect(() => {
    if (user) {
      setFirstName(user.first_name);
      setLastName(user.last_name);
      setEmail(user.email);
    }
  }, [user]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!user) return;
    await updateUser.mutateAsync({
      id: user.id,
      data: { firstName, lastName, email },
    });
    onClose();
  };

  return (
    <SmallModal
      isOpen={!!user}
      onClose={onClose}
      title="Редактировать пользователя"
      titleId="edit-user-modal-title"
    >
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
    </SmallModal>
  );
};

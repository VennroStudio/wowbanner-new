import React, { useEffect, useState } from 'react';
import type { User } from '@/entities/user';
import { useUpdateUserCommand } from '@/entities/user';
import { Button, Input } from '@/shared/components';

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

  if (!user) return null;

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    await updateUser.mutateAsync({
      id: user.id,
      data: { firstName, lastName, email },
    });
    onClose();
  };

  return (
    <div
      className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm"
      role="dialog"
      aria-modal="true"
      aria-labelledby="edit-user-title"
      onClick={onClose}
    >
      <div
        className="w-full max-w-md bg-white rounded-2xl shadow-xl p-6 max-h-[90vh] overflow-y-auto"
        onClick={(e) => e.stopPropagation()}
      >
        <h2 id="edit-user-title" className="text-lg font-semibold text-slate-900 mb-4">
          Редактировать пользователя
        </h2>
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
      </div>
    </div>
  );
};

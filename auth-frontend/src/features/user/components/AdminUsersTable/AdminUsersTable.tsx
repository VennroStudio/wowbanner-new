import React, { useState } from 'react';
import { Pencil, Trash2 } from 'lucide-react';
import type { User } from '@/entities/user';
import { useDeleteUserCommand, useUsersQuery } from '@/entities/user';
import { UserEditModal } from '../UserEditModal';

export const AdminUsersTable: React.FC = () => {
  const { data, isLoading, isError, error } = useUsersQuery({ page: 1, perPage: 20 });
  const deleteUser = useDeleteUserCommand();
  const [editingUser, setEditingUser] = useState<User | null>(null);

  const handleDelete = (u: User) => {
    if (!window.confirm(`Удалить пользователя ${u.first_name} ${u.last_name}?`)) return;
    deleteUser.mutate(u.id);
  };

  if (isLoading) {
    return (
      <div className="flex justify-center py-12 text-slate-500 text-sm">Загрузка…</div>
    );
  }

  if (isError) {
    return (
      <div className="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
        {error instanceof Error ? error.message : 'Не удалось загрузить список пользователей'}
      </div>
    );
  }

  const items = data?.items ?? [];

  return (
    <>
      <div className="overflow-x-auto rounded-2xl border border-slate-100 bg-slate-50/40">
        <table className="w-full text-left text-sm">
          <thead>
            <tr className="border-b border-slate-200/80 bg-white/80">
              <th className="px-4 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wide">Имя</th>
              <th className="px-4 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wide">Роль</th>
              <th className="px-4 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wide">Статус</th>
              <th className="px-4 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wide text-right w-28">
                Действия
              </th>
            </tr>
          </thead>
          <tbody>
            {items.length === 0 ? (
              <tr>
                <td colSpan={4} className="px-4 py-8 text-center text-slate-500">
                  Пользователи не найдены
                </td>
              </tr>
            ) : (
              items.map((u) => (
                <tr key={u.id} className="border-b border-slate-100 last:border-0 bg-white hover:bg-slate-50/60 transition-colors">
                  <td className="px-4 py-3 text-slate-900">
                    {u.first_name} {u.last_name}
                    <div className="text-xs text-slate-500 mt-0.5">{u.email}</div>
                  </td>
                  <td className="px-4 py-3 text-slate-700">{u.role.label}</td>
                  <td className="px-4 py-3 text-slate-700">{u.status.label}</td>
                  <td className="px-4 py-3 text-right">
                    <div className="inline-flex items-center justify-end gap-1">
                      <button
                        type="button"
                        onClick={() => setEditingUser(u)}
                        aria-label="Редактировать"
                        className="inline-flex items-center justify-center rounded-xl p-2 text-slate-600 hover:bg-white hover:text-blue-600 hover:shadow-sm border border-transparent hover:border-slate-200/80 transition-colors"
                      >
                        <Pencil size={18} strokeWidth={2} />
                      </button>
                      <button
                        type="button"
                        onClick={() => handleDelete(u)}
                        disabled={deleteUser.isPending}
                        aria-label="Удалить"
                        className="inline-flex items-center justify-center rounded-xl p-2 text-slate-500 hover:bg-red-50 hover:text-red-600 border border-transparent hover:border-red-100 transition-colors disabled:opacity-40"
                      >
                        <Trash2 size={18} strokeWidth={2} />
                      </button>
                    </div>
                  </td>
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>

      {editingUser && (
        <UserEditModal
          user={editingUser}
          onClose={() => setEditingUser(null)}
        />
      )}
    </>
  );
};

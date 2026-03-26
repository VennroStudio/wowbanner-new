import React, { useRef, useState } from 'react';
import { Camera, Trash2, X } from 'lucide-react';
import { useAuth } from '@/features/auth';
import { userApi } from '@/entities/user';
import { Button } from '@/shared/components';

interface AvatarManagerProps {
  isOpen: boolean;
  onClose: () => void;
}

export const AvatarManager: React.FC<AvatarManagerProps> = ({ isOpen, onClose }) => {
  const { user, apiFetch, updateUser } = useAuth();
  const fileInputRef = useRef<HTMLInputElement>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  if (!isOpen || !user) return null;

  const handleFileChange = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;

    setLoading(true);
    setError(null);
    try {
      const res = (await userApi.uploadAvatar(apiFetch, user.id, file)) as {
        data: { avatar: string };
      };
      updateUser({ avatar: res.data.avatar });
      onClose();
    } catch (err: any) {
      setError(err?.error?.message || 'Ошибка при загрузке аватара');
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async () => {
    if (!window.confirm('Вы уверены, что хотите удалить аватар?')) return;

    setLoading(true);
    setError(null);
    try {
      await userApi.deleteAvatar(apiFetch, user.id);
      updateUser({ avatar: null });
      onClose();
    } catch (err: any) {
      setError(err?.error?.message || 'Ошибка при удалении аватара');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm animate-in fade-in duration-200">
      <div className="w-full max-w-sm bg-white rounded-3xl shadow-2xl overflow-hidden animate-in zoom-in-95 duration-200">
        <div className="flex items-center justify-between p-6 border-b border-slate-50">
          <h3 className="text-lg font-bold text-slate-800">Управление фото</h3>
          <button onClick={onClose} className="text-slate-400 hover:text-slate-600 transition-colors">
            <X size={20} />
          </button>
        </div>

        <div className="p-6 space-y-4">
          {error && (
            <div className="p-3 text-sm text-red-600 bg-red-50 rounded-xl border border-red-100 italic">
              {error}
            </div>
          )}

          <input
            type="file"
            ref={fileInputRef}
            onChange={handleFileChange}
            accept="image/*"
            className="hidden"
          />

          <Button
            onClick={() => fileInputRef.current?.click()}
            isLoading={loading}
            variant="secondary"
            className="w-full flex items-center justify-center gap-2 cursor-pointer"
          >
            <Camera size={18} /> Загрузить новое фото
          </Button>

          {user.avatar && (
            <button
              onClick={handleDelete}
              disabled={loading}
              className="w-full flex items-center justify-center gap-2 py-3 text-sm font-semibold text-red-500 hover:bg-red-50 rounded-2xl transition-all disabled:opacity-50 cursor-pointer"
            >
              <Trash2 size={18} /> Удалить текущее фото
            </button>
          )}
        </div>
      </div>
    </div>
  );
};

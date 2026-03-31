import React, { useRef } from 'react';
import { Camera, Trash2 } from 'lucide-react';
import { useAuthStore } from '@/features/auth/store/authStore';
import { useUploadAvatarCommand } from '@/entities/user/hooks/useUploadAvatarCommand';
import { useDeleteAvatarCommand } from '@/entities/user/hooks/useDeleteAvatarCommand';
import { Button, SmallModal } from '@/shared/components';
import type { AxiosError } from 'axios';
import type { ApiError } from '@/shared/types';

interface AvatarManagerProps {
  isOpen: boolean;
  onClose: () => void;
}

export const AvatarManager: React.FC<AvatarManagerProps> = ({ isOpen, onClose }) => {
  const { user, updateUser } = useAuthStore();
  const uploadAvatar = useUploadAvatarCommand();
  const deleteAvatar = useDeleteAvatarCommand();
  const fileInputRef = useRef<HTMLInputElement>(null);

  if (!user) return null;

  const handleFileChange = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;

    try {
      const response = await uploadAvatar.mutateAsync({ id: user.id, file });
      const avatar = response.data?.avatar;

      if (avatar) {
        updateUser({ avatar });
      }
      onClose();
    } catch (err) {
      console.error('Upload avatar error', err);
    }
  };

  const handleDelete = async () => {
    if (!window.confirm('Вы уверены, что хотите удалить аватар?')) return;

    try {
      await deleteAvatar.mutateAsync(user.id);
      updateUser({ avatar: null });
      onClose();
    } catch (err) {
      console.error('Delete avatar error', err);
    }
  };

  const loading = uploadAvatar.isPending || deleteAvatar.isPending;
  const error =
    (uploadAvatar.error as AxiosError<ApiError>)?.response?.data?.error?.message ||
    (deleteAvatar.error as AxiosError<ApiError>)?.response?.data?.error?.message;

  return (
    <SmallModal isOpen={isOpen} onClose={onClose} title="Управление фото" titleId="avatar-modal-title">
      <div className="space-y-4">
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
            type="button"
            onClick={handleDelete}
            disabled={loading}
            className="w-full flex items-center justify-center gap-2 py-3 text-sm font-semibold text-red-500 hover:bg-red-50 rounded-2xl transition-all disabled:opacity-50 cursor-pointer"
          >
            <Trash2 size={18} /> Удалить текущее фото
          </button>
        )}
      </div>
    </SmallModal>
  );
};

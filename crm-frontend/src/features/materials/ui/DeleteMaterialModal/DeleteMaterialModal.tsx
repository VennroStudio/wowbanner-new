import { useState } from 'react';
import type { Material } from '@/entities/material';
import { useDeleteMaterialCommand } from '@/entities/material';
import { getApiErrorMessage } from '@/shared/utils/axiosError';
import { ConfirmActionModal } from '@/shared/ui';

interface DeleteMaterialModalProps {
  open: boolean;
  material: Material | null;
  onClose: () => void;
  onSuccess?: () => void;
}

export const DeleteMaterialModal = ({
  open,
  material,
  onClose,
  onSuccess,
}: DeleteMaterialModalProps) => {
  const deleteMutation = useDeleteMaterialCommand();
  const [error, setError] = useState<string | null>(null);

  if (!open || !material) return null;

  const handleDelete = async () => {
    setError(null);
    try {
      await deleteMutation.mutateAsync(material.id);
      onSuccess?.();
      onClose();
    } catch (e) {
      setError(getApiErrorMessage(e));
    }
  };

  const handleClose = () => {
    setError(null);
    onClose();
  };

  return (
    <ConfirmActionModal
      open
      title="Удалить материал?"
      titleId="delete-material-title"
      description={(
        <>
          Материал <span className="font-medium text-slate-800">{material.name}</span> будет удалён
          вместе с привязанными изображениями.
        </>
      )}
      confirmLabel="Удалить"
      pendingLabel="Удаление…"
      isPending={deleteMutation.isPending}
      error={error}
      onClose={handleClose}
      onConfirm={() => void handleDelete()}
    />
  );
};

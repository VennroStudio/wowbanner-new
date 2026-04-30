import { useState } from 'react';
import type { Processing } from '@/entities/processing';
import { useDeleteProcessingCommand } from '@/entities/processing';
import { getApiErrorMessage } from '@/shared/utils/axiosError';
import { ConfirmActionModal } from '@/shared/ui';

interface DeleteProcessingModalProps {
  open: boolean;
  processing: Processing | null;
  onClose: () => void;
  onSuccess?: () => void;
}

export const DeleteProcessingModal = ({
  open,
  processing,
  onClose,
  onSuccess,
}: DeleteProcessingModalProps) => {
  const deleteMutation = useDeleteProcessingCommand();
  const [error, setError] = useState<string | null>(null);

  if (!open || !processing) return null;

  const handleDelete = async () => {
    setError(null);
    try {
      await deleteMutation.mutateAsync(processing.id);
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
      title="Удалить обработку?"
      titleId="delete-processing-title"
      description={(
        <>
          Обработка <span className="font-medium text-slate-800">{processing.name}</span> будет удалена
          вместе с изображениями.
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

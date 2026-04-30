import { useState } from 'react';
import type { Printing } from '@/entities/printing';
import { useDeletePrintingCommand } from '@/entities/printing';
import { getApiErrorMessage } from '@/shared/utils/axiosError';
import { ConfirmActionModal } from '@/shared/ui';

interface DeletePrintingModalProps {
  open: boolean;
  printing: Printing | null;
  onClose: () => void;
  onSuccess?: () => void;
}

export const DeletePrintingModal = ({
  open,
  printing,
  onClose,
  onSuccess,
}: DeletePrintingModalProps) => {
  const deleteMutation = useDeletePrintingCommand();
  const [error, setError] = useState<string | null>(null);

  if (!open || !printing) return null;

  const handleDelete = async () => {
    setError(null);
    try {
      await deleteMutation.mutateAsync(printing.id);
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
      title="Удалить тип печати?"
      titleId="delete-printing-title"
      description={(
        <>
          Тип печати <span className="font-medium text-slate-800">{printing.name}</span> будет удалён
          без возможности восстановления.
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

import { useState } from 'react';
import type { Client } from '@/entities/client';
import { useDeleteClientCommand } from '@/entities/client';
import { getApiErrorMessage } from '@/shared/utils/axiosError';
import { ConfirmActionModal } from '@/shared/ui';

interface DeleteClientModalProps {
  open: boolean;
  client: Client | null;
  onClose: () => void;
  onSuccess?: () => void;
}

export const DeleteClientModal = ({
  open,
  client,
  onClose,
  onSuccess,
}: DeleteClientModalProps) => {
  const deleteMutation = useDeleteClientCommand();
  const [error, setError] = useState<string | null>(null);

  if (!open || !client) return null;

  const fullName =
    `${client.last_name} ${client.first_name} ${client.middle_name ?? ''}`.trim();

  const handleDelete = async () => {
    setError(null);
    try {
      await deleteMutation.mutateAsync(client.id);
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
      title="Удалить клиента?"
      titleId="delete-client-title"
      description={(
        <>
          Клиент <span className="font-medium text-slate-800">{fullName}</span> будет удалён без
          возможности восстановления.
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

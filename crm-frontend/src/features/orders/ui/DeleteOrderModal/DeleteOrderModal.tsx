import { useState } from 'react';
import type { Order } from '@/entities/order';
import { useDeleteOrderCommand } from '@/entities/order';
import { ConfirmActionModal } from '@/shared/ui';
import { getApiErrorMessage } from '@/shared/utils/axiosError';

interface DeleteOrderModalProps {
  open: boolean;
  order: Order | null;
  onClose: () => void;
  onSuccess?: () => void;
}

export const DeleteOrderModal = ({
  open,
  order,
  onClose,
  onSuccess,
}: DeleteOrderModalProps) => {
  const deleteMutation = useDeleteOrderCommand();
  const [error, setError] = useState<string | null>(null);

  if (!open || !order) return null;

  const handleDelete = async () => {
    setError(null);
    try {
      await deleteMutation.mutateAsync(order.id);
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
      title="Удалить заказ?"
      titleId="delete-order-title"
      description={(
        <>
          Заказ <span className="font-medium text-slate-800">#{order.id}</span> будет удалён
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

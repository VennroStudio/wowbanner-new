import { useState } from 'react';
import type { Product } from '@/entities/product';
import { useDeleteProductCommand } from '@/entities/product';
import { getApiErrorMessage } from '@/shared/utils/axiosError';
import { ConfirmActionModal } from '@/shared/ui';

interface DeleteProductModalProps {
  open: boolean;
  product: Product | null;
  onClose: () => void;
  onSuccess?: () => void;
}

export const DeleteProductModal = ({
  open,
  product,
  onClose,
  onSuccess,
}: DeleteProductModalProps) => {
  const deleteMutation = useDeleteProductCommand();
  const [error, setError] = useState<string | null>(null);

  if (!open || !product) return null;

  const handleDelete = async () => {
    setError(null);
    try {
      await deleteMutation.mutateAsync(product.id);
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
      title="Удалить продукт?"
      titleId="delete-product-title"
      description={(
        <>
          Продукт <span className="font-medium text-slate-800">{product.name}</span> будет удалён
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

import { useEffect, useState } from 'react';
import { AlertTriangle } from 'lucide-react';
import type { Material } from '@/entities/material';
import { useDeleteMaterialCommand } from '@/entities/material';
import { getApiErrorMessage } from '@/shared/utils/axiosError';
import { ModalDialog } from '@/shared/ui';

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

  useEffect(() => {
    if (open) setError(null);
  }, [open]);

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

  return (
    <ModalDialog open title="Удалить материал?" titleId="delete-material-title" onClose={onClose} size="md">
      <div className="px-5 py-4">
        <div className="flex gap-3 text-amber-600 mb-3">
          <AlertTriangle className="shrink-0 mt-0.5" size={20} />
          <p className="text-sm text-slate-600">
            Материал <span className="font-medium text-slate-800">{material.name}</span> будет удалён
            вместе с привязанными изображениями.
          </p>
        </div>
        {error && (
          <div className="rounded-lg bg-red-50 border border-red-100 text-red-700 text-sm px-3 py-2 mb-3">
            {error}
          </div>
        )}
      </div>

      <div className="flex justify-end gap-2 px-5 py-4 border-t border-slate-100 bg-slate-50/80">
        <button
          type="button"
          onClick={onClose}
          className="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-lg transition-colors"
        >
          Отмена
        </button>
        <button
          type="button"
          onClick={() => void handleDelete()}
          disabled={deleteMutation.isPending}
          className="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors disabled:opacity-50"
        >
          {deleteMutation.isPending ? 'Удаление…' : 'Удалить'}
        </button>
      </div>
    </ModalDialog>
  );
};

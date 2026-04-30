import type { ReactNode } from 'react';
import { AlertTriangle } from 'lucide-react';
import { ModalDialog } from '@/shared/ui/ModalDialog';
import { FormErrorBanner } from '@/shared/ui/FormErrorBanner';

interface ConfirmActionModalProps {
  open: boolean;
  title: string;
  titleId: string;
  description: ReactNode;
  confirmLabel: string;
  pendingLabel: string;
  isPending?: boolean;
  error?: string | null;
  onClose: () => void;
  onConfirm: () => void;
}

export const ConfirmActionModal = ({
  open,
  title,
  titleId,
  description,
  confirmLabel,
  pendingLabel,
  isPending = false,
  error,
  onClose,
  onConfirm,
}: ConfirmActionModalProps) => (
  <ModalDialog open={open} title={title} titleId={titleId} onClose={onClose} size="md">
    <div className="px-5 py-4">
      <div className="flex gap-3 text-amber-600 mb-3">
        <AlertTriangle className="shrink-0 mt-0.5" size={20} />
        <div className="text-sm text-slate-600">{description}</div>
      </div>
      <FormErrorBanner message={error} />
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
        onClick={onConfirm}
        disabled={isPending}
        className="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors disabled:opacity-50"
      >
        {isPending ? pendingLabel : confirmLabel}
      </button>
    </div>
  </ModalDialog>
);

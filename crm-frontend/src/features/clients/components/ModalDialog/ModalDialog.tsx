import type { ReactNode } from 'react';
import { X } from 'lucide-react';

export type ModalDialogSize = 'md' | '2xl';

const maxWidth: Record<ModalDialogSize, string> = {
  md: 'max-w-md',
  '2xl': 'max-w-2xl',
};

interface ModalDialogProps {
  open: boolean;
  title: string;
  titleId: string;
  onClose: () => void;
  children: ReactNode;
  size?: ModalDialogSize;
}

export const ModalDialog = ({
  open,
  title,
  titleId,
  onClose,
  children,
  size = '2xl',
}: ModalDialogProps) => {
  if (!open) return null;

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40">
      <div
        className={`bg-white rounded-xl shadow-xl w-full ${maxWidth[size]} max-h-[90vh] flex flex-col min-h-0 border border-slate-200`}
        role="dialog"
        aria-modal="true"
        aria-labelledby={titleId}
      >
        <div className="flex items-center justify-between px-5 py-4 border-b border-slate-100 shrink-0">
          <h2 id={titleId} className="text-lg font-semibold text-slate-900">
            {title}
          </h2>
          <button
            type="button"
            onClick={onClose}
            className="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-colors"
            aria-label="Закрыть"
          >
            <X size={20} />
          </button>
        </div>

        {children}
      </div>
    </div>
  );
};

import { Pencil, Trash2 } from 'lucide-react';

interface RowActionButtonsProps {
  onEdit?: () => void;
  onDelete?: () => void;
  editLabel?: string;
  deleteLabel?: string;
}

export const RowActionButtons = ({
  onEdit,
  onDelete,
  editLabel = 'Редактировать',
  deleteLabel = 'Удалить',
}: RowActionButtonsProps) => (
  <div className="flex items-center gap-1.5">
    <button
      type="button"
      onClick={onEdit}
      className="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-600 border border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300 rounded-lg transition-colors cursor-pointer"
    >
      <Pencil size={12} />
      {editLabel}
    </button>
    <button
      type="button"
      onClick={onDelete}
      className="flex items-center justify-center w-7 h-7 text-red-400 border border-red-100 bg-red-50 hover:bg-red-100 hover:border-red-200 rounded-lg transition-colors cursor-pointer"
      aria-label={deleteLabel}
    >
      <Trash2 size={12} />
    </button>
  </div>
);

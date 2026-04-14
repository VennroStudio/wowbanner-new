import { Pencil, Trash2 } from 'lucide-react';
import type { Printing } from '@/entities/printing';

interface Props {
  printing: Printing;
  onEdit?: (printing: Printing) => void;
  onDelete?: (printing: Printing) => void;
}

export const PrintingTableRow = ({ printing, onEdit, onDelete }: Props) => (
  <tr className="border-b border-slate-100 last:border-0 hover:bg-slate-50/70 transition-colors group">
    <td className="px-5 py-4 align-top">
      <span className="text-xs text-slate-400 font-mono">#{printing.id}</span>
    </td>

    <td className="px-5 py-4 align-top">
      <button
        type="button"
        onClick={() => onEdit?.(printing)}
        className="text-left w-full group/name"
      >
        <span className="block font-medium text-slate-900 text-sm leading-snug group-hover/name:text-blue-600 transition-colors">
          {printing.name}
        </span>
      </button>
    </td>

    <td className="px-5 py-4 align-top">
      <div className="flex items-center gap-1.5">
        <button
          type="button"
          onClick={() => onEdit?.(printing)}
          className="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-600
            border border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300
            rounded-lg transition-colors cursor-pointer"
        >
          <Pencil size={12} />
          Редактировать
        </button>
        <button
          type="button"
          onClick={() => onDelete?.(printing)}
          className="flex items-center justify-center w-7 h-7 text-red-400
            border border-red-100 bg-red-50 hover:bg-red-100 hover:border-red-200
            rounded-lg transition-colors cursor-pointer"
          aria-label="Удалить"
        >
          <Trash2 size={12} />
        </button>
      </div>
    </td>
  </tr>
);

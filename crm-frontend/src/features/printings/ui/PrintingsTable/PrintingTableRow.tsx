import type { Printing } from '@/entities/printing';
import { RowActionButtons } from '@/shared/ui';

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
      <RowActionButtons onEdit={() => onEdit?.(printing)} onDelete={() => onDelete?.(printing)} />
    </td>
  </tr>
);

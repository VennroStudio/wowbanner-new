import { Pencil, Trash2 } from 'lucide-react';
import type { Material } from '@/entities/material';
import { htmlToPlainText } from '@/shared/lib/htmlToPlainText';

interface Props {
  material: Material;
  onEdit?: (material: Material) => void;
  onDelete?: (material: Material) => void;
}

export const MaterialTableRow = ({ material, onEdit, onDelete }: Props) => {
  const images = material.images ?? [];
  const preview = images.slice(0, 3);
  const descriptionPreview = htmlToPlainText(material.description ?? '');

  return (
    <tr className="border-b border-slate-100 last:border-0 hover:bg-slate-50/70 transition-colors group">
      <td className="px-5 py-4 align-top">
        <span className="text-xs text-slate-400 font-mono">#{material.id}</span>
      </td>

      <td className="px-5 py-4 align-top">
        <button
          type="button"
          onClick={() => onEdit?.(material)}
          className="text-left w-full font-medium text-slate-900 text-sm leading-snug group-hover:text-blue-600 transition-colors cursor-pointer"
        >
          {material.name}
        </button>
      </td>

      <td className="px-5 py-4 align-top min-w-0">
        {descriptionPreview ? (
          <p className="text-xs text-slate-500 line-clamp-3 leading-relaxed break-words whitespace-pre-line">
            {descriptionPreview}
          </p>
        ) : (
          <p className="text-xs text-slate-300 italic">Нет описания</p>
        )}
      </td>

      <td className="px-5 py-4 align-top">
        {preview.length > 0 ? (
          <div className="flex gap-1.5 flex-wrap">
            {preview.map((img) => (
              <a
                key={img.id}
                href={img.path}
                target="_blank"
                rel="noopener noreferrer"
                className="block w-10 h-10 rounded-md overflow-hidden border border-slate-200 bg-slate-100 shrink-0"
              >
                <img src={img.path} alt="" className="w-full h-full object-cover" />
              </a>
            ))}
            {images.length > 3 && (
              <span className="inline-flex items-center justify-center w-10 h-10 text-[10px] font-medium text-slate-500 bg-slate-100 rounded-md border border-slate-200">
                +{images.length - 3}
              </span>
            )}
          </div>
        ) : (
          <span className="text-slate-300 text-sm">—</span>
        )}
      </td>

      <td className="px-5 py-4 align-top">
        <div className="flex items-center gap-1.5">
          <button
            type="button"
            onClick={() => onEdit?.(material)}
            className="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-600
              border border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300
              rounded-lg transition-colors cursor-pointer"
          >
            <Pencil size={12} />
            Редактировать
          </button>
          <button
            type="button"
            onClick={() => onDelete?.(material)}
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
};

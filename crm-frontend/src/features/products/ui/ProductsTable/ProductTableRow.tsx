import type { Product } from '@/entities/product';
import { RowActionButtons } from '@/shared/ui';

interface Props {
  product: Product;
  onEdit?: (product: Product) => void;
  onDelete?: (product: Product) => void;
}

export const ProductTableRow = ({ product, onEdit, onDelete }: Props) => (
  <tr className="border-b border-slate-100 last:border-0 hover:bg-slate-50/70 transition-colors group">
    <td className="px-5 py-4 align-top">
      <span className="text-xs text-slate-400 font-mono">#{product.id}</span>
    </td>

    <td className="px-5 py-4 align-top">
      <button
        type="button"
        onClick={() => onEdit?.(product)}
        className="text-left w-full group/name"
      >
        <span className="block font-medium text-slate-900 text-sm leading-snug group-hover/name:text-blue-600 transition-colors">
          {product.name}
        </span>
      </button>
    </td>

    <td className="px-5 py-4 align-top">
      {product.materials.length > 0 ? (
        <div className="flex flex-wrap gap-1.5">
          {product.materials.map((item) => (
            <span
              key={item.id}
              className="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs text-slate-600"
            >
              {item.material_name ?? 'Материал'}: {item.material_option_name ?? `#${item.material_option_id}`}
            </span>
          ))}
        </div>
      ) : (
        <span className="text-slate-300 text-sm">—</span>
      )}
    </td>

    <td className="px-5 py-4 align-top">
      {product.prints.length > 0 ? (
        <div className="flex flex-wrap gap-1.5">
          {product.prints.map((item) => (
            <span
              key={item.id}
              className="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-xs text-blue-700"
            >
              {item.print_name ?? `#${item.print_id}`}
            </span>
          ))}
        </div>
      ) : (
        <span className="text-slate-300 text-sm">—</span>
      )}
    </td>

    <td className="px-5 py-4 align-top">
      <RowActionButtons onEdit={() => onEdit?.(product)} onDelete={() => onDelete?.(product)} />
    </td>
  </tr>
);

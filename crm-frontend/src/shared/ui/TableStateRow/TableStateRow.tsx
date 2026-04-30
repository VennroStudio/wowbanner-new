import type { ReactNode } from 'react';

interface TableStateRowProps {
  colSpan: number;
  icon?: ReactNode;
  title: string;
  description?: string;
  tone?: 'default' | 'error';
}

export const TableStateRow = ({
  colSpan,
  icon,
  title,
  description,
  tone = 'default',
}: TableStateRowProps) => {
  const titleClass = tone === 'error' ? 'text-red-500' : 'text-slate-600';
  const iconClass = tone === 'error' ? 'text-red-300' : 'text-slate-200';

  return (
    <tr>
      <td colSpan={colSpan} className="px-6 py-14 text-center">
        {icon ? <div className={`mx-auto mb-3 w-fit ${iconClass}`}>{icon}</div> : null}
        <p className={`text-sm font-medium ${titleClass}`}>{title}</p>
        {description ? (
          <p className="text-xs text-slate-400 mt-1">{description}</p>
        ) : null}
      </td>
    </tr>
  );
};

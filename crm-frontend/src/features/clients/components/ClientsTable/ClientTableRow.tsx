import { Pencil, Trash2 } from 'lucide-react';
import type { Client } from '@/entities/client';

interface Props {
  client: Client;
  onEdit?: (client: Client) => void;
  onDelete?: (client: Client) => void;
}

export const ClientTableRow = ({ client, onEdit, onDelete }: Props) => {
  const fullName = `${client.last_name} ${client.first_name} ${client.middle_name || ''}`.trim();

  return (
    <tr className="border-b border-slate-100 last:border-0 hover:bg-slate-50/70 transition-colors group">
      <td className="px-5 py-4 align-top">
        <span className="text-xs text-slate-400 font-mono">#{client.id}</span>
      </td>

      <td className="px-5 py-4 align-top">
        <button
          type="button"
          onClick={() => onEdit?.(client)}
          className="text-left w-full font-medium text-slate-900 text-sm leading-snug mb-1.5 group-hover:text-blue-600 transition-colors cursor-pointer"
        >
          {fullName}
        </button>
        <div className="text-xs text-slate-400 leading-relaxed">
          {client.type.label}
          {client.docs && (
            <>
              <span className="mx-1.5 text-slate-200">·</span>
              {client.docs.label}
            </>
          )}
        </div>
      </td>

      <td className="px-5 py-4 align-top">
        {client.companies && client.companies.length > 0 ? (
          <div className="flex flex-col gap-1.5">
            {client.companies.map((company) => (
              <span
                key={company.id}
                className="inline-block px-2.5 py-1 bg-slate-100 text-slate-600 text-xs rounded font-medium w-fit"
              >
                {company.company_name}
              </span>
            ))}
          </div>
        ) : (
          <span className="text-slate-300 text-sm">—</span>
        )}
      </td>

      <td className="px-5 py-4 align-top">
        <div className="flex flex-col gap-1">
          {client.email ? (
            <a
              href={`mailto:${client.email}`}
              className="text-sm text-blue-600 hover:text-blue-700 hover:underline transition-colors"
            >
              {client.email}
            </a>
          ) : (
            <span className="text-xs text-slate-300 italic">Нет email</span>
          )}
          {client.phones && client.phones.length > 0 ? (
            client.phones.map((p) => (
              <span key={p.id} className="text-sm text-slate-600">
                {p.phone}
              </span>
            ))
          ) : (
            <span className="text-xs text-slate-300 italic">Нет телефона</span>
          )}
        </div>
      </td>

      <td className="px-5 py-4 align-top">
        <div className="flex items-center gap-1.5">
          <button
            type="button"
            onClick={() => onEdit?.(client)}
            className="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-600
              border border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300
              rounded-lg transition-colors cursor-pointer"
          >
            <Pencil size={12} />
            Редактировать
          </button>
          <button
            type="button"
            onClick={() => onDelete?.(client)}
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
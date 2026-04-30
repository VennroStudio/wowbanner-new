import type { Client } from '@/entities/client';
import { formatRuMobileDisplayFromStorage } from '@/shared/lib/ruMobilePhone';
import { RowActionButtons } from '@/shared/ui';

interface Props {
  client: Client;
  onEdit?: (client: Client) => void;
  onDelete?: (client: Client) => void;
}

export const ClientTableRow = ({ client, onEdit, onDelete }: Props) => {
  const fullName =
    `${client.last_name || ''} ${client.first_name || ''} ${client.middle_name || ''}`.trim() ||
    client.old_full_name;
  const docsLabel = client.docs?.label ?? '—';

  return (
    <tr className="border-b border-slate-100 last:border-0 hover:bg-slate-50/70 transition-colors group">
      <td className="px-5 py-4 align-top">
        <span className="text-xs text-slate-400 font-mono">#{client.id}</span>
      </td>

      <td className="px-5 py-4 align-top">
        <button
          type="button"
          onClick={() => onEdit?.(client)}
          className="text-left w-full font-medium text-slate-900 text-sm leading-snug group-hover:text-blue-600 transition-colors cursor-pointer"
        >
          {fullName}
        </button>
        <div className="text-xs text-slate-400 leading-relaxed flex flex-col gap-0.5">
          <span>{client.type.label}</span>
          <span>{docsLabel}</span>
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
                {formatRuMobileDisplayFromStorage(p.phone)}
              </span>
            ))
          ) : (
            <span className="text-xs text-slate-300 italic">Нет телефона</span>
          )}
        </div>
      </td>

      <td className="px-5 py-4 align-top">
        <RowActionButtons onEdit={() => onEdit?.(client)} onDelete={() => onDelete?.(client)} />
      </td>
    </tr>
  );
};

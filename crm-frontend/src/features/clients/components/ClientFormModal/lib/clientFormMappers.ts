import type { Client } from '@/entities/client';
import type { CreateClientBody, UpdateClientBody } from '@/entities/client';
import { digitsOnly, parseDigitsToStorage } from '@/shared/lib/ruMobilePhone';
import type { ClientFormValues } from './clientFormSchema';

export function mapClientToFormValues(client: Client): ClientFormValues {
  return {
    lastName: client.last_name,
    firstName: client.first_name,
    middleName: client.middle_name ?? '',
    email: client.email ?? '',
    type: client.type.id,
    docs: client.docs?.id ?? 1,
    info: client.info ?? '',
    phones:
      client.phones.length > 0
        ? client.phones.map((p) => ({
            id: p.id,
            type: p.type.id,
            phone: p.phone ? parseDigitsToStorage(digitsOnly(p.phone)) : '',
          }))
        : [{ type: 1, phone: '' }],
    companies: client.companies.map((c) => ({
      id: c.id,
      name: c.company_name,
    })),
  };
}

export function buildCreateClientBody(values: ClientFormValues): CreateClientBody {
  const phones = values.phones
    .filter((p) => digitsOnly(p.phone))
    .map(({ type, phone }) => ({ type, phone: digitsOnly(phone) }));
  const companies =
    values.type === 2
      ? values.companies.filter((c) => c.name.trim()).map(({ name }) => ({ name: name.trim() }))
      : undefined;

  return {
    lastName: values.lastName.trim(),
    firstName: values.firstName.trim(),
    middleName: values.middleName?.trim() || undefined,
    email: values.email.trim() || undefined,
    type: values.type,
    docs: values.docs,
    info: values.info?.trim() || undefined,
    phones: phones.length ? phones : undefined,
    companies,
  };
}

export function buildUpdateClientBody(values: ClientFormValues): UpdateClientBody {
  const phones = values.phones
    .filter((p) => digitsOnly(p.phone))
    .map((p) => {
      const row = { type: p.type, phone: digitsOnly(p.phone) };
      return p.id != null ? { id: p.id, ...row } : row;
    });
  const companies =
    values.type === 2
      ? values.companies
          .filter((c) => c.name.trim())
          .map((c) => {
            const name = c.name.trim();
            return c.id != null ? { id: c.id, name } : { name };
          })
      : [];

  return {
    lastName: values.lastName.trim(),
    firstName: values.firstName.trim(),
    middleName: values.middleName?.trim() || undefined,
    email: values.email.trim() || undefined,
    type: values.type,
    docs: values.docs,
    info: values.info?.trim() || undefined,
    phones,
    companies,
  };
}

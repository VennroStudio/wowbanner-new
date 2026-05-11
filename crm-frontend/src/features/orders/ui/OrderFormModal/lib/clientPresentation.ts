import type { Client } from '@/entities/client';

export const getClientDisplayName = (client: Client) => {
  const old = client.old_full_name?.trim();
  if (old) return old;

  return [client.last_name, client.first_name, client.middle_name].filter(Boolean).join(' ');
};

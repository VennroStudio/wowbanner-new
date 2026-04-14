/** Элемент справочника из enum (GET /clients/types и т.д.): { value, label } */
export interface ClientEnumOption {
  value: number;
  label: string;
}

export interface DictItem {
  id: number;
  label: string;
}

export interface ClientPhone {
  id: number;
  type: DictItem;
  phone: string;
}

export interface ClientCompany {
  id: number;
  company_name: string;
}

export interface Client {
  id: number;
  old_full_name: string | null;
  last_name: string;
  first_name: string;
  middle_name: string | null;
  email: string | null;
  type: DictItem;
  docs: DictItem | null;
  info?: string | null;
  phones: ClientPhone[];
  companies: ClientCompany[];
  created_at?: string;
  updated_at?: string | null;
}

export interface GetClientsParams {
  page?: number;
  perPage?: number;
  search?: string;
}

export interface PaginatedResponse<T> {
  data: {
    count: number;
    items: T[];
  };
}

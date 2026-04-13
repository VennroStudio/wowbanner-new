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

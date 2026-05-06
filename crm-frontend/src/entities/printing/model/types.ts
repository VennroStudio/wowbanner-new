import type { PaginatedResponse } from '@/shared/api/types';

export interface Printing {
  id: number;
  name: string;
}

export interface PrintingSelectOption {
  id: number;
  name: string;
}

export interface GetPrintingsParams {
  page?: number;
  perPage?: number;
  search?: string;
}
export type PaginatedPrintingsResponse = PaginatedResponse<Printing>;

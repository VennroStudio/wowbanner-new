import type { PaginatedResponse } from '@/shared/api/types';

export interface MaterialImage {
  id: number;
  path: string;
  alt: string | null;
}

export interface Material {
  id: number;
  name: string;
  description: string;
  images: MaterialImage[];
}

export interface GetMaterialsParams {
  page?: number;
  perPage?: number;
  search?: string;
}
export type { PaginatedResponse };

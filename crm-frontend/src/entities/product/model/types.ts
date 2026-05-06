import type { PaginatedResponse } from '@/shared/api/types';

export interface ProductMaterialLink {
  id: number;
  material_option_id: number;
  material_id?: number;
  material_name?: string;
  material_option_name?: string;
}

export interface ProductPrintLink {
  id: number;
  print_id: number;
  print_name?: string;
}

export interface Product {
  id: number;
  name: string;
  materials: ProductMaterialLink[];
  prints: ProductPrintLink[];
}

export interface ProductMaterialPayload {
  id?: number;
  materialOptionId: number;
}

export interface ProductPrintPayload {
  id?: number;
  printId: number;
}

export interface GetProductsParams {
  page?: number;
  perPage?: number;
  search?: string;
}

export type PaginatedProductsResponse = PaginatedResponse<Product>;

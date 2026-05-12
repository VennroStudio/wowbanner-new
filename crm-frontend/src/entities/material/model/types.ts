import type { PaginatedResponse } from '@/shared/api/types';

export interface MaterialEnumRef {
  id: number;
  label: string;
}

export interface MaterialSelectOption {
  id: number;
  name: string;
}

export interface MaterialOptionSelectOption {
  id: number;
  name: string;
}

export interface MaterialProcessingSelectOption {
  id: number;
  name: string;
}

export interface MaterialImage {
  id: number;
  path: string;
  alt: string | null;
}

export interface MaterialOptionProcessing {
  id: number;
  processingId: number;
}

export interface MaterialPricingByAreaRow {
  id: number;
  dpiType: MaterialEnumRef;
  areaRangeType: MaterialEnumRef;
  price: string;
  cost: string;
  printHours: string;
}

export interface MaterialPricingByPieceRow {
  id: number;
  variantType: MaterialEnumRef;
  price: string;
  cost: string;
  printHours: string;
}

export interface MaterialPricingByCutRow {
  id: number;
  type: MaterialEnumRef;
  price: string;
}

export interface MaterialOption {
  id: number;
  name: string;
  pricingType: MaterialEnumRef;
  isCut: boolean;
  pricingByArea: MaterialPricingByAreaRow[];
  pricingByPiece: MaterialPricingByPieceRow[];
  pricingByCut: MaterialPricingByCutRow[];
  processings: MaterialOptionProcessing[];
}

export interface Material {
  id: number;
  name: string;
  description: string;
  images: MaterialImage[];
  options?: MaterialOption[];
}

export interface MaterialCreateUpdateOptionProcessing {
  id?: number;
  processingId: number;
}

export interface MaterialCreateUpdatePricingByAreaRow {
  id?: number;
  dpiType: number;
  areaRangeType: number;
  price: string;
  cost: string;
  printHours: string;
}

export interface MaterialCreateUpdatePricingByPieceRow {
  id?: number;
  variantType: number;
  price: string;
  cost: string;
  printHours: string;
}

export interface MaterialCreateUpdatePricingByCutRow {
  id?: number;
  type: number;
  price: string;
}

export interface MaterialCreateUpdateOption {
  id?: number;
  name: string;
  pricingType: number;
  isCut: boolean;
  pricingByArea: MaterialCreateUpdatePricingByAreaRow[];
  pricingByPiece: MaterialCreateUpdatePricingByPieceRow[];
  pricingByCut: MaterialCreateUpdatePricingByCutRow[];
  processings: MaterialCreateUpdateOptionProcessing[];
}

export interface GetMaterialsParams {
  page?: number;
  perPage?: number;
  search?: string;
}

export type { PaginatedResponse };

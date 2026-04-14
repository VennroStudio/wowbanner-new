export interface ProcessingTypeRef {
  id: number;
  label: string;
}

export interface ProcessingImage {
  id: number;
  path: string;
  alt: string | null;
}

/** Элемент списка и деталь (деталь может содержать description и cost_price) */
export interface Processing {
  id: number;
  name: string;
  description?: string;
  type: ProcessingTypeRef;
  cost_price?: string;
  price: string;
  images: ProcessingImage[];
}

export interface GetProcessingsParams {
  page?: number;
  perPage?: number;
  search?: string;
}

export interface PaginatedProcessingsResponse {
  data: {
    count: number;
    items: Processing[];
  };
}

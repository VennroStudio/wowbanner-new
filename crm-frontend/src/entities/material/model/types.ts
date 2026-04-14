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

export interface PaginatedResponse<T> {
  data: {
    count: number;
    items: T[];
  };
}

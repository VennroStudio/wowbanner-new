export interface Printing {
  id: number;
  name: string;
}

export interface GetPrintingsParams {
  page?: number;
  perPage?: number;
  search?: string;
}

export interface PaginatedPrintingsResponse {
  data: {
    count: number;
    items: Printing[];
  };
}

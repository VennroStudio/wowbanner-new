export interface ApiDataResponse<T> {
  data: T;
}

export interface PaginatedItems<T> {
  count: number;
  items: T[];
}

export type PaginatedResponse<T> = ApiDataResponse<PaginatedItems<T>>;

export type ApiMutationResponse<T = number> = ApiDataResponse<T>;

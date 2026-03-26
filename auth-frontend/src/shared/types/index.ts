export interface ApiError {
  error?: { code: number; message: string };
  validations?: { field: string; message: string }[];
}

export interface ApiResponse<T> {
  data: T;
}

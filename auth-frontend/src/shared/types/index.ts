export interface ApiError {
  error?: { code: number; message: string };
  validations?: { field: string; message: string }[];
}

export interface ApiResponse<T> {
  data: T;
}

export interface LoginDto {
  email: string;
  password: string;
}

export interface ResetPasswordDto {
  token: string;
  password: string;
}

export interface RegisterDto {
  firstName: string;
  lastName: string;
  email: string;
}

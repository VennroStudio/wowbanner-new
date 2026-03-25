export interface User {
  id: number;
  role: { id: number; label: string };
  first_name: string;
  last_name: string;
  email: string;
  avatar: string | null;
  created_at: string;
  is_active: boolean;
}

export interface ApiError {
  error?: { code: number; message: string };
  validations?: { field: string; message: string }[];
}

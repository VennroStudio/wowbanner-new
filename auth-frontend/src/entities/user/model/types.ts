export interface User {
  id: number;
  role: { id: number; label: string };
  status: { id: number; label: string };
  first_name: string;
  last_name: string;
  email: string;
  avatar: string | null;
  created_at: string;
}

export interface RegisterDto {
  firstName: string;
  lastName: string;
  email: string;
}

import type { AxiosError } from 'axios';

type ErrorBody = {
  error?: { code?: number; message?: string };
  validations?: { field: string; message: string }[];
};

export function getApiErrorMessage(err: unknown): string {
  const ax = err as AxiosError<ErrorBody>;
  const data = ax.response?.data;
  if (!data) return ax.message || 'Произошла ошибка';

  if (data.validations?.length) {
    return data.validations.map((v) => v.message).join(' ');
  }
  if (data.error?.message) {
    return data.error.message;
  }
  return 'Произошла ошибка';
}

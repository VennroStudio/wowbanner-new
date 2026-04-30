import * as z from 'zod';

export const materialFormSchema = z.object({
  name: z.string().min(2, 'Название — минимум 2 символа'),
  description: z.string().max(60000, 'Описание слишком длинное').optional(),
});

export type MaterialFormValues = z.infer<typeof materialFormSchema>;

export const getMaterialFormDefaultValues = (): MaterialFormValues => ({
  name: '',
  description: '',
});

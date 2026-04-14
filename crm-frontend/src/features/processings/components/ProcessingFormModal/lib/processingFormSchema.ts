import * as z from 'zod';

export const processingFormSchema = z.object({
  name: z.string().min(2, 'Название — минимум 2 символа'),
  description: z.string().max(60000, 'Описание слишком длинное').optional(),
  typeId: z.coerce.number().refine((n) => n > 0, { message: 'Выберите тип расчёта' }),
  costPrice: z.string().min(1, 'Укажите себестоимость'),
  price: z.string().min(1, 'Укажите цену'),
});

export type ProcessingFormValues = z.infer<typeof processingFormSchema>;

export const getProcessingFormDefaultValues = (): ProcessingFormValues => ({
  name: '',
  description: '',
  typeId: 0,
  costPrice: '',
  price: '',
});

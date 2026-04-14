import { z } from 'zod';

export const printingFormSchema = z.object({
  name: z.string().trim().min(2, 'Не менее 2 символов').max(255, 'Не более 255 символов'),
});

export type PrintingFormValues = z.infer<typeof printingFormSchema>;

export const getPrintingFormDefaultValues = (): PrintingFormValues => ({
  name: '',
});

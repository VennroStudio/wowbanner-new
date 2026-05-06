import * as z from 'zod';

const materialLinkSchema = z.object({
  id: z.number().int().positive().optional(),
  materialId: z.number().int().positive('Выберите материал'),
  materialName: z.string().optional(),
  materialOptionId: z.number().int().positive('Выберите опцию'),
  materialOptionName: z.string().optional(),
});

const printLinkSchema = z.object({
  id: z.number().int().positive().optional(),
  printId: z.number().int().positive('Выберите тип печати'),
  printName: z.string().optional(),
});

export const productFormSchema = z.object({
  name: z.string().min(2, 'Название — минимум 2 символа'),
  materials: z.array(materialLinkSchema),
  prints: z.array(printLinkSchema),
});

export type ProductMaterialFormValue = z.infer<typeof materialLinkSchema>;
export type ProductPrintFormValue = z.infer<typeof printLinkSchema>;
export type ProductFormValues = z.infer<typeof productFormSchema>;

export const getProductFormDefaultValues = (): ProductFormValues => ({
  name: '',
  materials: [],
  prints: [],
});

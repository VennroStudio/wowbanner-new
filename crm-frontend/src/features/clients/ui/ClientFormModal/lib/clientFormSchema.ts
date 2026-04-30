import * as z from 'zod';
import { digitsOnly, isValidRuMobileStorage } from '@/shared/lib/ruMobilePhone';

const phoneRowSchema = z
  .object({
    id: z.number().optional(),
    type: z.number(),
    phone: z.string(),
  })
  .superRefine((row, ctx) => {
    const d = digitsOnly(row.phone);
    if (!d) return;
    if (!isValidRuMobileStorage(d)) {
      ctx.addIssue({
        code: z.ZodIssueCode.custom,
        message: 'Введите полный номер (10 цифр после +7)',
        path: ['phone'],
      });
    }
  });

const companyRowSchema = z.object({
  id: z.number().optional(),
  name: z.string(),
});

export const clientFormSchema = z
  .object({
    lastName: z.string().min(1, 'Укажите фамилию'),
    firstName: z.string().min(1, 'Укажите имя'),
    middleName: z.string().optional(),
    email: z.union([z.literal(''), z.string().email('Некорректный email')]),
    type: z.number(),
    docs: z.number(),
    info: z.string().optional(),
    phones: z.array(phoneRowSchema),
    companies: z.array(companyRowSchema),
  })
  .superRefine((data, ctx) => {
    if (data.type === 2) {
      const withName = data.companies.filter((c) => c.name.trim());
      if (withName.length === 0) {
        ctx.addIssue({
          code: z.ZodIssueCode.custom,
          message: 'Для юридического лица нужна хотя бы одна компания',
          path: ['companies', 0, 'name'],
        });
      }
    }
  });

export type ClientFormValues = z.infer<typeof clientFormSchema>;

export const getClientFormDefaultValues = (): ClientFormValues => ({
  lastName: '',
  firstName: '',
  middleName: '',
  email: '',
  type: 1,
  docs: 1,
  info: '',
  phones: [{ type: 1, phone: '' }],
  companies: [],
});

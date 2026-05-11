import { z } from 'zod';

const orderServiceFormSchema = z.object({
  serviceType: z.string().min(1, 'Выберите услугу'),
  price: z.string().trim().min(1, 'Укажите цену'),
  note: z.string(),
});

export const orderFormSchema = z
  .object({
    clientId: z.string().min(1, 'Выберите заказчика'),
    managerId: z.string(),
    designerId: z.string(),
    statusType: z.string().min(1, 'Выберите статус'),
    storageType: z.string().min(1, 'Выберите склад'),
    acceptedAt: z.string().min(1, 'Укажите дату постановки'),
    deadlineAt: z.string().min(1, 'Укажите дату сдачи'),
    generalNote: z.string(),
    extension: z.string(),
    hasDelivery: z.boolean(),
    deliveryType: z.string(),
    deliveryAddress: z.string(),
    deliveryComment: z.string(),
    services: z.array(orderServiceFormSchema),
  })
  .superRefine((values, ctx) => {
    if (values.hasDelivery && !values.deliveryType) {
      ctx.addIssue({
        code: z.ZodIssueCode.custom,
        path: ['deliveryType'],
        message: 'Выберите тип доставки',
      });
    }

    if (values.acceptedAt && values.deadlineAt && values.acceptedAt > values.deadlineAt) {
      ctx.addIssue({
        code: z.ZodIssueCode.custom,
        path: ['deadlineAt'],
        message: 'Дата сдачи не может быть раньше даты постановки',
      });
    }
  });

export type OrderFormValues = z.infer<typeof orderFormSchema>;

export const getOrderFormDefaultValues = (): OrderFormValues => ({
  clientId: '',
  managerId: '',
  designerId: '',
  statusType: '',
  storageType: '',
  acceptedAt: '',
  deadlineAt: '',
  generalNote: '',
  extension: '',
  hasDelivery: false,
  deliveryType: '',
  deliveryAddress: '',
  deliveryComment: '',
  services: [],
});

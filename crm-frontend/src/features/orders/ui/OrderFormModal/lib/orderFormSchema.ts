import { z } from 'zod';

const orderServiceFormSchema = z.object({
  serviceType: z.string().min(1, 'Выберите услугу'),
  price: z.string().trim().min(1, 'Укажите цену'),
  note: z.string(),
});

const orderItemFormSchema = z.object({
  printId: z.string().min(1, 'Выберите тип печати'),
  productId: z.string().min(1, 'Выберите продукцию'),
  materialId: z.string().min(1, 'Выберите материал'),
  optionId: z.string().min(1, 'Выберите опцию материала'),
  dpiType: z.string().min(1, 'Выберите DPI'),
  variantType: z.string().min(1, 'Выберите вариант'),
  width: z.string().trim().min(1, 'Укажите ширину'),
  height: z.string().trim().min(1, 'Укажите высоту'),
  quantity: z.string().trim().min(1, 'Укажите количество'),
  price: z.string().trim().min(1, 'Укажите цену'),
  performerId: z.string(),
  note: z.string(),
  printed: z.boolean(),
  ready: z.boolean(),
  processings: z.array(z.string()),
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
    sections: z.array(z.string()),
    hasDelivery: z.boolean(),
    deliveryType: z.string(),
    deliveryAddress: z.string(),
    deliveryComment: z.string(),
    items: z.array(orderItemFormSchema),
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

    values.items.forEach((item, index) => {
      if (Number(item.quantity) <= 0) {
        ctx.addIssue({
          code: z.ZodIssueCode.custom,
          path: ['items', index, 'quantity'],
          message: 'Количество должно быть больше 0',
        });
      }
    });
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
  sections: [],
  hasDelivery: false,
  deliveryType: '',
  deliveryAddress: '',
  deliveryComment: '',
  items: [],
  services: [],
});

export const createOrderItemDefaultValue = (printId: number | string) => ({
  printId: String(printId),
  productId: '',
  materialId: '',
  optionId: '',
  dpiType: '',
  variantType: '',
  width: '',
  height: '',
  quantity: '1',
  price: '',
  performerId: '',
  note: '',
  printed: false,
  ready: false,
  processings: [],
});

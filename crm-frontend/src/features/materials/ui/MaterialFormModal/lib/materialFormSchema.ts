import * as z from 'zod';

const decimalPattern = /^\d+(\.\d{1,2})?$/;

const isFilled = (value?: string) => (value ?? '').trim().length > 0;

const hasAreaRowValues = (row: MaterialAreaPricingFormValue) =>
  isFilled(row.price) || isFilled(row.cost) || isFilled(row.printHours);

const hasPieceRowValues = (row: MaterialPiecePricingFormValue) =>
  isFilled(row.price) || isFilled(row.cost) || isFilled(row.printHours);

const hasCutRowValues = (row: MaterialCutPricingFormValue) => isFilled(row.price);

const processingLinkSchema = z.object({
  id: z.number().int().positive().optional(),
  processingId: z.number().int().positive('Выберите обработку'),
});

const areaPricingSchema = z.object({
  id: z.number().int().positive().optional(),
  dpiType: z.number().int().positive(),
  areaRangeType: z.number().int().positive(),
  price: z.string(),
  cost: z.string(),
  printHours: z.string(),
});

const piecePricingSchema = z.object({
  id: z.number().int().positive().optional(),
  variantType: z.number().int().positive(),
  price: z.string(),
  cost: z.string(),
  printHours: z.string(),
});

const cutPricingSchema = z.object({
  id: z.number().int().positive().optional(),
  type: z.number().int().positive(),
  price: z.string(),
});

const optionSchema = z.object({
  id: z.number().int().positive().optional(),
  name: z.string().min(1, 'Название опции обязательно').max(255, 'Название опции слишком длинное'),
  pricingTypeId: z.number().int().positive('Выберите тип расчёта'),
  isCut: z.boolean(),
  processings: z.array(processingLinkSchema),
  pricingByArea: z.record(z.string(), areaPricingSchema),
  pricingByPiece: z.record(z.string(), piecePricingSchema),
  pricingByCut: z.record(z.string(), cutPricingSchema),
}).superRefine((option, ctx) => {
  const processingIds = new Set<number>();
  option.processings.forEach((processing, index) => {
    if (processingIds.has(processing.processingId)) {
      ctx.addIssue({
        code: z.ZodIssueCode.custom,
        path: ['processings', index, 'processingId'],
        message: 'Эта обработка уже добавлена',
      });
      return;
    }
    processingIds.add(processing.processingId);
  });

  Object.entries(option.pricingByArea).forEach(([key, row]) => {
    if (!hasAreaRowValues(row)) return;

    if (!isFilled(row.price)) {
      ctx.addIssue({
        code: z.ZodIssueCode.custom,
        path: ['pricingByArea', key, 'price'],
        message: 'Укажите цену',
      });
    } else if (!decimalPattern.test(row.price.trim())) {
      ctx.addIssue({
        code: z.ZodIssueCode.custom,
        path: ['pricingByArea', key, 'price'],
        message: 'Некорректный формат цены',
      });
    }

    if (!isFilled(row.cost)) {
      ctx.addIssue({
        code: z.ZodIssueCode.custom,
        path: ['pricingByArea', key, 'cost'],
        message: 'Укажите себестоимость',
      });
    } else if (!decimalPattern.test(row.cost.trim())) {
      ctx.addIssue({
        code: z.ZodIssueCode.custom,
        path: ['pricingByArea', key, 'cost'],
        message: 'Некорректный формат себестоимости',
      });
    }

    if (!isFilled(row.printHours)) {
      ctx.addIssue({
        code: z.ZodIssueCode.custom,
        path: ['pricingByArea', key, 'printHours'],
        message: 'Укажите норму час',
      });
    } else if (!decimalPattern.test(row.printHours.trim())) {
      ctx.addIssue({
        code: z.ZodIssueCode.custom,
        path: ['pricingByArea', key, 'printHours'],
        message: 'Некорректный формат нормы час',
      });
    }
  });

  Object.entries(option.pricingByPiece).forEach(([key, row]) => {
    if (!hasPieceRowValues(row)) return;

    if (!isFilled(row.price)) {
      ctx.addIssue({
        code: z.ZodIssueCode.custom,
        path: ['pricingByPiece', key, 'price'],
        message: 'Укажите цену',
      });
    } else if (!decimalPattern.test(row.price.trim())) {
      ctx.addIssue({
        code: z.ZodIssueCode.custom,
        path: ['pricingByPiece', key, 'price'],
        message: 'Некорректный формат цены',
      });
    }

    if (!isFilled(row.cost)) {
      ctx.addIssue({
        code: z.ZodIssueCode.custom,
        path: ['pricingByPiece', key, 'cost'],
        message: 'Укажите себестоимость',
      });
    } else if (!decimalPattern.test(row.cost.trim())) {
      ctx.addIssue({
        code: z.ZodIssueCode.custom,
        path: ['pricingByPiece', key, 'cost'],
        message: 'Некорректный формат себестоимости',
      });
    }

    if (!isFilled(row.printHours)) {
      ctx.addIssue({
        code: z.ZodIssueCode.custom,
        path: ['pricingByPiece', key, 'printHours'],
        message: 'Укажите норму час',
      });
    } else if (!decimalPattern.test(row.printHours.trim())) {
      ctx.addIssue({
        code: z.ZodIssueCode.custom,
        path: ['pricingByPiece', key, 'printHours'],
        message: 'Некорректный формат нормы час',
      });
    }
  });

  Object.entries(option.pricingByCut).forEach(([key, row]) => {
    if (!hasCutRowValues(row)) return;

    if (!option.isCut) {
      ctx.addIssue({
        code: z.ZodIssueCode.custom,
        path: ['pricingByCut', key, 'price'],
        message: 'Сначала включите рез',
      });
      return;
    }

    if (!decimalPattern.test(row.price.trim())) {
      ctx.addIssue({
        code: z.ZodIssueCode.custom,
        path: ['pricingByCut', key, 'price'],
        message: 'Некорректный формат цены',
      });
    }
  });
});

export const materialFormSchema = z.object({
  name: z.string().min(2, 'Название — минимум 2 символа'),
  description: z.string().max(60000, 'Описание слишком длинное'),
  options: z.array(optionSchema),
});

export type MaterialProcessingFormValue = z.infer<typeof processingLinkSchema>;
export type MaterialAreaPricingFormValue = z.infer<typeof areaPricingSchema>;
export type MaterialPiecePricingFormValue = z.infer<typeof piecePricingSchema>;
export type MaterialCutPricingFormValue = z.infer<typeof cutPricingSchema>;
export type MaterialOptionFormValues = z.infer<typeof optionSchema>;
export type MaterialFormValues = z.infer<typeof materialFormSchema>;

export const buildAreaKey = (areaRangeType: number, dpiType: number) => `${areaRangeType}:${dpiType}`;
export const buildPieceKey = (variantType: number) => `${variantType}`;
export const buildCutKey = (type: number) => `${type}`;

export const createEmptyMaterialOption = (pricingTypeId = 0): MaterialOptionFormValues => ({
  name: '',
  pricingTypeId,
  isCut: false,
  processings: [],
  pricingByArea: {},
  pricingByPiece: {},
  pricingByCut: {},
});

export const getMaterialFormDefaultValues = (): MaterialFormValues => ({
  name: '',
  description: '',
  options: [],
});

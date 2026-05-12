import { useEffect, useMemo } from 'react';
import {
  useWatch,
  type Control,
  type FieldArrayWithId,
  type FieldErrors,
  type UseFieldArrayAppend,
  type UseFieldArrayRemove,
  type UseFormRegister,
  type UseFormSetValue,
} from 'react-hook-form';
import {
  useMaterialProcessingSelectQuery,
} from '@/entities/material';
import type { PrintingSelectOption } from '@/entities/printing';
import {
  useProductQuery,
  useProductSelectQuery,
  type ProductMaterialLink,
  type ProductSelectOption,
} from '@/entities/product';
import type { UserSelectOption } from '@/entities/user';
import { fieldInputClass, fieldSelectClass, fieldTextareaClass } from '@/shared/ui';
import { createOrderItemDefaultValue, type OrderFormValues } from '../lib/orderFormSchema';

const PRINT_COLOR_CLASSES = [
  'border-fuchsia-300 bg-fuchsia-50 text-fuchsia-700 hover:bg-fuchsia-100',
  'border-sky-300 bg-sky-50 text-sky-700 hover:bg-sky-100',
  'border-emerald-300 bg-emerald-50 text-emerald-700 hover:bg-emerald-100',
  'border-amber-300 bg-amber-50 text-amber-700 hover:bg-amber-100',
  'border-violet-300 bg-violet-50 text-violet-700 hover:bg-violet-100',
  'border-rose-300 bg-rose-50 text-rose-700 hover:bg-rose-100',
  'border-cyan-300 bg-cyan-50 text-cyan-700 hover:bg-cyan-100',
  'border-lime-300 bg-lime-50 text-lime-700 hover:bg-lime-100',
];

const getPrintChipClass = (index: number) =>
  PRINT_COLOR_CLASSES[index % PRINT_COLOR_CLASSES.length];

type ItemField = FieldArrayWithId<OrderFormValues, 'items', 'fieldId'>;

interface OrderPrintItemsEditorProps {
  control: Control<OrderFormValues>;
  register: UseFormRegister<OrderFormValues>;
  setValue: UseFormSetValue<OrderFormValues>;
  errors: FieldErrors<OrderFormValues>;
  fields: ItemField[];
  append: UseFieldArrayAppend<OrderFormValues, 'items'>;
  remove: UseFieldArrayRemove;
  printOptions: PrintingSelectOption[];
  performerOptions: UserSelectOption[];
  dpiOptions: Array<{ id: number; label: string }>;
  variantOptions: Array<{ id: number; label: string }>;
  disabled?: boolean;
}

interface OrderPrintGroupProps {
  printId: string;
  printName: string;
  groupIndex: number;
  indices: number[];
  fields: ItemField[];
  control: Control<OrderFormValues>;
  register: UseFormRegister<OrderFormValues>;
  setValue: UseFormSetValue<OrderFormValues>;
  errors: FieldErrors<OrderFormValues>;
  append: UseFieldArrayAppend<OrderFormValues, 'items'>;
  remove: UseFieldArrayRemove;
  performerOptions: UserSelectOption[];
  dpiOptions: Array<{ id: number; label: string }>;
  variantOptions: Array<{ id: number; label: string }>;
  disabled?: boolean;
}

interface OrderPrintItemCardProps {
  index: number;
  fieldId: string;
  control: Control<OrderFormValues>;
  register: UseFormRegister<OrderFormValues>;
  setValue: UseFormSetValue<OrderFormValues>;
  errors: FieldErrors<OrderFormValues>;
  productOptions: ProductSelectOption[];
  performerOptions: UserSelectOption[];
  dpiOptions: Array<{ id: number; label: string }>;
  variantOptions: Array<{ id: number; label: string }>;
  onRemove: () => void;
  disabled?: boolean;
}

const buildMaterialOptions = (productMaterials: ProductMaterialLink[]) => {
  const unique = new Map<number, { id: number; name: string }>();

  productMaterials.forEach((material) => {
    if (!unique.has(material.material_id)) {
      unique.set(material.material_id, {
        id: material.material_id,
        name: material.material_name || `Материал #${material.material_id}`,
      });
    }
  });

  return Array.from(unique.values());
};

const buildOptionOptions = (
  productMaterials: ProductMaterialLink[],
  materialId: number,
) => {
  return productMaterials
    .filter((material) => material.material_id === materialId)
    .map((material) => ({
      id: material.material_option_id,
      name: material.material_option_name || `Опция #${material.material_option_id}`,
    }));
};

const OrderPrintItemCard = ({
  index,
  fieldId,
  control,
  register,
  setValue,
  errors,
  productOptions,
  performerOptions,
  dpiOptions,
  variantOptions,
  onRemove,
  disabled = false,
}: OrderPrintItemCardProps) => {
  const productIdPath = useMemo(() => `items.${index}.productId` as const, [index]);
  const materialIdPath = useMemo(() => `items.${index}.materialId` as const, [index]);
  const optionIdPath = useMemo(() => `items.${index}.optionId` as const, [index]);
  const processingsPath = useMemo(() => `items.${index}.processings` as const, [index]);

  const currentProductIdRaw = useWatch({ control, name: productIdPath }) ?? '';
  const currentMaterialIdRaw = useWatch({ control, name: materialIdPath }) ?? '';
  const currentOptionIdRaw = useWatch({ control, name: optionIdPath }) ?? '';
  const selectedProcessingsWatch = useWatch({ control, name: processingsPath });
  const selectedProcessings = useMemo(
    () => selectedProcessingsWatch ?? [],
    [selectedProcessingsWatch],
  );

  const currentProductId = Number(currentProductIdRaw);
  const currentMaterialId = Number(currentMaterialIdRaw);
  const currentOptionId = Number(currentOptionIdRaw);

  const { data: selectedProductResponse } = useProductQuery(currentProductId, {
    enabled: currentProductId > 0,
  });

  const productMaterials = useMemo(
    () => selectedProductResponse?.data?.materials ?? [],
    [selectedProductResponse?.data?.materials],
  );
  const availableMaterials = useMemo(
    () => buildMaterialOptions(productMaterials),
    [productMaterials],
  );
  const availableOptions = useMemo(
    () => buildOptionOptions(productMaterials, currentMaterialId),
    [currentMaterialId, productMaterials],
  );

  const { data: resolvedProcessingOptions = [] } = useMaterialProcessingSelectQuery(
    currentMaterialId,
    currentOptionId,
    { enabled: currentMaterialId > 0 && currentOptionId > 0 },
  );

  useEffect(() => {
    if (!currentProductId) {
      if (currentMaterialIdRaw) {
        setValue(materialIdPath, '', { shouldDirty: true, shouldValidate: true });
      }
      if (currentOptionIdRaw) {
        setValue(optionIdPath, '', { shouldDirty: true, shouldValidate: true });
      }
      if (selectedProcessings.length > 0) {
        setValue(processingsPath, [], { shouldDirty: true, shouldValidate: true });
      }
      return;
    }

    if (availableMaterials.length === 0) {
      if (currentMaterialIdRaw) {
        setValue(materialIdPath, '', { shouldDirty: true, shouldValidate: true });
      }
      if (currentOptionIdRaw) {
        setValue(optionIdPath, '', { shouldDirty: true, shouldValidate: true });
      }
      if (selectedProcessings.length > 0) {
        setValue(processingsPath, [], { shouldDirty: true, shouldValidate: true });
      }
      return;
    }

    const hasCurrentMaterial = availableMaterials.some((material) => String(material.id) === currentMaterialIdRaw);
    if (!hasCurrentMaterial) {
      setValue(materialIdPath, String(availableMaterials[0].id), { shouldDirty: true, shouldValidate: true });
    }
  }, [
    availableMaterials,
    currentMaterialIdRaw,
    currentOptionIdRaw,
    currentProductId,
    materialIdPath,
    optionIdPath,
    processingsPath,
    selectedProcessings.length,
    setValue,
  ]);

  useEffect(() => {
    if (!currentMaterialId) {
      if (currentOptionIdRaw) {
        setValue(optionIdPath, '', { shouldDirty: true, shouldValidate: true });
      }
      if (selectedProcessings.length > 0) {
        setValue(processingsPath, [], { shouldDirty: true, shouldValidate: true });
      }
      return;
    }

    if (availableOptions.length === 0) {
      if (currentOptionIdRaw) {
        setValue(optionIdPath, '', { shouldDirty: true, shouldValidate: true });
      }
      if (selectedProcessings.length > 0) {
        setValue(processingsPath, [], { shouldDirty: true, shouldValidate: true });
      }
      return;
    }

    const hasCurrentOption = availableOptions.some((option) => String(option.id) === currentOptionIdRaw);
    if (!hasCurrentOption) {
      setValue(optionIdPath, String(availableOptions[0].id), { shouldDirty: true, shouldValidate: true });
    }
  }, [
    availableOptions,
    currentMaterialId,
    currentOptionIdRaw,
    optionIdPath,
    processingsPath,
    selectedProcessings.length,
    setValue,
  ]);

  useEffect(() => {
    if (resolvedProcessingOptions.length === 0) {
      if (selectedProcessings.length > 0) {
        setValue(processingsPath, [], { shouldDirty: true, shouldValidate: true });
      }
      return;
    }

    const allowed = new Set(resolvedProcessingOptions.map((option) => String(option.id)));
    const next = selectedProcessings.filter((value) => allowed.has(value));

    if (next.length !== selectedProcessings.length) {
      setValue(processingsPath, next, { shouldDirty: true, shouldValidate: true });
    }
  }, [processingsPath, resolvedProcessingOptions, selectedProcessings, setValue]);

  const toggleProcessing = (processingId: number) => {
    const key = String(processingId);
    const next = selectedProcessings.includes(key)
      ? selectedProcessings.filter((value) => value !== key)
      : [...selectedProcessings, key];

    setValue(processingsPath, next, { shouldDirty: true, shouldValidate: true });
  };

  const itemErrors = errors.items?.[index];

  return (
    <div key={fieldId} className="rounded-xl border border-slate-200 bg-slate-50/60 p-4 space-y-4">
      <div className="flex items-center justify-between gap-3">
        <p className="text-sm font-semibold text-slate-700">Позиция #{index + 1}</p>
        <button
          type="button"
          onClick={onRemove}
          className="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-medium text-rose-600 transition-colors hover:bg-rose-50 cursor-pointer"
          disabled={disabled}
        >
          Удалить
        </button>
      </div>

      <div className="grid grid-cols-1 gap-3 xl:grid-cols-4">
        <label className="block">
          <span className="mb-1 block text-xs font-medium text-slate-600">Продукция</span>
          <select className={fieldSelectClass} {...register(`items.${index}.productId`)}>
            <option value="">Выберите продукцию</option>
            {productOptions.map((product) => (
              <option key={product.id} value={product.id}>
                {product.name}
              </option>
            ))}
          </select>
          {itemErrors?.productId ? <p className="mt-1 text-xs text-red-600">{itemErrors.productId.message}</p> : null}
        </label>

        <label className="block">
          <span className="mb-1 block text-xs font-medium text-slate-600">Материал</span>
          <select className={fieldSelectClass} {...register(`items.${index}.materialId`)}>
            <option value="">Выберите материал</option>
            {availableMaterials.map((material) => (
              <option key={material.id} value={material.id}>
                {material.name}
              </option>
            ))}
          </select>
          {itemErrors?.materialId ? <p className="mt-1 text-xs text-red-600">{itemErrors.materialId.message}</p> : null}
        </label>

        <label className="block">
          <span className="mb-1 block text-xs font-medium text-slate-600">Опция материала</span>
          <select className={fieldSelectClass} {...register(`items.${index}.optionId`)}>
            <option value="">Выберите опцию</option>
            {availableOptions.map((option) => (
              <option key={option.id} value={option.id}>
                {option.name}
              </option>
            ))}
          </select>
          {itemErrors?.optionId ? <p className="mt-1 text-xs text-red-600">{itemErrors.optionId.message}</p> : null}
        </label>

        <label className="block">
          <span className="mb-1 block text-xs font-medium text-slate-600">Исполнитель</span>
          <select className={fieldSelectClass} {...register(`items.${index}.performerId`)}>
            <option value="">Не выбран</option>
            {performerOptions.map((user) => (
              <option key={user.id} value={user.id}>
                {user.name}
              </option>
            ))}
          </select>
        </label>
      </div>

      <div className="grid grid-cols-1 gap-3 xl:grid-cols-6">
        <label className="block">
          <span className="mb-1 block text-xs font-medium text-slate-600">Ширина</span>
          <input className={fieldInputClass} {...register(`items.${index}.width`)} />
          {itemErrors?.width ? <p className="mt-1 text-xs text-red-600">{itemErrors.width.message}</p> : null}
        </label>

        <label className="block">
          <span className="mb-1 block text-xs font-medium text-slate-600">Высота</span>
          <input className={fieldInputClass} {...register(`items.${index}.height`)} />
          {itemErrors?.height ? <p className="mt-1 text-xs text-red-600">{itemErrors.height.message}</p> : null}
        </label>

        <label className="block">
          <span className="mb-1 block text-xs font-medium text-slate-600">Кол-во</span>
          <input className={fieldInputClass} {...register(`items.${index}.quantity`)} />
          {itemErrors?.quantity ? <p className="mt-1 text-xs text-red-600">{itemErrors.quantity.message}</p> : null}
        </label>

        <label className="block">
          <span className="mb-1 block text-xs font-medium text-slate-600">Цена</span>
          <input className={fieldInputClass} {...register(`items.${index}.price`)} />
          {itemErrors?.price ? <p className="mt-1 text-xs text-red-600">{itemErrors.price.message}</p> : null}
        </label>

        <label className="block">
          <span className="mb-1 block text-xs font-medium text-slate-600">DPI</span>
          <select className={fieldSelectClass} {...register(`items.${index}.dpiType`)}>
            <option value="">Выберите DPI</option>
            {dpiOptions.map((option) => (
              <option key={option.id} value={option.id}>
                {option.label}
              </option>
            ))}
          </select>
          {itemErrors?.dpiType ? <p className="mt-1 text-xs text-red-600">{itemErrors.dpiType.message}</p> : null}
        </label>

        <label className="block">
          <span className="mb-1 block text-xs font-medium text-slate-600">Вариант</span>
          <select className={fieldSelectClass} {...register(`items.${index}.variantType`)}>
            <option value="">Выберите вариант</option>
            {variantOptions.map((option) => (
              <option key={option.id} value={option.id}>
                {option.label}
              </option>
            ))}
          </select>
          {itemErrors?.variantType ? <p className="mt-1 text-xs text-red-600">{itemErrors.variantType.message}</p> : null}
        </label>
      </div>

      <div className="grid grid-cols-1 gap-4 xl:grid-cols-[minmax(0,1.15fr)_minmax(0,0.85fr)]">
        <label className="block">
          <span className="mb-1 block text-xs font-medium text-slate-600">Примечание</span>
          <textarea rows={4} className={fieldTextareaClass} {...register(`items.${index}.note`)} />
        </label>

        <div className="space-y-3">
          <div>
            <span className="mb-2 block text-xs font-medium text-slate-600">Обработки</span>
            <div className="flex flex-wrap gap-2">
              {resolvedProcessingOptions.length === 0 ? (
                <div className="rounded-lg border border-dashed border-slate-300 px-3 py-2 text-xs text-slate-500">
                  Сначала выберите продукцию, материал и опцию материала.
                </div>
              ) : (
                resolvedProcessingOptions.map((processing) => {
                  const selected = selectedProcessings.includes(String(processing.id));
                  return (
                    <button
                      key={processing.id}
                      type="button"
                      onClick={() => toggleProcessing(processing.id)}
                      className={`rounded-full border px-3 py-1.5 text-xs font-medium transition-colors cursor-pointer ${
                        selected
                          ? 'border-blue-600 bg-blue-600 text-white'
                          : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'
                      }`}
                    >
                      {processing.name}
                    </button>
                  );
                })
              )}
            </div>
          </div>

          <div className="flex flex-wrap gap-4 pt-1">
            <label className="flex items-center gap-2 text-sm text-slate-700">
              <input type="checkbox" className="h-4 w-4 rounded border-slate-300 text-blue-600" {...register(`items.${index}.printed`)} />
              Отпечатано
            </label>
            <label className="flex items-center gap-2 text-sm text-slate-700">
              <input type="checkbox" className="h-4 w-4 rounded border-slate-300 text-blue-600" {...register(`items.${index}.ready`)} />
              Готово
            </label>
          </div>
        </div>
      </div>
    </div>
  );
};

const OrderPrintGroup = ({
  printId,
  printName,
  groupIndex,
  indices,
  fields,
  control,
  register,
  setValue,
  errors,
  append,
  remove,
  performerOptions,
  dpiOptions,
  variantOptions,
  disabled = false,
}: OrderPrintGroupProps) => {
  const { data: productOptions = [] } = useProductSelectQuery(Number(printId), {
    enabled: Number(printId) > 0,
  });

  const addPrintGroup = () => {
    append(createOrderItemDefaultValue(Number(printId)));
  };

  return (
    <div className="rounded-2xl border border-slate-200 p-4 space-y-4">
      <div className="flex items-center justify-between gap-3">
        <div className="flex items-center gap-3">
          <span className={`inline-flex rounded-full border px-3 py-1.5 text-sm font-semibold ${getPrintChipClass(groupIndex)}`}>
            {printName}
          </span>
          <span className="text-sm text-slate-500">Позиции: {indices.length}</span>
        </div>

        <button
          type="button"
          onClick={addPrintGroup}
          className="rounded-lg border border-slate-200 px-3 py-1.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 cursor-pointer"
          disabled={disabled}
        >
          + Добавить позицию
        </button>
      </div>

      <div className="space-y-3">
        {indices.map((index) => (
          <OrderPrintItemCard
            key={fields[index]?.fieldId ?? `${printId}-${index}`}
            index={index}
            fieldId={fields[index]?.fieldId ?? `${printId}-${index}`}
            control={control}
            register={register}
            setValue={setValue}
            errors={errors}
            productOptions={productOptions}
            performerOptions={performerOptions}
            dpiOptions={dpiOptions}
            variantOptions={variantOptions}
            onRemove={() => remove(index)}
            disabled={disabled}
          />
        ))}
      </div>
    </div>
  );
};

export const OrderPrintItemsEditor = ({
  control,
  register,
  setValue,
  errors,
  fields,
  append,
  remove,
  printOptions,
  performerOptions,
  dpiOptions,
  variantOptions,
  disabled = false,
}: OrderPrintItemsEditorProps) => {
  const watchedItems = useWatch({ control, name: 'items' });
  const normalizedItems = useMemo(() => watchedItems ?? [], [watchedItems]);

  const groupedItems = useMemo(() => {
    const groups = new Map<string, number[]>();

    normalizedItems.forEach((item, index) => {
      if (!item?.printId) return;

      const bucket = groups.get(item.printId) ?? [];
      bucket.push(index);
      groups.set(item.printId, bucket);
    });

    return groups;
  }, [normalizedItems]);

  const activePrintIds = useMemo(() => new Set(groupedItems.keys()), [groupedItems]);

  const availablePrints = useMemo(
    () => printOptions.filter((option) => !activePrintIds.has(String(option.id))),
    [activePrintIds, printOptions],
  );

  const addPrintGroup = (printId: number) => {
    append(createOrderItemDefaultValue(printId));
  };

  return (
    <div className="rounded-2xl border border-slate-200 bg-white p-4 space-y-4">
      <div>
        <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">Характеристики заказа</p>
        <p className="mt-1 text-sm text-slate-500">Выберите тип печати, затем заполните позиции именно для него.</p>
      </div>

      <div className="space-y-2">
        <p className="text-xs font-medium uppercase tracking-wide text-slate-400">Доступные типы печати</p>
        <div className="flex flex-wrap gap-2">
          {availablePrints.length === 0 ? (
            <div className="rounded-xl border border-dashed border-slate-300 px-4 py-3 text-sm text-slate-500">
              Все доступные типы печати уже добавлены в заказ.
            </div>
          ) : (
            availablePrints.map((printOption, index) => (
              <button
                key={printOption.id}
                type="button"
                onClick={() => addPrintGroup(printOption.id)}
                className={`rounded-full border px-4 py-2 text-sm font-semibold transition-colors cursor-pointer ${getPrintChipClass(index)}`}
                disabled={disabled}
              >
                {printOption.name}
              </button>
            ))
          )}
        </div>
      </div>

      {groupedItems.size > 0 ? (
        <div className="space-y-4">
          {Array.from(groupedItems.entries()).map(([printId, indices], groupIndex) => {
            const printOption = printOptions.find((option) => String(option.id) === printId);

            return (
              <OrderPrintGroup
                key={printId}
                printId={printId}
                printName={printOption?.name ?? `Печать #${printId}`}
                groupIndex={groupIndex}
                indices={indices}
                fields={fields}
                control={control}
                register={register}
                setValue={setValue}
                errors={errors}
                append={append}
                remove={remove}
                performerOptions={performerOptions}
                dpiOptions={dpiOptions}
                variantOptions={variantOptions}
                disabled={disabled}
              />
            );
          })}
        </div>
      ) : (
        <div className="rounded-xl border border-dashed border-slate-300 px-4 py-6 text-center text-sm text-slate-500">
          Пока не выбрано ни одного типа печати.
        </div>
      )}
    </div>
  );
};

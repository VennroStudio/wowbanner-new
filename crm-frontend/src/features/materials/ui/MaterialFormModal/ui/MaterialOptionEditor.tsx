import { useMemo, useState } from 'react';
import { useWatch, type Control, type FieldErrors, type UseFormRegister, type UseFormSetValue } from 'react-hook-form';
import type { MaterialEnumRef } from '@/entities/material';
import type { ProcessingSelectOption } from '@/entities/processing';
import { fieldInputClass, fieldSelectClass } from '@/shared/ui';
import type { MaterialFormValues } from '../lib/materialFormSchema';
import { buildAreaKey, buildCutKey, buildPieceKey } from '../lib/materialFormSchema';

interface MaterialOptionEditorProps {
  optionIndex: number;
  control: Control<MaterialFormValues>;
  register: UseFormRegister<MaterialFormValues>;
  setValue: UseFormSetValue<MaterialFormValues>;
  errors: FieldErrors<MaterialFormValues>;
  optionPricingTypes: MaterialEnumRef[];
  areaRangeTypes: MaterialEnumRef[];
  dpiTypes: MaterialEnumRef[];
  variantTypes: MaterialEnumRef[];
  pricingCutTypes: MaterialEnumRef[];
  processingOptions: ProcessingSelectOption[];
  disabled?: boolean;
}

const blockTitleClass = 'text-xs font-semibold text-slate-500 uppercase tracking-wide';
const compactInputClass = `${fieldInputClass} py-1.5 text-xs`;

const getErrorMessage = (value: unknown) => {
  if (!value || typeof value !== 'object' || !('message' in value)) return null;
  const message = (value as { message?: unknown }).message;
  return typeof message === 'string' ? message : null;
};

type OptionErrorBag = {
  name?: unknown;
  pricingTypeId?: unknown;
  processings?: {
    root?: unknown;
  };
  pricingByArea?: Record<string, { price?: unknown; cost?: unknown; printHours?: unknown }>;
  pricingByPiece?: Record<string, { price?: unknown; cost?: unknown; printHours?: unknown }>;
  pricingByCut?: Record<string, { price?: unknown }>;
};

export const MaterialOptionEditor = ({
  optionIndex,
  control,
  register,
  setValue,
  errors,
  optionPricingTypes,
  areaRangeTypes,
  dpiTypes,
  variantTypes,
  pricingCutTypes,
  processingOptions,
  disabled = false,
}: MaterialOptionEditorProps) => {
  const option = useWatch({
    control,
    name: `options.${optionIndex}`,
  });

  const optionErrors = (errors.options?.[optionIndex] ?? {}) as OptionErrorBag;
  const [selectedProcessingId, setSelectedProcessingId] = useState<number>(processingOptions[0]?.id ?? 0);

  const processingMap = useMemo(
    () => new Map(processingOptions.map((processing) => [processing.id, processing.name])),
    [processingOptions],
  );

  if (!option) return null;

  const pricingTypeId = option.pricingTypeId;
  const processings = option.processings ?? [];
  const pricingByArea = option.pricingByArea ?? {};
  const pricingByPiece = option.pricingByPiece ?? {};
  const pricingByCut = option.pricingByCut ?? {};
  const currentProcessingId =
    selectedProcessingId > 0 && processingOptions.some((item) => item.id === selectedProcessingId)
      ? selectedProcessingId
      : (processingOptions[0]?.id ?? 0);

  const updateAreaField = (
    areaRangeType: number,
    dpiType: number,
    field: 'price' | 'cost' | 'printHours',
    value: string,
  ) => {
    const key = buildAreaKey(areaRangeType, dpiType);
    const current = pricingByArea[key] ?? {
      dpiType,
      areaRangeType,
      price: '',
      cost: '',
      printHours: '',
    };

    setValue(`options.${optionIndex}.pricingByArea.${key}`, {
      ...current,
      [field]: value,
    }, { shouldDirty: true, shouldValidate: true });
  };

  const updatePieceField = (
    variantType: number,
    field: 'price' | 'cost' | 'printHours',
    value: string,
  ) => {
    const key = buildPieceKey(variantType);
    const current = pricingByPiece[key] ?? {
      variantType,
      price: '',
      cost: '',
      printHours: '',
    };

    setValue(`options.${optionIndex}.pricingByPiece.${key}`, {
      ...current,
      [field]: value,
    }, { shouldDirty: true, shouldValidate: true });
  };

  const updateCutField = (type: number, value: string) => {
    const key = buildCutKey(type);
    const current = pricingByCut[key] ?? {
      type,
      price: '',
    };

    setValue(`options.${optionIndex}.pricingByCut.${key}`, {
      ...current,
      price: value,
    }, { shouldDirty: true, shouldValidate: true });
  };

  const handlePricingTypeChange = (value: number) => {
    setValue(`options.${optionIndex}.pricingTypeId`, value, {
      shouldDirty: true,
      shouldValidate: true,
    });

    if (value === 1) {
      setValue(`options.${optionIndex}.pricingByPiece`, {}, { shouldDirty: true });
      return;
    }

    if (value === 2) {
      setValue(`options.${optionIndex}.pricingByArea`, {}, { shouldDirty: true });
    }
  };

  const handleCutToggle = (checked: boolean) => {
    setValue(`options.${optionIndex}.isCut`, checked, {
      shouldDirty: true,
      shouldValidate: true,
    });

    if (!checked) {
      setValue(`options.${optionIndex}.pricingByCut`, {}, { shouldDirty: true });
    }
  };

  const addProcessing = () => {
    if (!currentProcessingId) return;
    if (processings.some((item) => item.processingId === currentProcessingId)) return;

    setValue(
      `options.${optionIndex}.processings`,
      [...processings, { processingId: currentProcessingId }],
      { shouldDirty: true, shouldValidate: true },
    );
  };

  const removeProcessing = (processingId: number) => {
    setValue(
      `options.${optionIndex}.processings`,
      processings.filter((item) => item.processingId !== processingId),
      { shouldDirty: true, shouldValidate: true },
    );
  };

  return (
    <div className="space-y-6">
      <div>
        <label className="block text-xs font-medium text-slate-500 mb-1">Название опции *</label>
        <input
          {...register(`options.${optionIndex}.name`)}
          className={fieldInputClass}
          autoComplete="off"
          disabled={disabled}
        />
        {getErrorMessage(optionErrors.name) && (
          <p className="mt-1 text-xs text-red-600">{getErrorMessage(optionErrors.name)}</p>
        )}
      </div>

      <section className="space-y-3 border-t border-slate-100 pt-4">
        <div className="flex items-center justify-between gap-3">
          <h3 className={blockTitleClass}>Обработки</h3>
        </div>

        <div className="rounded-2xl border border-slate-200 bg-slate-50/60 p-4 space-y-3">
          <div className="space-y-2">
            {processings.length === 0 ? (
              <p className="text-sm text-slate-500">Обработки пока не добавлены.</p>
            ) : (
              processings.map((processing) => (
                <div
                  key={`${processing.id ?? 'new'}-${processing.processingId}`}
                  className="flex items-center justify-between rounded-xl border border-slate-200 bg-white px-3 py-2"
                >
                  <span className="text-sm text-slate-700">
                    {processingMap.get(processing.processingId) ?? `#${processing.processingId}`}
                  </span>
                  <button
                    type="button"
                    onClick={() => removeProcessing(processing.processingId)}
                    className="text-xs font-medium text-red-600 hover:text-red-700"
                    disabled={disabled}
                  >
                    Удалить
                  </button>
                </div>
              ))
            )}
          </div>

          <div className="grid grid-cols-1 gap-3 md:grid-cols-[minmax(0,1fr)_auto]">
            <select
              value={currentProcessingId}
              onChange={(event) => setSelectedProcessingId(Number(event.target.value))}
              className={fieldSelectClass}
              disabled={disabled || processingOptions.length === 0}
            >
              {processingOptions.length === 0 ? (
                <option value={0}>Нет доступных обработок</option>
              ) : (
                processingOptions.map((processing) => (
                  <option key={processing.id} value={processing.id}>
                    {processing.name}
                  </option>
                ))
              )}
            </select>
            <button
              type="button"
              onClick={addProcessing}
              className="rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
              disabled={disabled || !currentProcessingId}
            >
              Добавить
            </button>
          </div>

          {getErrorMessage(optionErrors.processings?.root) && (
            <p className="text-xs text-red-600">{getErrorMessage(optionErrors.processings?.root)}</p>
          )}
        </div>
      </section>

      <section className="space-y-3 border-t border-slate-100 pt-4">
        <h3 className={blockTitleClass}>Блок цен</h3>

        <div className="space-y-4">
          <div>
            <label className="block text-xs font-medium text-slate-500 mb-1">Тип расчёта *</label>
            <select
              value={pricingTypeId}
              onChange={(event) => handlePricingTypeChange(Number(event.target.value))}
              className={fieldSelectClass}
              disabled={disabled}
            >
              <option value={0}>Выберите тип…</option>
              {optionPricingTypes.map((item) => (
                <option key={item.id} value={item.id}>
                  {item.label}
                </option>
              ))}
            </select>
            {getErrorMessage(optionErrors.pricingTypeId) && (
              <p className="mt-1 text-xs text-red-600">{getErrorMessage(optionErrors.pricingTypeId)}</p>
            )}
          </div>

          {pricingTypeId === 1 && (
            <div className="overflow-x-auto rounded-2xl border border-slate-200 bg-white">
              <table className="min-w-full border-separate border-spacing-0">
                <thead>
                  <tr className="bg-slate-900 text-white">
                    <th className="px-4 py-3 text-left text-sm font-semibold">Площадь / DPI</th>
                    {dpiTypes.map((dpi) => (
                      <th key={dpi.id} className="px-3 py-3 text-left text-sm font-semibold">
                        {dpi.label}
                      </th>
                    ))}
                  </tr>
                </thead>
                <tbody>
                  {areaRangeTypes.map((areaRange) => (
                    <tr key={areaRange.id} className="align-top odd:bg-slate-50/60">
                      <td className="whitespace-nowrap border-t border-slate-200 px-4 py-3 text-sm font-medium text-slate-800">
                        {areaRange.label}
                      </td>
                      {dpiTypes.map((dpi) => {
                        const key = buildAreaKey(areaRange.id, dpi.id);
                        const cell = pricingByArea[key] ?? {
                          dpiType: dpi.id,
                          areaRangeType: areaRange.id,
                          price: '',
                          cost: '',
                          printHours: '',
                        };
                        const cellErrors = optionErrors.pricingByArea?.[key] ?? {};

                        return (
                          <td key={key} className="border-t border-slate-200 px-3 py-3">
                            <div className="space-y-2 rounded-xl border border-slate-200 bg-white p-3">
                              <div>
                                <input
                                  value={cell.price}
                                  onChange={(event) =>
                                    updateAreaField(areaRange.id, dpi.id, 'price', event.target.value)
                                  }
                                  className={compactInputClass}
                                  placeholder="Цена"
                                  inputMode="decimal"
                                  disabled={disabled}
                                />
                                {getErrorMessage(cellErrors.price) && (
                                  <p className="mt-1 text-[11px] text-red-600">{getErrorMessage(cellErrors.price)}</p>
                                )}
                              </div>
                              <div>
                                <input
                                  value={cell.cost}
                                  onChange={(event) =>
                                    updateAreaField(areaRange.id, dpi.id, 'cost', event.target.value)
                                  }
                                  className={compactInputClass}
                                  placeholder="Себест."
                                  inputMode="decimal"
                                  disabled={disabled}
                                />
                                {getErrorMessage(cellErrors.cost) && (
                                  <p className="mt-1 text-[11px] text-red-600">{getErrorMessage(cellErrors.cost)}</p>
                                )}
                              </div>
                              <div>
                                <input
                                  value={cell.printHours}
                                  onChange={(event) =>
                                    updateAreaField(areaRange.id, dpi.id, 'printHours', event.target.value)
                                  }
                                  className={compactInputClass}
                                  placeholder="Норма час"
                                  inputMode="decimal"
                                  disabled={disabled}
                                />
                                {getErrorMessage(cellErrors.printHours) && (
                                  <p className="mt-1 text-[11px] text-red-600">
                                    {getErrorMessage(cellErrors.printHours)}
                                  </p>
                                )}
                              </div>
                            </div>
                          </td>
                        );
                      })}
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}

          {pricingTypeId === 2 && (
            <div className="grid grid-cols-1 gap-4 xl:grid-cols-3">
              {variantTypes.map((variant) => {
                const key = buildPieceKey(variant.id);
                const item = pricingByPiece[key] ?? {
                  variantType: variant.id,
                  price: '',
                  cost: '',
                  printHours: '',
                };
                const itemErrors = optionErrors.pricingByPiece?.[key] ?? {};

                return (
                  <div key={variant.id} className="rounded-2xl border border-slate-200 bg-white p-4 space-y-3">
                    <div className="rounded-xl bg-slate-900 px-3 py-2 text-sm font-semibold text-white">
                      {variant.label}
                    </div>
                    <div>
                      <label className="block text-xs font-medium text-slate-500 mb-1">Цена</label>
                      <input
                        value={item.price}
                        onChange={(event) => updatePieceField(variant.id, 'price', event.target.value)}
                        className={fieldInputClass}
                        placeholder="0.00"
                        inputMode="decimal"
                        disabled={disabled}
                      />
                      {getErrorMessage(itemErrors.price) && (
                        <p className="mt-1 text-xs text-red-600">{getErrorMessage(itemErrors.price)}</p>
                      )}
                    </div>
                    <div>
                      <label className="block text-xs font-medium text-slate-500 mb-1">Себестоимость</label>
                      <input
                        value={item.cost}
                        onChange={(event) => updatePieceField(variant.id, 'cost', event.target.value)}
                        className={fieldInputClass}
                        placeholder="0.00"
                        inputMode="decimal"
                        disabled={disabled}
                      />
                      {getErrorMessage(itemErrors.cost) && (
                        <p className="mt-1 text-xs text-red-600">{getErrorMessage(itemErrors.cost)}</p>
                      )}
                    </div>
                    <div>
                      <label className="block text-xs font-medium text-slate-500 mb-1">Норма час</label>
                      <input
                        value={item.printHours}
                        onChange={(event) => updatePieceField(variant.id, 'printHours', event.target.value)}
                        className={fieldInputClass}
                        placeholder="0.00"
                        inputMode="decimal"
                        disabled={disabled}
                      />
                      {getErrorMessage(itemErrors.printHours) && (
                        <p className="mt-1 text-xs text-red-600">{getErrorMessage(itemErrors.printHours)}</p>
                      )}
                    </div>
                  </div>
                );
              })}
            </div>
          )}
        </div>
      </section>

      <section className="space-y-3 border-t border-slate-100 pt-4">
        <div className="flex items-center justify-between gap-3">
          <div>
            <h3 className={blockTitleClass}>Рез</h3>
            <p className="mt-1 text-sm text-slate-500">Включите, если для опции нужно хранить цены на рез.</p>
          </div>
          <label className="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
            <input
              type="checkbox"
              checked={option.isCut}
              onChange={(event) => handleCutToggle(event.target.checked)}
              className="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
              disabled={disabled}
            />
            Есть рез
          </label>
        </div>

        {option.isCut && (
          <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
            {pricingCutTypes.map((cutType) => {
              const key = buildCutKey(cutType.id);
              const row = pricingByCut[key] ?? {
                type: cutType.id,
                price: '',
              };
              const rowErrors = optionErrors.pricingByCut?.[key] ?? {};

              return (
                <div key={cutType.id} className="rounded-2xl border border-slate-200 bg-white p-4">
                  <label className="block text-xs font-medium text-slate-500 mb-1">{cutType.label}</label>
                  <input
                    value={row.price}
                    onChange={(event) => updateCutField(cutType.id, event.target.value)}
                    className={fieldInputClass}
                    placeholder="0.00"
                    inputMode="decimal"
                    disabled={disabled}
                  />
                  {getErrorMessage(rowErrors.price) && (
                    <p className="mt-1 text-xs text-red-600">{getErrorMessage(rowErrors.price)}</p>
                  )}
                </div>
              );
            })}
          </div>
        )}
      </section>
    </div>
  );
};

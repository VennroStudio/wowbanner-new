import { useEffect, useMemo, useState } from 'react';
import type { MaterialOptionSelectOption, MaterialSelectOption } from '@/entities/material';
import { useMaterialOptionSelectQuery } from '@/entities/material';
import type { ProductMaterialFormValue } from '../lib/productFormSchema';
import { fieldSelectClass } from '@/shared/ui';

interface ProductMaterialsEditorProps {
  items: ProductMaterialFormValue[];
  materialOptionsById: Record<number, MaterialOptionSelectOption[]>;
  materialSelectOptions: MaterialSelectOption[];
  onChangeMaterialOptionsCache: (materialId: number, options: MaterialOptionSelectOption[]) => void;
  onAppend: (item: ProductMaterialFormValue) => void;
  onRemove: (index: number) => void;
  disabled?: boolean;
}

const areMaterialOptionsEqual = (
  left: MaterialOptionSelectOption[] | undefined,
  right: MaterialOptionSelectOption[],
) => {
  if (!left) return false;
  if (left.length !== right.length) return false;

  return left.every((item, index) => {
    const pair = right[index];
    return pair != null && pair.id === item.id && pair.name === item.name;
  });
};

export const ProductMaterialsEditor = ({
  items,
  materialOptionsById,
  materialSelectOptions,
  onChangeMaterialOptionsCache,
  onAppend,
  onRemove,
  disabled = false,
}: ProductMaterialsEditorProps) => {
  const [selectedMaterialId, setSelectedMaterialId] = useState<number>(materialSelectOptions[0]?.id ?? 0);
  const [selectedOptionId, setSelectedOptionId] = useState<number>(0);

  const currentMaterialId =
    selectedMaterialId > 0 && materialSelectOptions.some((item) => item.id === selectedMaterialId)
      ? selectedMaterialId
      : (materialSelectOptions[0]?.id ?? 0);

  const { data: currentMaterialOptions = [] } = useMaterialOptionSelectQuery(currentMaterialId, {
    enabled: currentMaterialId > 0,
  });

  useEffect(() => {
    if (currentMaterialId <= 0 || currentMaterialOptions.length === 0) {
      return;
    }

    if (!areMaterialOptionsEqual(materialOptionsById[currentMaterialId], currentMaterialOptions)) {
      onChangeMaterialOptionsCache(currentMaterialId, currentMaterialOptions);
    }
  }, [currentMaterialId, currentMaterialOptions, materialOptionsById, onChangeMaterialOptionsCache]);

  const optionList = useMemo(
    () => materialOptionsById[currentMaterialId] ?? currentMaterialOptions,
    [currentMaterialId, currentMaterialOptions, materialOptionsById],
  );
  const currentOptionId =
    selectedOptionId > 0 && optionList.some((item) => item.id === selectedOptionId)
      ? selectedOptionId
      : (optionList[0]?.id ?? 0);

  const materialNameMap = useMemo(
    () => new Map(materialSelectOptions.map((item) => [item.id, item.name])),
    [materialSelectOptions],
  );

  const optionNameMap = useMemo(() => {
    const entries = Object.values(materialOptionsById).flat().map((item) => [item.id, item.name] as const);
    return new Map(entries);
  }, [materialOptionsById]);

  const handleAdd = () => {
    if (!currentMaterialId || !currentOptionId) return;
    if (items.some((item) => item.materialOptionId === currentOptionId)) return;

    onAppend({
      materialId: currentMaterialId,
      materialName: materialNameMap.get(currentMaterialId),
      materialOptionId: currentOptionId,
      materialOptionName: optionNameMap.get(currentOptionId) ?? optionList.find((item) => item.id === currentOptionId)?.name,
    });
  };

  return (
    <div className="space-y-3">
      <div className="flex items-center justify-between">
        <h3 className="text-xs font-semibold text-slate-500 uppercase tracking-wide">Материалы</h3>
      </div>

      <div className="rounded-2xl border border-slate-200 bg-slate-50/60 p-4 space-y-3">
        <div className="space-y-2">
          {items.length === 0 ? (
            <p className="text-sm text-slate-500">Материалы пока не добавлены.</p>
          ) : (
            items.map((item, index) => (
              <div
                key={`${item.id ?? 'new'}-${item.materialOptionId}`}
                className="flex items-center justify-between rounded-xl border border-slate-200 bg-white px-3 py-2 gap-3"
              >
                <div className="min-w-0">
                  <p className="text-sm text-slate-700 truncate">
                    {item.materialName ?? `Материал #${item.materialId}`}
                  </p>
                  <p className="text-xs text-slate-400 truncate">
                    {item.materialOptionName ?? `Опция #${item.materialOptionId}`}
                  </p>
                </div>
                <button
                  type="button"
                  onClick={() => onRemove(index)}
                  className="text-xs font-medium text-red-600 hover:text-red-700"
                  disabled={disabled}
                >
                  Удалить
                </button>
              </div>
            ))
          )}
        </div>

        <div className="grid grid-cols-1 gap-3 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto]">
          <select
            value={currentMaterialId}
            onChange={(event) => setSelectedMaterialId(Number(event.target.value))}
            className={fieldSelectClass}
            disabled={disabled || materialSelectOptions.length === 0}
          >
            {materialSelectOptions.length === 0 ? (
              <option value={0}>Нет доступных материалов</option>
            ) : (
              materialSelectOptions.map((item) => (
                <option key={item.id} value={item.id}>
                  {item.name}
                </option>
              ))
            )}
          </select>

          <select
            value={currentOptionId}
            onChange={(event) => setSelectedOptionId(Number(event.target.value))}
            className={fieldSelectClass}
            disabled={disabled || optionList.length === 0}
          >
            {optionList.length === 0 ? (
              <option value={0}>Сначала выберите материал</option>
            ) : (
              optionList.map((item) => (
                <option key={item.id} value={item.id}>
                  {item.name}
                </option>
              ))
            )}
          </select>

          <button
            type="button"
            onClick={handleAdd}
            className="rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
            disabled={disabled || !currentMaterialId || !currentOptionId}
          >
            Добавить
          </button>
        </div>
      </div>
    </div>
  );
};

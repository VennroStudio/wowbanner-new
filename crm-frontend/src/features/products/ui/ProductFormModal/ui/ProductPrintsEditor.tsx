import { useMemo, useState } from 'react';
import type { PrintingSelectOption } from '@/entities/printing';
import { fieldSelectClass } from '@/shared/ui';
import type { ProductPrintFormValue } from '../lib/productFormSchema';

interface ProductPrintsEditorProps {
  items: ProductPrintFormValue[];
  printingOptions: PrintingSelectOption[];
  onAppend: (item: ProductPrintFormValue) => void;
  onRemove: (index: number) => void;
  disabled?: boolean;
}

export const ProductPrintsEditor = ({
  items,
  printingOptions,
  onAppend,
  onRemove,
  disabled = false,
}: ProductPrintsEditorProps) => {
  const [selectedPrintId, setSelectedPrintId] = useState<number>(printingOptions[0]?.id ?? 0);
  const currentPrintId =
    selectedPrintId > 0 && printingOptions.some((item) => item.id === selectedPrintId)
      ? selectedPrintId
      : (printingOptions[0]?.id ?? 0);

  const printNameMap = useMemo(
    () => new Map(printingOptions.map((item) => [item.id, item.name])),
    [printingOptions],
  );

  const handleAdd = () => {
    if (!currentPrintId) return;
    if (items.some((item) => item.printId === currentPrintId)) return;

    onAppend({
      printId: currentPrintId,
      printName: printNameMap.get(currentPrintId),
    });
  };

  return (
    <div className="space-y-3">
      <div className="flex items-center justify-between">
        <h3 className="text-xs font-semibold text-slate-500 uppercase tracking-wide">Типы печати</h3>
      </div>

      <div className="rounded-2xl border border-slate-200 bg-slate-50/60 p-4 space-y-3">
        <div className="space-y-2">
          {items.length === 0 ? (
            <p className="text-sm text-slate-500">Типы печати пока не добавлены.</p>
          ) : (
            items.map((item, index) => (
              <div
                key={`${item.id ?? 'new'}-${item.printId}`}
                className="flex items-center justify-between rounded-xl border border-slate-200 bg-white px-3 py-2 gap-3"
              >
                <p className="text-sm text-slate-700 truncate">
                  {item.printName ?? `Печать #${item.printId}`}
                </p>
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

        <div className="grid grid-cols-1 gap-3 lg:grid-cols-[minmax(0,1fr)_auto]">
          <select
            value={currentPrintId}
            onChange={(event) => setSelectedPrintId(Number(event.target.value))}
            className={fieldSelectClass}
            disabled={disabled || printingOptions.length === 0}
          >
            {printingOptions.length === 0 ? (
              <option value={0}>Нет доступных типов печати</option>
            ) : (
              printingOptions.map((item) => (
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
            disabled={disabled || !currentPrintId}
          >
            Добавить
          </button>
        </div>
      </div>
    </div>
  );
};
